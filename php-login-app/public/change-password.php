<?php
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = sanitizeInput($_POST['old_password'] ?? '');
    $newPassword = sanitizeInput($_POST['new_password'] ?? '');
    $confirmPassword = sanitizeInput($_POST['confirm_password'] ?? '');
    
    $result = changePassword($oldPassword, $newPassword, $confirmPassword);
    
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - PHP Auth</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Change Password</h2>
            <p>Welcome, <?php echo htmlspecialchars($currentUser['username']); ?>!</p>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" autocomplete="off">
                <div class="form-group">
                    <label for="old_password">Current Password</label>
                    <input type="password" id="old_password" name="old_password" required autocomplete="current-password">
                    <small>Enter your current password (the one you use to log in)</small>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
                    <small>Must be at least 6 characters long</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
            
            <div class="links">
                <a href="index.php">Back to Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <script src="assets/script.js"></script>
</body>
</html> 