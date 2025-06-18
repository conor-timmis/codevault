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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
            <div class="container-fluid">
                <span class="navbar-brand fw-bold">PHP Auth System</span>
                <div class="d-flex">
                    <a href="profile.php" class="btn btn-primary me-2">Profile</a>
                    <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
                </div>
            </div>
        </nav>
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title mb-4">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                <div class="mb-4">
                    <h2 class="h5">Your Account Information</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="text-uppercase text-muted small mb-1">User ID:</div>
                                <div class="fw-bold"><?php echo $user['id']; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="text-uppercase text-muted small mb-1">Username:</div>
                                <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="text-uppercase text-muted small mb-1">Email:</div>
                                <div class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="text-uppercase text-muted small mb-1">Login Time:</div>
                                <div class="fw-bold"><?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="h5 mb-3">What you can do:</h2>
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item">✅ Register new accounts with secure password hashing</li>
                        <li class="list-group-item">✅ Login with username or email</li>
                        <li class="list-group-item">✅ Secure session management</li>
                        <li class="list-group-item">✅ Change password with current password verification</li>
                        <li class="list-group-item">✅ Logout functionality</li>
                        <li class="list-group-item">✅ Input validation</li>
                        <li class="list-group-item">✅ Environment variable configuration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
