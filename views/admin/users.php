<?php 
require_once 'errorHandler.php';
require_once 'successHandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
    <div class="admin_dashboard">
        <h1><b>Users</b></h1>
        
        <!-- Search Bar -->
        <div class="search-container" style="margin-bottom: 20px;">
            <form method="GET" action="/blogtech/views/admin/users" style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative; flex: 1; max-width: 400px;">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search users by username or email..." 
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        style="width: 100%; padding: 10px 40px 10px 15px; border: 1px solid #ddd; border-radius: 25px; font-size: 14px;"
                    >
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                </div>
                <button 
                    type="submit" 
                    style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 20px; cursor: pointer;"
                >
                    Search
                </button>
                <?php if (!empty($_GET['search'])): ?>
                    <a 
                        href="/blogtech/views/admin/users" 
                        style="padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 20px;"
                    >
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Search Results Info -->
        <?php if (!empty($_GET['search'])): ?>
            <div style="margin-bottom: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px;">
                <i class="fas fa-info-circle"></i>
                Showing results for: "<strong><?= htmlspecialchars($_GET['search']) ?></strong>" 
                (<?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?> found)
            </div>
        <?php endif; ?>

        <!-- Display errors using displayErrors function -->
        <?php
            displayErrors();
            displaySuccessMessages();
        ?>

        <!-- No users found message -->
        <?php if (empty($users)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px;"></i>
                <h3>No users found</h3>
                <?php if (!empty($_GET['search'])): ?>
                    <p>Try adjusting your search criteria or <a href="/blogtech/views/admin/users">view all users</a>.</p>
                <?php else: ?>
                    <p>There are no users in the system yet.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="user-cards">
                <?php foreach ($users as $user) {
                    if ($user['id'] === $_SESSION['user_id']) {
                        continue;
                    }
                ?>
                    <div class="user-card">
                        <h3><?= htmlspecialchars($user['username']) ?></h3>
                        <p>Email: <?= htmlspecialchars($user['email']) ?></p>

                        <!-- Update Username and Email Form -->
                        <form method="POST" action="/blogtech/views/admin/users">
                            <input type="hidden" name="action" value="update_user">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <label for="username">Username:</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                            <label for="email">Email:</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            <button type="submit">Update User</button>
                        </form>

                        <!-- Delete User Form -->
                        <form method="POST" action="/blogtech/views/admin/users" onsubmit="return confirmDelete()">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" style="background-color: #A72925;">Delete User</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="../../js/script.js"></script>
</body>
</html>
