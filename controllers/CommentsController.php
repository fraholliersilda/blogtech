<?php
namespace Controllers;

use PDOException;
use Exception;
use Models\Comment;
use Models\Post;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class CommentsController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function addComment($postId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
            try {
                $content = trim($_POST['content']);
                
                if (empty($content)) {
                    setErrors(['Comment cannot be empty.']);
                    redirect("/blogtech/views/posts/post/$postId");
                    return;
                }

                if (strlen($content) > 1000) {
                    setErrors(['Comment is too long. Maximum 1000 characters allowed.']);
                    redirect("/blogtech/views/posts/post/$postId");
                    return;
                }

                // Check if post exists
                $post = (new Post)->getPostById($postId);
                if (!$post) {
                    setErrors(['Post not found.']);
                    redirect('/blogtech/views/posts/blog');
                    return;
                }

                $commentData = [
                    'content' => $content,
                    'user_id' => $_SESSION['user_id'],
                    'post_id' => $postId
                ];

                $result = (new Comment)->addComment($commentData);

                if ($result) {
                    setSuccessMessages(['Comment added successfully!']);
                } else {
                    setErrors(['Failed to add comment.']);
                }

                redirect("/blogtech/views/posts/post/$postId");

            } catch (Exception $e) {
                setErrors(['Error: ' . $e->getMessage()]);
                redirect("/blogtech/views/posts/post/$postId");
            }
        } else {
            setErrors(['Invalid request.']);
            redirect("/blogtech/views/posts/post/$postId");
        }
    }

    public function editComment($commentId)
    {
        try {
            $comment = (new Comment)->getCommentById($commentId);
            
            if (!$comment) {
                setErrors(['Comment not found.']);
                redirect('/blogtech/views/posts/blog');
                return;
            }

            // Check if user owns the comment or is admin
            if ($_SESSION['user_id'] != $comment['user_id'] && $_SESSION['role'] != 1) {
                setErrors(['You are not authorized to edit this comment.']);
                redirect("/blogtech/views/posts/post/{$comment['post_id']}");
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
                $content = trim($_POST['content']);
                
                if (empty($content)) {
                    setErrors(['Comment cannot be empty.']);
                    redirect("/blogtech/views/posts/post/{$comment['post_id']}");
                    return;
                }

                if (strlen($content) > 1000) {
                    setErrors(['Comment is too long. Maximum 1000 characters allowed.']);
                    redirect("/blogtech/views/posts/post/{$comment['post_id']}");
                    return;
                }

                $result = (new Comment)->updateComment($commentId, $content);

                if ($result) {
                    setSuccessMessages(['Comment updated successfully!']);
                } else {
                    setErrors(['Failed to update comment.']);
                }

                redirect("/blogtech/views/posts/post/{$comment['post_id']}");
            }

            // If GET request, show edit form
            include BASE_PATH . '/views/comments/edit_comment.php';

        } catch (Exception $e) {
            setErrors(['Error: ' . $e->getMessage()]);
            redirect('/blogtech/views/posts/blog');
        }
    }

    public function deleteComment($commentId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $comment = (new Comment)->getCommentById($commentId);
                
                if (!$comment) {
                    setErrors(['Comment not found.']);
                    redirect('/blogtech/views/posts/blog');
                    return;
                }

                // Get the post to check if current user is the post author
                $post = (new Post)->getPostById($comment['post_id']);
                
                // Allow deletion if:
                // 1. User is the comment author
                // 2. User is the post author  
                // 3. User is admin
                $canDelete = ($_SESSION['user_id'] == $comment['user_id']) || 
                           ($_SESSION['user_id'] == $post['user_id']) || 
                           ($_SESSION['role'] == 1);

                if (!$canDelete) {
                    setErrors(['You are not authorized to delete this comment.']);
                    redirect("/blogtech/views/posts/post/{$comment['post_id']}");
                    return;
                }

                $result = (new Comment)->deleteComment($commentId);

                if ($result) {
                    setSuccessMessages(['Comment deleted successfully!']);
                } else {
                    setErrors(['Failed to delete comment.']);
                }

                redirect("/blogtech/views/posts/post/{$comment['post_id']}");

            } catch (Exception $e) {
                setErrors(['Error: ' . $e->getMessage()]);
                redirect('/blogtech/views/posts/blog');
            }
        } else {
            setErrors(['Invalid request method.']);
            redirect('/blogtech/views/posts/blog');
        }
    }
}