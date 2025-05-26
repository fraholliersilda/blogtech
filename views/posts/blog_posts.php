<?php 
require_once 'successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <link rel="stylesheet" href="/blogtech/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .like-btn {
            background: none;
            border: none;
            color: #ccc;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }
        .like-btn.liked {
            color: #e74c3c;
        }
        .like-btn:hover {
            color: #e74c3c;
        }
        .engagement-stats {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
        .engagement-stats i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
<?php displaySuccessMessages(); ?>
    <div class="container">
        <h1><b>BLOG POSTS</b></h1>
        <div class="row">
      <?php if (!empty($posts)) { ?>
    <?php foreach ($posts as $post) { ?>
        <div class="col-md-12 post-card">
            <div class="post-content">
                <div class="post-image">
                <img src="<?= htmlspecialchars($post['cover_photo_path'] ?? '/blogtech/images/default_cover.jpg'); ?>" alt="Cover Photo" class="card-img-top">
                </div>
                <div class="post-details">
                    <h5 class="card-title"><?= htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><em>By: <?= htmlspecialchars($post['username'] ?? 'Unknown'); ?></em></p>
                    <p class="card-text"><?= htmlspecialchars(substr($post['description'], 0, 300)); ?>...</p>
                    
                    <!-- Engagement Stats -->
                    <div class="engagement-stats">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php 
                            $hasLiked = (new \Models\Like())->hasUserLikedPost($_SESSION['user_id'], $post['id']);
                            ?>
                            <button class="like-btn <?= $hasLiked ? 'liked' : ''; ?>" 
                                    onclick="toggleLike(<?= $post['id']; ?>, this)"
                                    data-post-id="<?= $post['id']; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                        <?php else: ?>
                            <i class="fas fa-heart" style="color: #ccc;"></i>
                        <?php endif; ?>
                        <span id="likes-count-<?= $post['id']; ?>"><?= $post['likes_count'] ?? 0; ?></span> likes
                        
                        <span style="margin-left: 15px;">
                            <i class="fas fa-comment"></i>
                            <?= $post['comments_count'] ?? 0; ?> comments
                        </span>
                    </div>
                    
                    <div class="post-actions">
                        <a href="<?= BASE_URL ?>/views/posts/post/<?= $post['id']; ?>" class="btn btn-secondary">Read More</a>
                        <?php if ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'])) { ?>
                            <a href="/blogtech/views/posts/edit/<?php echo $post['id']; ?>" class="btn btn-primary">Edit</a>
                            <form action="<?= BASE_URL ?>/posts/delete/<?= $post['id'] ?>" method="post" style="display: inline;">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>No posts found.</p>
<?php } ?>

        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        function toggleLike(postId, button) {
            $.ajax({
                url: '/blogtech/likes/toggle/' + postId,
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        // Update like count
                        $('#likes-count-' + postId).text(response.likes_count);
                        
                        // Toggle button appearance
                        if (response.has_liked) {
                            $(button).addClass('liked');
                        } else {
                            $(button).removeClass('liked');
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    </script>
</body>

</html>