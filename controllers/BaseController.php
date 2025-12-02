<?php
namespace Controllers;

use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class BaseController
{
    public $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Verify user is logged in, redirect to login page if not
    public function checkLoggedIn()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect("/blogtech/views/registration/login");
        }
    }

    // Retrieve current logged-in user with their role information
    public function getLoggedInUser()
    {
        if (isset($_SESSION['user_id'])) {
            return (new QueryBuilder())
                ->table('users u')
                ->select(['u.*', 'r.role'])
                ->join('roles r', 'u.role', '=', 'r.id')
                ->where('u.id', '=', $_SESSION['user_id'])
                ->limit(1)
                ->get()[0] ?? null;
        }
        return null;
    }

}