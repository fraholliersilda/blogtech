<?php 
namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class IsUserMiddleware implements Middleware{
    // Verify user has regular user role (role = 2), redirect if not
    public function handle(){
        if($_SESSION['role'] !== 2){
            redirect("/blogtech/views/posts/blog");
            exit();
        }
    }
}