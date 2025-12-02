<?php
// Include the success handler to display success messages to the user
require_once 'successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <!-- Custom stylesheet for the blog -->
    <link rel="stylesheet" href="../../css/styles.css">
    <!-- Bootstrap CSS for responsive layout and styling -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <?php 
    // Include the navigation bar
    include BASE_PATH . '/navbar/navbar.php'; 
    ?>
    
    <?php 
    // Display any success messages (like profile updated successfully)
    displaySuccessMessages(); 
    ?>
    
    <div class="container">
        <div class="row gutters-sm">
            <!-- Left column: User profile card with avatar and basic stats -->
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <!-- Display user's profile picture -->
                            <img src="<?php echo htmlspecialchars($profilePicture['path']); ?>" alt="User"
                                class="rounded-circle" width="150">
                            <div class="mt-3">
                                <!-- Display username -->
                                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                <!-- Display email address -->
                                <p class="text-muted font-size-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                                <!-- Show total number of blog posts created by this user -->
                                <p class="text-muted font-size-sm">
                                    <i class="fa fa-newspaper"></i>
                                    <?php echo count($userPosts); ?> Blog Posts
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right column: Detailed user information and edit button -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Username row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-0">Username</div>
                            </div>
                            <div class="col-sm-9">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </div>
                        </div>
                        <hr>
                        
                        <!-- Email row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-0">Email</div>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>
                        <hr>
                        
                        <!-- Edit profile button -->
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-info" target="_self" href="edit"
                                    style="background-color:#1abc9c; color: white; border-color: #1abc9c;">Edit
                                    Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section displaying all blog posts by this user -->
        <div class="row">
            <div class="col-md-12">
                <h1><b>MY BLOG POSTS</b></h1>
                
                <?php if (!empty($userPosts)) { ?>
                    <!-- Loop through each blog post and display it -->
                    <?php foreach ($userPosts as $post) { ?>
                        <div class="col-md-12 post-card">
                            <div class="post-content">
                                <!-- Display post cover image -->
                                <div class="post-image">
                                    <img src="<?= htmlspecialchars($post['cover_photo_path'] ?? '/blogtech/images/default_cover.jpg'); ?>"
                                        alt="Cover Photo" class="card-img-top">
                                </div>
                                
                                <!-- Display post details -->
                                <div class="post-details">
                                    <!-- Post title -->
                                    <h5 class="card-title"><?= htmlspecialchars($post['title']); ?></h5>
                                    <!-- Post author -->
                                    <p class="card-text"><em>By: <?= htmlspecialchars($post['username'] ?? 'Unknown'); ?></em>
                                    </p>
                                    <!-- Post description preview (first 300 characters) -->
                                    <p class="card-text"><?= htmlspecialchars(substr($post['description'], 0, 300)); ?>...</p>
                                    
                                    <!-- Button to view full post -->
                                    <a href="<?= BASE_URL ?>/views/posts/post/<?= $post['id']; ?>"
                                        class="btn btn-secondary">Read More</a>
                                    
                                    <!-- Button to edit this post -->
                                    <a href="/blogtech/views/posts/edit/<?php echo $post['id']; ?>"
                                        class="btn btn-primary">Edit</a>
                                    
                                    <!-- Form to delete this post -->
                                    <form action="<?= BASE_URL ?>/posts/delete/<?= $post['id'] ?>" method="post"
                                        style="display: inline;">
                                        <!-- Hidden field to simulate DELETE request -->
                                        <input type="hidden" name="_method" value="DELETE">
                                        <!-- Delete button with confirmation prompt -->
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <!-- Empty state when user has no blog posts yet -->
                    <div class="text-center" style="padding: 40px 0;">
                        <i class="fa fa-newspaper fa-3x text-muted" style="margin-bottom: 20px;"></i>
                        <h3 class="text-muted">No blog posts yet</h3>
                        <p class="text-muted">Start sharing your thoughts with the world!</p>
                        <!-- Button to create first blog post -->
                        <a href="/blogtech/views/posts/new" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create Your First Post
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="../../js/script.js"></script>
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>