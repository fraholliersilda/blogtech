<?php
namespace Controllers;
error_reporting(E_ALL);
ini_set('display_errors', 1);


use PDOException;
use Requests\RegistrationRequest;
use Requests\UpdateUsernameRequest;
use Exceptions\ValidationException;
use Models\User;
use Models\Roles;
use Database;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class AdminController extends BaseController
{

    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    // Verify that the current user has admin privileges (role = 1)
    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) {
            redirect("/blogtech/views/profile/profile");
        }
    }

    // Fetch users filtered by role and optional search term
    public function fetchUsersByRole($role, $search = null)
    {
        $roleId = (new Roles)->findBy('role', $role)['id'] ?? null;

        if ($roleId) {
            return (new User)->findByRoleWithSearch($roleId, $search);
        }

        return [];
    }

    // Display list of admin users with optional search filtering
    public function listAdmins()
    {
        $this->checkAdmin();
        $search = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : null;
        $search = empty($search) ? null : $search;
        $admins = $this->fetchUsersByRole('admin', $search);
        require BASE_PATH . '/views/admin/admins.php';
    }

    // Display list of regular users with optional search filtering
    public function listUsers()
    {
        $this->checkAdmin();
        $search = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : null;
        $search = empty($search) ? null : $search;
        $users = $this->fetchUsersByRole('user', $search);
        require BASE_PATH . '/views/admin/users.php';
    }

    // Handle POST requests for user management (update or delete)
    public function handleUserActions()
    {
        $this->checkLoggedIn();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];

            if ($action === 'update_user') {
                $errors = $this->updateUser();
                if ($errors) {
                    setErrors([$errors]);
                }
                setSuccessMessages(['User updated!']);
            } elseif ($action === 'delete') {
                $this->deleteUser();
                setSuccessMessages(['User deleted!']);
            }
            redirect("/blogtech/views/admin/users");
        } else {
            redirect("/blogtech/views/admin/users");
        }
    }

    // Update user information (username and email) with validation
    private function updateUser()
    {
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email'])
        ];

        try {
            UpdateUsernameRequest::validate($data);
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
            redirect("/blogtech/views/admin/users");
        }

        $id = intval($_POST['id']);
        (new User)->update($id, $data);

        return null;
    }

    // Delete a user with transaction safety
    private function deleteUser()
    {
        $id = intval($_POST['id']);

        try {
            Database::getConnection()->beginTransaction();

            (new User)->delete($id);

            Database::getConnection()->commit();
        } catch (PDOException $e) {
            // Rollback transaction on error
            if (Database::getConnection()->inTransaction()) {
                Database::getConnection()->rollBack();
            }
            setErrors(["Database error: " . $e->getMessage()]);
        }
    }

    // Handle admin login with credential validation
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password'])
            ];

            try {
                RegistrationRequest::validateLogin($data);
            } catch (ValidationException $e) {
                setErrors([$e->getMessage()]);
                redirect("/blogtech/views/admin/login");
            }

            $admin = $this->authenticateAdmin($data['email'], $data['password']);

            if ($admin) {
                // Set session variables for authenticated admin
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role'] = 1;
                redirect("/blogtech/views/profile/profile");
            } else {
                setErrors(["Invalid email or password"]);
                redirect("/blogtech/views/admin/login");
            }
        } else {
            redirect("/blogtech/views/registration/login");
        }
    }

    // Display the admin login page
    public function showAdminLogin()
    {
        include BASE_PATH . '/views/admin/admin_login.php';
        exit();
    }

    // Verify admin credentials and return user data if valid
    private function authenticateAdmin($email, $password)
    {
        // Hardcoded admin credentials as fallback
        $hardcodedEmail = 'admin@admin.com';
        $hardcodedPassword = 'adminadmin';
        
        // Check hardcoded credentials first
        if ($email === $hardcodedEmail && $password === $hardcodedPassword) {
            return [
                'id' => 0, // Special ID for hardcoded admin
                'email' => $hardcodedEmail,
                'username' => 'Super Admin',
                'role' => 1,
                'password' => '' // Not needed for return
            ];
        }

        // Then check database credentials
        $user = (new User)->findByEmail($email);

        // Check if user exists and has admin role (role = 1)
        if ($user && intval($user['role']) === 1) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

}