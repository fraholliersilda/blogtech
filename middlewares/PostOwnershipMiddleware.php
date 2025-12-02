<?php
namespace Middlewares;

use core\Middleware;
use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class PostOwnershipMiddleware implements Middleware {
    public function handle() {
        // Verify user is authenticated
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            die("Unauthorized access.");
        }

        // Allow admins to bypass ownership checks
        if ($_SESSION['role'] == 1) {
            return;
        }

        $url = $_SERVER['REQUEST_URI'];
        
        // Extract post ID from URL for edit or delete operations
        $postId = null;
        if (preg_match('/edit\/(\d+)/', $url, $matches)) {
            $postId = $matches[1];
        } elseif (preg_match('/delete\/(\d+)/', $url, $matches)) {
            $postId = $matches[1];
        }
    
        if (!$postId) {
            http_response_code(400);
            die("Post ID not provided.");
        }
    
        // Verify user owns the post
        $queryBuilder = new QueryBuilder();
        $post = $queryBuilder->table('posts')
            ->select(['user_id'])
            ->where('id', '=', $postId)
            ->getOne();
    
        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            http_response_code(403);
            redirect("/blogtech/views/posts/blog");
        }
    }
}