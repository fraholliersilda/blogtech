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
        body {
            background: #f8f9fa;
            font-family: "Segoe UI", sans-serif;
        }

        h1 {
            text-align: center;
            margin: 20px 0 30px;
            font-weight: 700;
            font-size: 40px;
            position: relative;
        }

        h1::after {
            content: "";
            width: 120px;
            height: 4px;
            background:#16a085;
            display: block;
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .post-card {
            background: #fff;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: 0.3s ease;
        }

        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        .post-content {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .post-image img {
            width: 260px;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
        }

        .post-details h5 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .post-details p {
            font-size: 15px;
            color: #555;
        }

        .engagement-stats {
            margin-top: 8px;
            font-size: 14px;
            color: #777;
        }

        .engagement-stats i {
            margin-right: 6px;
            color: #555;
        }

        .post-actions {
            margin-top: 15px;
        }

        .post-actions .btn {
            border-radius: 20px;
            padding: 6px 16px;
        }

        .post-actions .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .post-actions .btn-primary {
            background: #0275d8;
            border-color: #0275d8;
        }

        .post-actions .btn-danger {
            background: #d9534f;
            border-color: #d43f3a;
        }

        @media (max-width: 768px) {
            .post-content {
                flex-direction: column;
                text-align: center;
            }

            .post-image img {
                width: 100%;
                height: 220px;
            }
        }
    </style>
</head>

<body>

    <?php include BASE_PATH . '/navbar/navbar.php'; ?>

    <?php displaySuccessMessages(); ?>

    <div class="container">
        <h1>BLOG POSTS</h1>
        <div class="row">

            <?php if (!empty($posts)) { ?>
                <?php foreach ($posts as $post) { ?>
                    <div class="col-md-12">
                        <div class="post-card">
                            <div class="post-content">

                                <!-- Display post cover image or default -->
                                <div class="post-image">
                                    <img src="<?= htmlspecialchars($post['cover_photo_path'] ?? '/blogtech/images/default_cover.jpg'); ?>"
                                        alt="Cover">
                                </div>

                                <div class="post-details">
                                    <h5><?= htmlspecialchars($post['title']); ?></h5>

                                    <p><em>By: <?= htmlspecialchars($post['username'] ?? 'Unknown'); ?></em></p>

                                    <!-- Display truncated description (first 250 characters) -->
                                    <p><?= htmlspecialchars(substr($post['description'], 0, 250)); ?>...</p>

                                    <!-- Show comment count -->
                                    <div class="engagement-stats">
                                        <i class="fas fa-comment"></i>
                                        <?= $post['comments_count'] ?? 0; ?> comments
                                    </div>

                                    <div class="post-actions">
                                        <a href="<?= BASE_URL ?>/views/posts/post/<?= $post['id']; ?>"
                                            class="btn btn-secondary">Read More</a>

                                        <?php 
                                        // Show edit/delete buttons only for post owner or admin
                                        if ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'])) { ?>
                                            <a href="/blogtech/views/posts/edit/<?= $post['id']; ?>"
                                                class="btn btn-primary">Edit</a>

                                            <form action="<?= BASE_URL ?>/posts/delete/<?= $post['id'] ?>" method="post"
                                                style="display: inline;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                                            </form>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">No posts found.</p>
            <?php } ?>

        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>

</html>