<?php 
namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class IsAdminMiddleware implements Middleware{
    // Verify user has admin privileges (role = 1), redirect if not
    public function handle(){    
            if($_SESSION['role'] !== 1){
            redirect("/blogtech/views/profile/profile");
            exit();
        }
    }
}