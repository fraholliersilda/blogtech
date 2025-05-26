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
    <style>
        /* Enhanced Like Section Styling */
        .like-section {
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .like-section:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .like-btn-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .like-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .like-btn.liked {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            transform: scale(1.05);
        }
        
        .like-btn:hover {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }
        
        .like-btn:active {
            transform: scale(0.95);
        }
        
        .like-icon {
            animation: heartbeat 1.5s ease-in-out infinite;
        }
        
        .liked .like-icon {
            animation: heartPulse 0.6s ease-in-out;
        }
        
        @keyframes heartbeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes heartPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        
        .likes-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #e74c3c;
        }
        
        .likes-count {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .show-likers {
            color: #3498db;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .show-likers:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .likers-list {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .liker-badge {
            display: inline-block;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 3px;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Enhanced Comment Section Styling */
        .comment-section {
            margin-top: 40px;
        }
        
        .comment-section h3 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        
        .add-comment-form {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border: none;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .add-comment-form h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .comment-textarea {
            border-radius: 8px;
            border: 2px solid #bdc3c7;
            transition: border-color 0.3s ease;
            resize: vertical;
        }
        
        .comment-textarea:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn-comment-submit {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-comment-submit:hover {
            background: linear-gradient(135deg, #229954 0%, #1e8449 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
        }
        
        .comment-item {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #3498db;
        }
        
        .comment-item:hover {
            box-shadow: 0 4px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .comment-meta {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
            font-size: 13px;
            color: #6c757d;
        }
        
        .comment-author {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .comment-content {
            font-size: 15px;
            line-height: 1.6;
            color: #2c3e50;
            margin: 15px 0;
        }
        
        .comment-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn-comment-action {
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            margin-right: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);
            border: none;
            color: white;
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, #d68910 0%, #b7750f 100%);
            transform: translateY(-1px);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            color: white;
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
            transform: translateY(-1px);
        }
        
        .comment-edit-form {
            display: none;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .no-comments {
            text-align: center;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            color: #6c757d;
            font-style: italic;
        }
        
        .no-comments i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .like-btn {
                font-size: 16px;
                padding: 10px 16px;
            }
            
            .comment-item {
                padding: 15px;
            }
            
            .add-comment-form {
                padding: 20px;
            }
        }
    </style>
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

    <!-- Enhanced Like Section -->
    <div class="like-section">
        <div class="like-btn-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="like-btn <?= $hasLiked ? 'liked' : ''; ?>" 
                        onclick="toggleLike(<?= $post['id']; ?>, this)"
                        data-post-id="<?= $post['id']; ?>">
                    <i class="fas fa-heart like-icon"></i>
                    <span><?= $hasLiked ? 'Liked' : 'Like' ?></span>
                </button>
            <?php else: ?>
                <div class="alert alert-info" style="margin: 0;">
                    <i class="fas fa-info-circle"></i> Please log in to like this post
                </div>
            <?php endif; ?>
        </div>
        
        <div class="likes-info">
            <div class="likes-count">
                <i class="fas fa-heart text-danger"></i>
                <span id="likes-count"><?= $post['likes_count'] ?? 0; ?></span> 
                <?= ($post['likes_count'] == 1) ? 'like' : 'likes'; ?>
            </div>
            
            <?php if ($post['likes_count'] > 0): ?>
                <div style="margin-top: 10px;">
                    <a href="#" class="show-likers" onclick="toggleLikers(); return false;">
                        <i class="fas fa-users"></i> Show who liked this post
                    </a>
                </div>
                <div class="likers-list" id="likers-list">
                    <div style="margin-bottom: 10px;">
                        <strong><i class="fas fa-heart text-danger"></i> Liked by:</strong>
                    </div>
                    <div id="likers-content">Loading...</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Comments Section -->
    <div class="comment-section">
        <h3><i class="fas fa-comments"></i> Comments (<?= count($comments); ?>)</h3>
        
        <!-- Add Comment Form (only for logged in users) -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="add-comment-form">
                <h4><i class="fas fa-plus-circle"></i> Add Your Comment</h4>
                <form method="POST" action="/blogtech/comments/add/<?= $post['id']; ?>">
                    <div class="form-group">
                        <textarea class="form-control comment-textarea" name="content" rows="4" 
                                  placeholder="Share your thoughts about this post..." 
                                  maxlength="1000" required></textarea>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Maximum 1000 characters
                        </small>
                    </div>
                    <button type="submit" class="btn btn-comment-submit">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-sign-in-alt"></i> 
                <strong>Want to join the conversation?</strong> 
                Please <a href="/blogtech/login">log in</a> to add your comment.
            </div>
        <?php endif; ?>

        <!-- Display Comments -->
        <div class="comments-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item" id="comment-<?= $comment['id']; ?>">
                        <div class="comment-meta">
                            <span class="comment-author">
                                <i class="fas fa-user-circle"></i> 
                                <?= htmlspecialchars($comment['username']); ?>
                            </span>
                            <span style="margin: 0 8px;">•</span>
                            <span>
                                <i class="fas fa-clock"></i> 
                                <?= date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                            </span>
                        </div>
                        
                        <div class="comment-content" id="comment-content-<?= $comment['id']; ?>">
                            <?= nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                        
                        <!-- Inline Edit Form (hidden by default) -->
                        <div class="comment-edit-form" id="edit-form-<?= $comment['id']; ?>">
                            <form onsubmit="updateComment(event, <?= $comment['id']; ?>)">
                                <div class="form-group">
                                    <textarea class="form-control" id="edit-content-<?= $comment['id']; ?>" 
                                              rows="3" maxlength="1000" required><?= htmlspecialchars($comment['content']); ?></textarea>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" 
                                            onclick="cancelEdit(<?= $comment['id']; ?>)">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Comment Actions -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="comment-actions">
                                <!-- Edit button - only for comment owner -->
                                <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
                                    <button class="btn btn-comment-action btn-edit" 
                                            onclick="showEditForm(<?= $comment['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                <?php endif; ?>
                                
                                <!-- Delete button - for comment owner, post owner, or admin -->
                                <?php if ($_SESSION['user_id'] == $comment['user_id'] || 
                                          $_SESSION['user_id'] == $post['user_id'] || 
                                          (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')): ?>
                                    <button class="btn btn-comment-action btn-delete" 
                                            onclick="deleteComment(<?= $comment['id']; ?>)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-comments">
                    <i class="fas fa-comment-slash"></i>
                    <h4>No comments yet</h4>
                    <p>Be the first to share your thoughts about this post!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal for Post -->
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
                        <form method="POST" action="/blogtech/posts/delete/<?= $post['id']; ?>" style="display: inline;">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Latest Posts Section -->
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
                                    
                                    <!-- Edit and Delete buttons for latest posts -->
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
                                <div class="modal fade" id="deleteModal<?= $latestPost['id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                                <h4 class="modal-title">Confirm Delete</h4>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the post "<strong><?= htmlspecialchars($latestPost['title']); ?></strong>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                <form method="POST" action="/blogtech/posts/delete/<?= $latestPost['id']; ?>" style="display: inline;">
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
<script>
    // Toggle like functionality with enhanced feedback
    function toggleLike(postId, button) {
        // Add loading state
        $(button).prop('disabled', true);
        const originalText = $(button).find('span').text();
        $(button).find('span').text('...');
        
        $.ajax({
            url: '/blogtech/likes/toggle/' + postId,
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Update like count with animation
                    $('#likes-count').fadeOut(200, function() {
                        $(this).text(response.likes_count).fadeIn(200);
                    });
                    
                    // Toggle button appearance with animation
                    if (response.has_liked) {
                        $(button).addClass('liked');
                        $(button).find('span').text('Liked');
                    } else {
                        $(button).removeClass('liked');
                        $(button).find('span').text('Like');
                    }
                    
                    // Reload page to update likers list
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Error: ' + response.message);
                    $(button).find('span').text(originalText);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $(button).find('span').text(originalText);
            },
            complete: function() {
                $(button).prop('disabled', false);
            }
        });
    }

    // Enhanced toggle likers functionality
    function toggleLikers() {
        const likersList = $('#likers-list');
        if (likersList.is(':visible')) {
            likersList.slideUp(300);
        } else {
            // Load likers if not already loaded
            if ($('#likers-content').text() === 'Loading...') {
                $.ajax({
                    url: '/blogtech/likes/<?= $post['id']; ?>',
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.likes.length > 0) {
                            const likersHtml = response.likes.map(like => 
                                '<span class="liker-badge">' + 
                                '<i class="fas fa-user"></i> ' + like.username + 
                                '</span>'
                            ).join('');
                            $('#likers-content').html(likersHtml);
                        } else {
                            $('#likers-content').html('<em class="text-muted">No likes yet.</em>');
                        }
                    },
                    error: function() {
                        $('#likers-content').html('<em class="text-danger">Error loading likers.</em>');
                    }
                });
            }
            likersList.slideDown(300);
        }
    }

    // Enhanced comment form interactions
    function showEditForm(commentId) {
        $('#comment-content-' + commentId).slideUp(200);
        $('#edit-form-' + commentId).slideDown(200);
    }

    function cancelEdit(commentId) {
        $('#edit-form-' + commentId).slideUp(200);
        $('#comment-content-' + commentId).slideDown(200);
    }

    // Enhanced update comment with better feedback
    function updateComment(event, commentId) {
        event.preventDefault();
        
        const content = $('#edit-content-' + commentId).val().trim();
        if (!content) {
            alert('Comment cannot be empty.');
            return;
        }

        // Show loading state
        const submitBtn = $(event.target).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: '/blogtech/comments/edit/' + commentId,
            type: 'POST',
            data: { content: content },
            success: function() {
                // Update the comment content display with animation
                $('#comment-content-' + commentId).fadeOut(200, function() {
                    $(this).html(nl2br(escapeHtml(content))).fadeIn(200);
                });
                cancelEdit(commentId);
                
                // Show success notification
                showNotification('Comment updated successfully!', 'success');
            },
            error: function() {
                showNotification('Error updating comment. Please try again.', 'error');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    }

    // Enhanced delete comment with animation
    function deleteComment(commentId) {
        if (confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
            $.ajax({
                url: '/blogtech/comments/delete/' + commentId,
                type: 'POST',
                success: function() {
                    $('#comment-' + commentId).slideUp(300, function() {
                        $(this).remove();
                        showNotification('Comment deleted successfully!', 'success');
                        // Update comment count after animation
                        setTimeout(() => location.reload(), 1000);
                    });
                },
                error: function() {
                    showNotification('Error deleting comment. Please try again.', 'error');
                }
            });
        }
    }

    // Helper function to show notifications
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="${iconClass}"></i> ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-hide after 4 seconds
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Helper functions
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function nl2br(str) {
        return str.replace(/\n/g, '<br>');
    }

    // Enhanced interactions on page load
    $(document).ready(function() {
        // Add smooth scrolling to comment section
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 500);
            }
        });

        // Add character counter to comment textarea
        $('.comment-textarea').on('input', function() {
            const maxLength = 1000;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            let counterElement = $(this).siblings('.char-counter');
            if (counterElement.length === 0) {
                counterElement = $('<small class="char-counter text-muted"></small>');
                $(this).after(counterElement);
            }
            
            const color = remaining < 100 ? 'text-warning' : remaining < 50 ? 'text-danger' : 'text-muted';
            counterElement.removeClass('text-muted text-warning text-danger').addClass(color);
            counterElement.text(`${remaining} characters remaining`);
        });

        // Add loading animation to forms
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        });

        // Add hover effects to comment items
        $('.comment-item').hover(
            function() {
                $(this).css('border-left-color', '#2980b9');
            },
            function() {
                $(this).css('border-left-color', '#3498db');
            }
        );
    });