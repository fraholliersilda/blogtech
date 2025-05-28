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
use Controllers\CommentsController;
use Middlewares\CommentOwnershipMiddleware;

require_once BASE_PATH . '/Database.php';

$profileController = new ProfileController($conn);
$registrationController = new RegistrationController($conn);
$postsController = new PostsController($conn);
$adminController = new AdminController($conn);
$commentsController = new CommentsController($conn);

$routes = [
    'GET' => [
        '/logout' => [
            fn() => $registrationController->logout(),
            [AuthMiddleware::class]
        ],
        '/views/admin/login' => [
            fn() => $adminController->showAdminLogin(),
            [GuestMiddleware::class]
        ],
        '/views/posts/new' => [
            fn() => $postsController->showNewPost(),
            [AuthMiddleware::class, IsUserMiddleware::class]
        ],
        '/views/posts/blog' => [
            fn() => $postsController->listPosts(),
            []
        ],
        '/views/posts/post/{id}' => [
            fn($id) => $postsController->viewPost($id),
            []
        ],
        '/views/posts/edit/{id}' => [
            fn($id) => $postsController->editPost($id),
            [AuthMiddleware::class, PostOwnershipMiddleware::class]
        ],
        '/views/admin/admins' => [
            fn() => $adminController->listAdmins(),
            [AuthMiddleware::class, IsAdminMiddleware::class]
        ],
        '/views/admin/users' => [
            fn() => $adminController->listUsers(),
            [AuthMiddleware::class, IsAdminMiddleware::class, AdminEditUserRoleMiddleware::class]
        ],
        '/views/profile/edit' => [
            fn() => $profileController->editProfile(),
            [AuthMiddleware::class]
        ],
        '/views/profile/profile' => [
            fn() => $profileController->viewProfile(),
            [AuthMiddleware::class]
        ],
        '/views/registration/login' => [
            fn() => $registrationController->showLogin(),
            [GuestMiddleware::class]
        ],
        '/views/registration/signup' => [
            fn() => $registrationController->showSignup(),
            [GuestMiddleware::class]
        ],
        '/views/registration/forgot_password' => [
            fn() => $registrationController->showForgotPassword(),
            [GuestMiddleware::class]
        ],
        '/views/registration/reset_password' => [
            fn() => $registrationController->showResetPassword(),
            [GuestMiddleware::class]
        ],
        '/comments/edit/{id}' => [
            fn($id) => $commentsController->editComment($id),
            [AuthMiddleware::class, CommentOwnershipMiddleware::class]
        ],
    ],
    'POST' => [
        '/views/registration/login' => [
            fn() => $registrationController->login(),
            []
        ],
        '/views/admin/login' => [
            fn() => $adminController->login(),
            []
        ],
        '/views/registration/signup' => [
            fn() => $registrationController->signup(),
            []
        ],
        '/views/posts/new' => [
            fn() => $postsController->createPost(),
            [AuthMiddleware::class, IsUserMiddleware::class]
        ],
        '/views/posts/edit/{id}' => [
            fn($id) => $postsController->editPost($id),
            [AuthMiddleware::class, PostOwnershipMiddleware::class]
        ],
        '/posts/delete/{id}' => [
            fn($id) => $postsController->deletePost($id),
            [AuthMiddleware::class, PostOwnershipMiddleware::class]
        ],
        '/views/admin/users' => [
            fn() => $adminController->handleUserActions(),
            [AuthMiddleware::class, IsAdminMiddleware::class, AdminEditUserRoleMiddleware::class]
        ],
        '/views/profile/edit' => [
            fn() => $profileController->updateProfile($_POST, $_FILES),
            [AuthMiddleware::class]
        ],
        '/views/registration/forgot_password' => [
            fn() => $registrationController->forgotPassword(),
            []
        ],
        '/views/registration/reset_password' => [
            fn() => $registrationController->resetPassword(),
            []
        ],
        '/comments/add/{id}' => [
            fn($id) => $commentsController->addComment($id),
            [AuthMiddleware::class]
        ],
        '/comments/edit/{id}' => [
            fn($id) => $commentsController->editComment($id),
            [AuthMiddleware::class, CommentOwnershipMiddleware::class]
        ],
        '/comments/delete/{id}' => [
            fn($id) => $commentsController->deleteComment($id),
            [AuthMiddleware::class, CommentOwnershipMiddleware::class]
        ],
        
    ]
];

$routeFound = false;
foreach ($routes[$method] as $route => $action) {
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    if (preg_match("#^$pattern$#", $path, $matches)) {
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

// Route not found - Redirect to 404 page
if (!$routeFound) {
    require BASE_PATH . '/views/404page.php';
    exit;
}
