<?php 
require_once 'successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <link rel="stylesheet" href="/blogtech/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
</head>
<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>

<?php displaySuccessMessages(); ?>
<div class="container">
    <h1 class="post-title"><?= htmlspecialchars($post['title']); ?></h1>
    <div class="post-author">
        <p><em>By: <?= htmlspecialchars($post['username'] ?? 'Unknown'); ?></em></p>
    </div>
    
    <div class="coverphoto post-image">
        <img src="<?= htmlspecialchars($post['cover_photo_path']); ?>" alt="Cover Photo" class="image-fluid">
    </div>
    <div class="post-description">
        <p><?= nl2br(htmlspecialchars($post['description'])); ?></p>
    </div>
    
    <!-- Navigation and Action buttons -->
    <div class="post-navigation" style="margin-bottom: 20px;">
        <a href="/blogtech/views/posts/blog" class="btn btn-primary" style="background-color: #16a085; margin-right: 10px;">Back to Blog</a>
        
        <!-- Edit and Delete buttons - only show if user owns the post or is admin -->
        <?php if (isset($_SESSION['user_id']) && 
                  (($_SESSION['user_id'] == $post['user_id']) || 
                   (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'))): ?>
            <a href="/blogtech/views/posts/edit/<?= $post['id']; ?>" class="btn btn-primary" style="margin-right: 10px;">
                <i class="fa fa-edit"></i> Edit Post
            </a>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                <i class="fa fa-trash"></i> Delete Post
            </button>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <?php if (isset($_SESSION['user_id']) && 
              (($_SESSION['user_id'] == $post['user_id']) || 
               (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'))): ?>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="deleteModalLabel">Confirm Delete</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this post? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <form method="POST" action="/blogtech/views/posts/delete/<?= $post['id']; ?>" style="display: inline;">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="containerposts">
        <h3>Latest Posts</h3>
        <div class="row">
            <?php if (!empty($latestPosts)) { ?>
                <?php foreach ($latestPosts as $latestPost) { ?>
                    <div class="col-md-5 post-card">
                        <div class="post-content">
                            <div class="post-image">
                                <img src="<?= htmlspecialchars($latestPost['cover_photo_path'] ?? 'default_path.jpg'); ?>" alt="Cover Photo" class="card-img-top">
                            </div>
                            <div class="posting-details">
                                <h5 class="card-title"><b><?= htmlspecialchars($latestPost['title']); ?></b></h5>
                                <p class="card-text"><em>By: <?= htmlspecialchars($latestPost['username']); ?></em></p>
                                <div class="card-actions" style="margin-bottom: 10px;">
                                    <a href="<?= BASE_URL ?>/views/posts/post/<?= $latestPost['id']; ?>" class="btn btn-secondary">Read More</a>
                                    
                                    <!-- Edit and Delete buttons for latest posts - only show if user owns the post or is admin -->
                                    <?php if (isset($_SESSION['user_id']) && 
                                              (($_SESSION['user_id'] == $latestPost['user_id']) || 
                                               (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'))): ?>
                                        <a href="/blogtech/views/posts/edit/<?= $latestPost['id']; ?>" class="btn btn-primary btn-sm" style="margin-left: 5px;">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $latestPost['id']; ?>" style="margin-left: 5px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Delete Confirmation Modal for each latest post -->
                            <?php if (isset($_SESSION['user_id']) && 
                                      (($_SESSION['user_id'] == $latestPost['user_id']) || 
                                       (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'))): ?>
                                <div class="modal fade" id="deleteModal<?= $latestPost['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $latestPost['id']; ?>">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="deleteModalLabel<?= $latestPost['id']; ?>">Confirm Delete</h4>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the post "<strong><?= htmlspecialchars($latestPost['title']); ?></strong>"? This action cannot be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                <form method="POST" action="/blogtech/views/posts/delete/<?= $latestPost['id']; ?>" style="display: inline;">
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No latest posts available.</p>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>