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
        <h1>Your Profile</h1>
        <?php if ($updateMsg): ?>
            <div class="alert"> <?= htmlspecialchars($updateMsg) ?> </div>
        <?php endif; ?>
        <form method="post">
            <label>Username:</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled><br>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
            <label>Display Name:</label>
            <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>"><br>
            <label>Registration Date:</label>
            <input type="text" value="<?= htmlspecialchars($user['created_at']) ?>" disabled><br>
            <label>Last Login:</label>
            <input type="text" value="<?= htmlspecialchars($user['last_login']) ?>" disabled><br>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <h2>Change Password</h2>
        <?php if ($pwMsg): ?>
            <div class="alert"> <?= htmlspecialchars($pwMsg) ?> </div>
        <?php endif; ?>
        <form method="post">
            <label>Current Password:</label>
            <input type="password" name="old_password" required><br>
            <label>New Password:</label>
            <input type="password" name="new_password" required><br>
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required><br>
            <button type="submit" name="change_password">Change Password</button>
        </form>
        <br>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html> 