<?php
namespace Controllers;

use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;

use Models\Post;
use Models\Media;
use Models\Comment;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class PostsController extends BaseController
{

    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    // Display all blog posts with admin check
    public function listPosts()
    {
        try {
            $posts = (new Post)->getAllPost();

            // Check if current user has admin privileges
            $is_admin = false;
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $is_admin = true;
            }

            include BASE_PATH . '/views/posts/blog_posts.php';
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    }

    // Display a single post with its comments and related posts
    public function viewPost($postId)
    {
        try {
            $post = (new Post)->getPostById($postId);

            if ($post) {
                $latestPosts = (new Post)->getLatestPosts($postId);

                $comments = (new Comment)->getCommentsByPostId($postId);


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

    // Edit an existing post with optional cover photo replacement
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

                    // Update post title and description
                    (new Post)->updatePost($postId, [
                        'title' => $_POST['title'],
                        'description' => $_POST['description']
                    ]);

                    // Handle cover photo replacement if new file uploaded
                    if (!empty($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                        if ($coverPhoto) {
                            // Delete old cover photo file and database record
                            $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                            if (file_exists($deleteFile)) {
                                unlink($deleteFile);
                            }

                            (new Media)->deleteMediaById($coverPhoto['id']);
                        }

                        // Save new cover photo
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

    // Create a new post with optional cover photo
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
                    // Save cover photo if uploaded
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

    // Display the new post creation form
    public function showNewPost()
    {
        include BASE_PATH . '/views/posts/new_post.php';
        exit();
    }

    // Delete a post along with its cover photo and associated media
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

                // Delete cover photo if exists
                $coverPhoto = (new Media)->getCoverPhotoByPostId($postId);
                if ($coverPhoto) {
                    error_log("Deleting cover photo: " . $coverPhoto['path']);
                    (new Media)->deleteMediaById($coverPhoto['id']);
                    // Remove physical file from server
                    $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                    if (file_exists($deleteFile)) {
                        unlink($deleteFile);
                    }
                }

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