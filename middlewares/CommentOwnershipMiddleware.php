<?php
namespace Middlewares;

use core\Middleware;
use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class CommentOwnershipMiddleware implements Middleware {
    public function handle() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            die("Unauthorized access.");
        }

        // If the user is an admin, they can access any comment
        if ($_SESSION['role'] == 1) {
            return; // Admins can bypass the ownership check
        }

        $url = $_SERVER['REQUEST_URI'];
        
        // Match comment edit pattern
        $commentId = null;
        if (preg_match('/comments\/edit\/(\d+)/', $url, $matches)) {
            $commentId = $matches[1];
        } elseif (preg_match('/comments\/delete\/(\d+)/', $url, $matches)) {
            $commentId = $matches[1];
            // For delete, we need to check both comment ownership and post ownership
            $this->handleDeletePermission($matches[1]);
            return;
        }
    
        if (!$commentId) {
            http_response_code(400);
            die("Comment ID not provided.");
        }
    
        $queryBuilder = new QueryBuilder();
        $comment = $queryBuilder->table('comments')
            ->select(['user_id'])
            ->where('id', '=', $commentId)
            ->getOne();
    
        if (!$comment || $comment['user_id'] !== $_SESSION['user_id']) {
            http_response_code(403);
            redirect("/blogtech/views/posts/blog");
        }
    }

    private function handleDeletePermission($commentId) {
        $queryBuilder = new QueryBuilder();
        
        // Get comment with post information
        $comment = $queryBuilder->table('comments')
            ->select(['comments.user_id as comment_user_id', 'posts.user_id as post_user_id'])
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->where('comments.id', '=', $commentId)
            ->getOne();

        if (!$comment) {
            http_response_code(404);
            die("Comment not found.");
        }

        // Allow deletion if user is comment author OR post author
        $canDelete = ($_SESSION['user_id'] == $comment['comment_user_id']) || 
                    ($_SESSION['user_id'] == $comment['post_user_id']);

        if (!$canDelete) {
            http_response_code(403);
            redirect("/blogtech/views/posts/blog");
        }
    }
}