<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Fetch user info from DB
$userId = $_SESSION['user_id'];
global $pdo;
$stmt = $pdo->prepare("SELECT username, email, display_name, created_at, last_login FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Handle form submission for profile update
$updateMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = sanitizeInput($_POST['email']);
    $display_name = sanitizeInput($_POST['display_name']);
    $stmt = $pdo->prepare("UPDATE users SET email = ?, display_name = ? WHERE id = ?");
    try {
        $stmt->execute([$email, $display_name, $userId]);
        $_SESSION['email'] = $email;
        $updateMsg = 'Profile updated successfully!';
        $stmt = $pdo->prepare("SELECT username, email, display_name, created_at, last_login FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        $updateMsg = 'Error updating profile: ' . $e->getMessage();
    }
}

// Handle password change
$pwMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $result = changePassword($oldPassword, $newPassword, $confirmPassword);
    $pwMsg = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - PHP Auth System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard" style="max-width: 500px; margin: 40px auto;">
            <header class="dashboard-header" style="justify-content: center;">
                <h1>Your Profile</h1>
            </header>
            <div class="dashboard-content">
                <?php if ($updateMsg): ?>
                    <div class="message success"> <?= htmlspecialchars($updateMsg) ?> </div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Display Name:</label>
                        <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Registration Date:</label>
                        <input type="text" value="<?= htmlspecialchars($user['created_at']) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Last Login:</label>
                        <input type="text" value="<?= htmlspecialchars($user['last_login']) ?>" disabled>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>

                <h2 style="margin-top: 40px;">Change Password</h2>
                <?php if ($pwMsg): ?>
                    <div class="message <?= strpos($pwMsg, 'success') !== false ? 'success' : 'error' ?>"> <?= htmlspecialchars($pwMsg) ?> </div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label>Current Password:</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password:</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
                <div class="links" style="margin-top: 30px; text-align: center;">
                    <a href="index.php">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 