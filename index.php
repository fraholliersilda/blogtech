<?php

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Start session
session_start();

// Define base paths
define('BASE_PATH', __DIR__);
define('BASE_URL', '/blogtech');

// Get request details
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Parse and clean the path
$path = rtrim(parse_url(str_replace(BASE_URL, '', $request), PHP_URL_PATH), '/');

// Load helper functions
require_once 'redirect.php';

// Import required classes
use core\MiddlewareHandler;
use Middlewares\AuthMiddleware;
use Middlewares\IsAdminMiddleware;
use Middlewares\IsUserMiddleware;
use Middlewares\GuestMiddleware;
use Middlewares\AdminEditUserRoleMiddleware;
use Middlewares\PostOwnershipMiddleware;
use Middlewares\CommentOwnershipMiddleware;
use Exceptions\ValidationException;
use Controllers\ProfileController;
use Controllers\RegistrationController;
use Controllers\PostsController;
use Controllers\AdminController;
use Controllers\CommentsController;

// Initialize database connection
require_once BASE_PATH . '/Database.php';

// Initialize controllers
$profileController = new ProfileController($conn);
$registrationController = new RegistrationController($conn);
$postsController = new PostsController($conn);
$adminController = new AdminController($conn);
$commentsController = new CommentsController($conn);

// Define routes with callbacks and middlewares
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

// Route matching and execution
$routeFound = false;
foreach ($routes[$method] as $route => $action) {
    // Convert route pattern to regex (e.g., {id} becomes ([a-zA-Z0-9_-]+))
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    
    // Check if current path matches the route pattern
    if (preg_match("#^$pattern$#", $path, $matches)) {
        // Remove full match, keep only captured groups
        array_shift($matches);

        try {
            // Extract callback and middlewares
            $callback = $action[0];
            $middlewares = $action[1] ?? [];
            
            // Run middlewares
            MiddlewareHandler::run($middlewares);

            // Execute callback with route parameters
            if (is_callable($callback)) {
                $callback(...$matches);
            } else {
                require BASE_PATH . '/' . $callback;
            }

            $routeFound = true;
        } catch (ValidationException $exception) {
            // Handle validation errors
            setErrors([$exception->getMessage()]);
            redirect($_SERVER['HTTP_REFERER']);
        } catch (Exception $exception) {
            // Handle general errors
            setErrors(['Something went wrong. Please try again later.']);
            redirect("/blogtech/views/500.php");
        }
        exit;
    }
}

// Show 404 page if no route matched
if (!$routeFound) {
    require BASE_PATH . '/views/404page.php';
    exit;
}