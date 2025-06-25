<?php
require_once '../includes/functions.php';

// Initialize session first
initSecureSession();

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
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateMsg = 'Please enter a valid email address';
    } else {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        
        if ($stmt->rowCount() > 0) {
            $updateMsg = 'Email is already taken by another user';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET email = ?, display_name = ? WHERE id = ?");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
                    <div class="container-fluid">
                        <a href="index.php" class="navbar-brand fw-bold">PHP Auth System</a>
                        <div class="d-flex">
                            <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                        </div>
                    </div>
                </nav>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Your Profile</h1>
                        <?php if ($updateMsg): ?>
                            <div class="alert alert-success"> <?= htmlspecialchars($updateMsg) ?> </div>
                        <?php endif; ?>
                        <form method="post" class="mb-4">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Display Name</label>
                                <input type="text" class="form-control" name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Registration Date</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['created_at']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Login</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['last_login']) ?>" disabled>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profile</button>
                        </form>
                        <h2 class="h5 mb-3">Change Password</h2>
                        <?php if ($pwMsg): ?>
                            <div class="alert <?= strpos($pwMsg, 'success') !== false ? 'alert-success' : 'alert-danger' ?>"> <?= htmlspecialchars($pwMsg) ?> </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="old_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary w-100">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 