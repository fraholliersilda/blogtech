<?php 

namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class AuthMiddleware implements Middleware{
    // Verify user is authenticated, redirect to login if not
    public function handle(){
        if(!isset($_SESSION['user_id'])){
            redirect('/blogtech/views/registration/login');
            exit();
        }
    }
}