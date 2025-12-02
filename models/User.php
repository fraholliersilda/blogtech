<?php
namespace Models;

use QueryBuilder\QueryBuilder;

class User extends Model
{
    public $table = 'users';

    public $fields = [
        'id',
        'username',
        'email',
        'password',
        'reset_token',
        'reset_token_expires_at'
    ];

    // Find user by email address
    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    // Find user by username
    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }

    // Retrieve all users with a specific role
    public function findByRole($roleId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['id', 'username', 'email'])
            ->where('role', '=', $roleId)
            ->get();
    }

    // Find users by role with optional search filtering on username or email
    public function findByRoleWithSearch($roleId, $search = null)
    {
        if (empty($search)) {
            return $this->findByRole($roleId);
        }

        $search = trim($search);

        $allUsers = $this->findByRole($roleId);

        // Filter users by search term (case-insensitive)
        $filteredUsers = array_filter($allUsers, function ($user) use ($search) {
            $searchLower = strtolower($search);
            $usernameLower = strtolower($user['username']);
            $emailLower = strtolower($user['email']);

            return strpos($usernameLower, $searchLower) !== false ||
                strpos($emailLower, $searchLower) !== false;
        });

        return array_values($filteredUsers);
    }
}