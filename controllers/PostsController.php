<?php
namespace Controllers;

use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;

use Models\Post;
use Models\Media;
use Models\Comment;
use Models\Like;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class PostsController extends BaseController
{

    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function listPosts()
    {
        try {
            $posts = (new Post)->getAllPost();
            
            // Check if user is admin
            $is_admin = false;
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $is_admin = true;
            }
            
            include BASE_PATH . '/views/posts/blog_posts.php';
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    }
public function viewPost($postId)
{
    try {
        $post = (new Post)->getPostById($postId);
        
        if ($post) {
            // Fetch the latest posts except the current one
            $latestPosts = (new Post)->getLatestPosts($postId);
            
            // Fetch comments for this post
            $comments = (new Comment)->getCommentsByPostId($postId);
            
            // Check if current user has liked this post (if logged in)
            $hasLiked = false;
            if (isset($_SESSION['user_id'])) {
                $hasLiked = (new Like)->hasUserLikedPost($_SESSION['user_id'], $postId);
            }
            
            include BASE_PATH . '/views/posts/post.php';
        } else {
            setErrors(["Post not found."]);
            redirect('/blogtech/views/posts/blog');
        }
    } catch (PDOException $e) {
        setErrors(["Error: " . $e->getMessage()]);
        redirect('/blogtech/views/posts/blog');
    }
}
    public function editPost($postId)
    {
        try {
            $post = (new Post)->getPostById($postId);

            if (!$post) {
                setErrors(["Post not found"]);
                redirect('/blogtech/views/posts/blog');
                return;
            }

            $coverPhoto = (new Media)->getCoverPhotoByPostId($postId);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
                try {
                    PostsRequest::validate($_POST, true);

                    (new Post)->updatePost($postId, [
                        'title' => $_POST['title'],
                        'description' => $_POST['description']
                    ]);
                    
                    if (!empty($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                        if ($coverPhoto) {
                            $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                            if (file_exists($deleteFile)) {
                                unlink($deleteFile);
                            }

                            (new Media)->deleteMediaById($coverPhoto['id']);
                        }

                        (new Media)->saveCoverPhoto($_FILES['cover_photo'], $postId);
                    }
                    
                    setSuccessMessages(['Post updated!']);
                    redirect("/blogtech/views/posts/post/$postId");
                } catch (ValidationException $e) {
                    setErrors([$e->getMessage()]);
                }
            }

            include BASE_PATH . '/views/posts/edit_post.php';
        } catch (PDOException $e) {
            setErrors(["Database Error: " . $e->getMessage()]);
            redirect("/blogtech/views/posts/blog");
        }
    }

    public function createPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
            try {
                PostsRequest::validate($_POST);

                $postId = (new Post)->createPost([
                    'title' => $_POST['title'],
                    'description' => $_POST['description']
                ]);

                if ($postId) {
                    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === 0) {
                        (new Media)->saveCoverPhoto($_FILES['cover_photo'], $postId);
                    }

                    setSuccessMessages(['Post created!']);
                    redirect('/blogtech/views/posts/blog');
                } else {
                    setErrors(['Failed to create post.']);
                }
            } catch (Exception $e) {
                setErrors(["Error: " . $e->getMessage()]);
            }
        }

        include BASE_PATH . '/views/posts/new_post.php';
    }

    public function showNewPost()
    {
        include BASE_PATH . '/views/posts/new_post.php';
        exit();
    }

    public function deletePost($postId)
    {
        error_log("DeletePost called with ID: " . $postId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate post ID
                if (!$postId || !is_numeric($postId)) {
                    setErrors(["Invalid post ID."]);
                    redirect('/blogtech/views/posts/blog');
                    return;
                }

                $post = (new Post)->getPostById($postId);

                if (!$post) {
                    setErrors(["Post not found."]);
                    redirect('/blogtech/views/posts/blog');
                    return;
                }

                error_log("Post found, proceeding with deletion");

                // Delete associated media first
                $coverPhoto = (new Media)->getCoverPhotoByPostId($postId);
                if ($coverPhoto) {
                    error_log("Deleting cover photo: " . $coverPhoto['path']);
                    (new Media)->deleteMediaById($coverPhoto['id']);
                    $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                    if (file_exists($deleteFile)) {
                        unlink($deleteFile);
                    }
                }

                // Delete the post
                $result = (new Post)->deletePost($postId);
                error_log("Delete result: " . ($result ? 'success' : 'failed'));

                setSuccessMessages(['Post deleted successfully!']);
                redirect('/blogtech/views/posts/blog');
            } catch (Exception $e) {
                error_log("Delete error: " . $e->getMessage());
                setErrors(["Error: " . $e->getMessage()]);
                redirect('/blogtech/views/posts/blog');
            }
        } else {
            setErrors(["Invalid request method."]);
            redirect('/blogtech/views/posts/blog');
        }
    }
}