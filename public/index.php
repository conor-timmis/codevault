<?php
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PHP Auth System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <header class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                <nav>
                    <a href="profile.php" class="btn btn-primary">Profile</a>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </nav>
            </header>
            
            <div class="dashboard-content">
                <div class="user-info">
                    <h2>Your Account Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>User ID:</label>
                            <span><?php echo $user['id']; ?></span>
                        </div>
                        <div class="info-item">
                            <label>Username:</label>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Login Time:</label>
                            <span><?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="features">
                    <h2>What you can do:</h2>
                    <ul>
                        <li>✅ Register new accounts with secure password hashing</li>
                        <li>✅ Login with username or email</li>
                        <li>✅ Secure session management</li>
                        <li>✅ Change password with current password verification</li>
                        <li>✅ Logout functionality</li>
                        <li>✅ Input validation </li>
                        <li>✅ Environment variable configuration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
