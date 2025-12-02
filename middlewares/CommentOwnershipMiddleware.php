<?php
namespace Middlewares;

use core\Middleware;
use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class CommentOwnershipMiddleware implements Middleware {
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
        
        // Extract comment ID from URL
        $commentId = null;
        if (preg_match('/comments\/edit\/(\d+)/', $url, $matches)) {
            $commentId = $matches[1];
        } elseif (preg_match('/comments\/delete\/(\d+)/', $url, $matches)) {
            $commentId = $matches[1];
            // Handle delete with special permission logic
            $this->handleDeletePermission($matches[1]);
            return;
        }
    
        if (!$commentId) {
            http_response_code(400);
            die("Comment ID not provided.");
        }
    
        // Verify user owns the comment for edit operations
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

    // Check if user can delete comment (comment owner or post owner)
    private function handleDeletePermission($commentId) {
        $queryBuilder = new QueryBuilder();

        // Fetch both comment owner and post owner
        $comment = $queryBuilder->table('comments')
            ->select(['comments.user_id as comment_user_id', 'posts.user_id as post_user_id'])
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->where('comments.id', '=', $commentId)
            ->getOne();

        if (!$comment) {
            http_response_code(404);
            die("Comment not found.");
        }

        // Allow deletion if user owns the comment or the post
        $canDelete = ($_SESSION['user_id'] == $comment['comment_user_id']) || 
                    ($_SESSION['user_id'] == $comment['post_user_id']);

        if (!$canDelete) {
            http_response_code(403);
            redirect("/blogtech/views/posts/blog");
        }
    }
}