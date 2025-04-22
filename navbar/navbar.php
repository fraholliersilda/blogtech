<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogtech/functions.php';
checkLoggedIn();
$is_admin = isAdmin();

$current_page = basename($_SERVER['REQUEST_URI']); 
//nese do perdorej klasa BaseController
// require __DIR__. '../../Database.php';
// use App\Controllers\BaseController;

// $baseController = new BaseController($conn);
?>

<!-- navbar -->
<div class="dashboard-nav">
    <header>
       <b><a href="#" class="brand-logo"><i class="fa-solid fa-laptop-code"></i> <span>BLOGTECH</span></a></b> 
    </header>
    <nav class="dashboard-nav-list">
        <a href="/blogtech/views/posts/blog"
            class="dashboard-nav-item <?php echo ($current_page == 'blog_posts.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Blog Posts
        </a>

        <?php if (!$is_admin): ?>
            <!-- show new post only if normal user -->
            <a href="/blogtech/views/posts/new"
                class="dashboard-nav-item <?php echo ($current_page == 'new_post.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-folder-open"></i> New post
            </a>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="dropdown">
                <button class="dashboard-nav-item"
                    style="background-color: #16a085; width: 100%; color: white; border: none;">
                    <i class="fas fa-cogs"></i>Admin Dashboard
                    <i class="fa-solid fa-caret-down" style="margin-left: 2px;"></i>
                </button>
                <div class="dropdown-menu" style="background-color: #16a085; width: 100%;">
                    <a href="/blogtech/views/admin/admins"
                        class="dashboard-nav-item <?php echo ($current_page == 'admins.php') ? 'active' : ''; ?>"
                        style="color: white;">
                        <i class="fas fa-user-shield"></i> Admins
                    </a>
                    <a href="/blogtech/views/admin/users"
                        class="dashboard-nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>"
                        style="color: white;">
                        <i class="fas fa-users"></i> Users
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <a href="/blogtech/views/profile/profile"
            class="dashboard-nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a>

        <div class="nav-item-divider log"></div>
        <a href="/blogtech/logout" class="dashboard-nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>