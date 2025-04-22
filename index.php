<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
session_start();
define('BASE_PATH', __DIR__);
define('BASE_URL', '/blogtech');


$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];


$path = rtrim(parse_url(str_replace(BASE_URL, '', $request), PHP_URL_PATH), '/');

require_once 'redirect.php';

use core\MiddlewareHandler;
use Middlewares\AuthMiddleware;
use Middlewares\IsAdminMiddleware;
use Middlewares\IsUserMiddleware;
use Middlewares\GuestMiddleware;
use Middlewares\AdminEditUserRoleMiddleware;
use Middlewares\PostOwnershipMiddleware;
use Exceptions\ValidationException;
use Controllers\ProfileController;
use Controllers\RegistrationController;
use Controllers\PostsController;
use Controllers\AdminController;

require_once BASE_PATH . '/Database.php';


$profileController = new ProfileController($conn);
$registrationController = new RegistrationController($conn);
$postsController = new PostsController($conn);
$adminController = new AdminController($conn);

// if (strpos($path, '/views/registration/reset_password') === 0) {
//     error_log("Reset password path detected: " . $path);
//     $registrationController->showResetPassword();
//     exit;
// }

$routes = [
    'GET' => [
        '/logout' =>
            [
                fn() => $registrationController->logout(),
                [AuthMiddleware::class]
            ]
        ,
        '/views/admin/login' =>
            [
                fn() => $adminController->showAdminLogin(),
                [GuestMiddleware::class]
            ],
        '/views/posts/new' =>
            [
                fn() => $postsController->showNewPost(),
                [AuthMiddleware::class, IsUserMiddleware::class]
            ],
        '/views/posts/blog' => [fn() => $postsController->listPosts(), []],
        '/views/posts/post/{id}' => [fn($id) => $postsController->viewPost($id), []],
        '/views/posts/edit/{id}' =>
            [
                fn($id) => $postsController->editPost($id),
                [AuthMiddleware::class, PostOwnershipMiddleware::class, IsAdminMiddleware::class]
            ],
        '/views/admin/admins' =>
            [
                fn() => $adminController->listAdmins(),
                [AuthMiddleware::class, IsAdminMiddleware::class]
            ],
        '/views/admin/users' =>
            [
                fn() => $adminController->listUsers(),
                [AuthMiddleware::class, IsAdminMiddleware::class, AdminEditUserRoleMiddleware::class]
            ],
        '/views/profile/edit' =>
            [
                fn() => $profileController->editProfile(),
                [AuthMiddleware::class]
            ],
        '/views/profile/profile' =>
            [
                fn() => $profileController->viewProfile(),
                [AuthMiddleware::class]
            ],
        '/views/registration/login' => [fn() => $registrationController->showLogin(), [GuestMiddleware::class]],
        '/views/registration/signup' => [fn() => $registrationController->showSignup(), [GuestMiddleware::class]],
        '/views/registration/forgot_password' => [fn() => $registrationController->showForgotPassword(), [GuestMiddleware::class]],
        '/views/registration/reset_password' => [fn() => $registrationController->showResetPassword(), [GuestMiddleware::class]],
    ],
    'POST' => [
        '/views/registration/login' => [fn() => $registrationController->login(), []],
        '/views/admin/login' => [fn() => $adminController->login(), []],
        '/views/registration/signup' => [fn() => $registrationController->signup(), []],
        '/views/posts/new' =>
            [
                fn() => $postsController->createPost(),
                [AuthMiddleware::class, IsUserMiddleware::class]
            ],
        '/views/posts/edit/{id}' =>
            [
                fn() => $postsController->editPost($_POST['id']),
                [AuthMiddleware::class, PostOwnershipMiddleware::class]
            ],
        '/posts/delete/{id}' =>
            [
                fn($id) => $postsController->deletePost($id),
                [AuthMiddleware::class, IsAdminMiddleware::class, PostOwnershipMiddleware::class]
            ],
        '/views/admin/users' =>
            [
                fn() => $adminController->handleUserActions(),
                [AuthMiddleware::class, IsAdminMiddleware::class, AdminEditUserRoleMiddleware::class]
            ],
        '/views/profile/edit' =>
            [
                fn() => $profileController->updateProfile($_POST, $_FILES),
                [AuthMiddleware::class]
            ],
        '/views/registration/forgot_password' => [fn() => $registrationController->forgotPassword(), []],
        '/views/registration/reset_password' => [fn() => $registrationController->resetPassword(), []],
    ],
];



$routeFound = false;
foreach ($routes[$method] as $route => $action) {
    error_log("Checking route: $route against path: $path");
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    error_log("Pattern: $pattern");
    if (preg_match("#^$pattern$#", $path, $matches)) {
        error_log("Checking route: $route against path: $path");
        error_log("Route matched: $route");
        array_shift($matches);

        try {
            $callback = $action[0];
            $middlewares = $action[1] ?? [];
            MiddlewareHandler::run($middlewares);

            if (is_callable($callback)) {
                $callback(...$matches);
            } else {
                require BASE_PATH . '/' . $callback;
            }

            $routeFound = true;
        } catch (ValidationException $exception) {
            setErrors([$exception->getMessage()]);
            redirect($_SERVER['HTTP_REFERER']);
        } catch (Exception $exception) {
            setErrors(['Something went wrong. Please try again later.']);
            redirect("/blogtech/views/500.php");
        }
        exit;
    }
}

error_log("No route matched for path: $path");
// Route not found - Redirect to 404 page
if (!$routeFound) {
    require BASE_PATH . '/views/404page.php';
    exit;
}