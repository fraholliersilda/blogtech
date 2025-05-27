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

    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }

    public function findByRole($roleId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['id', 'username', 'email'])
            ->where('role', '=', $roleId)
            ->get();
    }

    public function findByRoleWithSearch($roleId, $search = null)
    {
        if (empty($search)) {
            return $this->findByRole($roleId);
        }

        $search = trim($search);
        
        // Get all users with the role first
        $allUsers = $this->findByRole($roleId);
        
        // Filter by search term (case-insensitive)
        $filteredUsers = array_filter($allUsers, function($user) use ($search) {
            $searchLower = strtolower($search);
            $usernameLower = strtolower($user['username']);
            $emailLower = strtolower($user['email']);
            
            return strpos($usernameLower, $searchLower) !== false || 
                   strpos($emailLower, $searchLower) !== false;
        });
        
        return array_values($filteredUsers); // Re-index array
    }
}