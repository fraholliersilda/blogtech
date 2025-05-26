<?php
namespace Controllers;

use PDOException;
use Exception;
use Models\Like;
use Models\Post;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class LikesController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function toggleLike($postId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Check if post exists
                $post = (new Post)->getPostById($postId);
                if (!$post) {
                    setErrors(['Post not found.']);
                    redirect('/blogtech/views/posts/blog');
                    return;
                }

                $userId = $_SESSION['user_id'];
                $likeModel = new Like();

                if ($likeModel->hasUserLikedPost($userId, $postId)) {
                    // Unlike the post
                    $result = $likeModel->removeLike($userId, $postId);
                    $message = $result ? 'Post unliked!' : 'Failed to unlike post.';
                } else {
                    // Like the post
                    $result = $likeModel->addLike($userId, $postId);
                    $message = $result ? 'Post liked!' : 'Failed to like post.';
                }

                if ($result) {
                    setSuccessMessages([$message]);
                } else {
                    setErrors([$message]);
                }

                // Check if request is AJAX
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    
                    // Return JSON response for AJAX
                    $likesCount = $likeModel->getLikesByPostId($postId);
                    $hasLiked = $likeModel->hasUserLikedPost($userId, $postId);
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $result,
                        'message' => $message,
                        'likes_count' => $likesCount['total_likes'],
                        'has_liked' => $hasLiked
                    ]);
                    exit;
                }

                // Redirect back to the post
                redirect("/blogtech/views/posts/post/$postId");

            } catch (Exception $e) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ]);
                    exit;
                }

                setErrors(['Error: ' . $e->getMessage()]);
                redirect("/blogtech/views/posts/post/$postId");
            }
        } else {
            setErrors(['Invalid request method.']);
            redirect('/blogtech/views/posts/blog');
        }
    }

    public function getLikes($postId)
    {
        try {
            $likes = (new Like)->getAllLikesForPost($postId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'likes' => $likes
            ]);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}