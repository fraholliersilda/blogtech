<?php

namespace Controllers;
ini_set("error_log", "php_error.txt");

use MailService;
use PDO;
use Exception;
use Requests\RegistrationRequest;
use Requests\PasswordResetRequest;
use Exceptions\ValidationException;
use Models\User;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class RegistrationController extends BaseController
{
    public function __construct(PDO $conn)
    {
        parent::__construct($conn);
    }

    // Display the login page
    public function showLogin()
    {
        include BASE_PATH . '/views/registration/login.php';
        exit();
    }

    // Handle user login with credential verification
    public function login()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect("/blogtech/views/registration/login");
        }

        $data = [
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
        ];

        try {
            RegistrationRequest::validateLogin($data);

            $user = (new User)->findByEmail($data['email']);

            if (!$user) {
                setErrors(["Invalid email or password."]);
                return redirect("/blogtech/views/registration/login");
            }

            // Prevent admin users from logging in through regular interface
            if ($user['role'] === 1) {
                setErrors(["Admin login not allowed through this interface."]);
                return redirect("/blogtech/views/registration/login");
            }

            // Verify password and set session variables
            if (password_verify($data['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                return redirect("/blogtech/views/profile/profile");
            } else {
                setErrors(["Invalid email or password."]);
            }
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
        }

        redirect("/blogtech/views/registration/login");
    }

    // Display the signup page
    public function showSignup()
    {
        include BASE_PATH . '/views/registration/signup.php';
        exit();
    }

    // Handle new user registration with validation
    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect("/blogtech/views/registration/signup");
        }

        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ];

        try {
            RegistrationRequest::validateSignup($data);

            // Check if email or username already exists
            $existingUser = (new User)->findByEmail($data['email']) ?? (new User)->findByUsername($data['username']);

            if ($existingUser) {
                $errors = [];

                if ($existingUser['email'] === $data['email']) {
                    $errors[] = "Email already exists.";
                }
                if ($existingUser['username'] === $data['username']) {
                    $errors[] = "Username already exists.";
                }

                setErrors($errors);
                return redirect("/blogtech/views/registration/signup");
            }

            // Hash password and set default role (2 = regular user)
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['role'] = 2;

            if ((new User)->create($data)) {
                setSuccessMessages(['User created! Please login to continue.']);
                return redirect("/blogtech/views/registration/login");
            }

            setErrors(["Error creating account."]);
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
        }

        redirect("/blogtech/views/registration/signup");
    }

    // Handle user logout and destroy session
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        setSuccessMessages(['User logged out!']);
        redirect("/blogtech/views/registration/login");
    }

    // Display the forgot password page
    public function showForgotPassword()
    {
        include BASE_PATH . '/views/registration/forgot_password.php';
        exit();
    }

    // Handle forgot password request and send reset email
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect("/blogtech/views/registration/forgot_password");
        }

        $email = trim($_POST['email']);

        try {
            $data = ['email' => $email];
            $request = new PasswordResetRequest();
            $request->validateEmail($data);

            $user = (new User)->findByEmail($email);

            if ($user) {
                // Generate unique reset token valid for 1 hour
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $this->conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);

                $resetUrl = "http://{$_SERVER['HTTP_HOST']}/blogtech/views/registration/reset_password?token=$token";
                $this->sendPasswordResetEmail($email, $resetUrl);
            }
            // Generic message to prevent email enumeration
            setSuccessMessages(['If your email is registered, a password reset link has been sent to your inbox.']);
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            setErrors(['An error occurred. Please try again later.']);
        }

        redirect("/blogtech/views/registration/forgot_password");
    }

    // Display the reset password page if token is valid
    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            setErrors(['Invalid token.']);
            return redirect("/blogtech/views/registration/login");
        }

        // Verify token exists and hasn't expired
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || strtotime($user['reset_token_expires_at']) < time()) {
            setErrors(['Token is invalid or has expired.']);
            return redirect("/blogtech/views/registration/login");
        }

        $_SESSION['reset_token'] = $token;

        include BASE_PATH . '/views/registration/reset_password.php';
        exit();
    }

    // Handle password reset with token validation
    public function resetPassword()
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect("/blogtech/views/registration/login");
        }

        $token = $_SESSION['reset_token'] ?? '';

        if (empty($token)) {
            setErrors(['Invalid token. Please try resetting your password again.']);
            return redirect("/blogtech/views/registration/forgot_password");
        }

        $data = [
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'token' => $token
        ];

        try {
            $request = new PasswordResetRequest();
            $request->validatePasswordReset($data);

            // Verify token is still valid
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE reset_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || strtotime($user['reset_token_expires_at']) < time()) {
                throw new ValidationException("Token is invalid or has expired.");
            }

            // Update password and clear reset token
            $updateStmt = $this->conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
            $updateStmt->execute([password_hash($data['password'], PASSWORD_DEFAULT), $user['id']]);


            unset($_SESSION['reset_token']);

            setSuccessMessages(['Your password has been reset successfully.']);
            return redirect("/blogtech/views/registration/login");
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
            return redirect("/blogtech/views/registration/reset_password?token=$token");
        } catch (Exception $e) {
            setErrors(['An error occurred. Please try again later.']);
            return redirect("/blogtech/views/registration/reset_password?token=$token");
        }
    }

    // Send password reset email with reset link
    private function sendPasswordResetEmail($email, $resetUrl)
    {
        $subject = "Password Reset Request";

        $message = "
        <html>
        <head><title>Password Reset</title></head>
        <body>
            <p>You have requested to reset your password.</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='$resetUrl'>Reset Password</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this password reset, please ignore this email.</p>
        </body>
        </html>";

        $plainTextMessage = "You have requested to reset your password. Please visit this link: $resetUrl. This link will expire in 1 hour. If you did not request this password reset, please ignore this email.";

        require_once BASE_PATH . '/MailService.php';
        $mailService = new MailService();
        return $mailService->send($email, $subject, $message, $plainTextMessage);
    }
}