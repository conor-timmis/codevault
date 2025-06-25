<?php
require_once '../includes/functions.php';
require_once '../includes/paste-functions.php';

// Get recent public pastes
$recentPastes = getRecentPublicPastes(10);
$languages = getSupportedLanguages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeVault - Share Code & Text Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .paste-preview {
            transition: transform 0.2s;
        }
        .paste-preview:hover {
            transform: translateY(-2px);
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">📋 CodeVault</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="create.php">+ New Paste</a>
                <?php if (isLoggedIn()): ?>
                    <a class="nav-link" href="my-pastes.php">My Pastes</a>
                    <a class="nav-link" href="profile.php">Profile</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Share Code & Text Instantly</h1>
            <p class="lead mb-4">Create, share, and discover code snippets and text notes with syntax highlighting and expiration options.</p>
            <a href="create.php" class="btn btn-light btn-lg px-5">Create New Paste</a>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <?php if (isLoggedIn()): ?>
            <div class="alert alert-info">
                <strong>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</strong> 
                <a href="my-pastes.php">View your pastes</a> or <a href="create.php">create a new one</a>.
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <strong>🔒 Creating pastes requires an account.</strong> 
                <a href="register.php">Sign up</a> or <a href="login.php">login</a> to start sharing your code and text!
            </div>
        <?php endif; ?>

        <!-- Features -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-primary mb-3">
                            <i class="fas fa-code fa-3x"></i>
                        </div>
                        <h5>Syntax Highlighting</h5>
                        <p class="text-muted">Support for 20+ programming languages with beautiful syntax highlighting.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-3">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                        <h5>Expiration Control</h5>
                        <p class="text-muted">Set expiration times or keep your pastes forever. You control the lifecycle.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-3">
                            <i class="fas fa-eye fa-3x"></i>
                        </div>
                        <h5>Privacy Options</h5>
                        <p class="text-muted">Choose between public, private, or unlisted visibility for your content.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Public Pastes -->
        <h2 class="h4 mb-4">🔥 Recent Public Pastes</h2>
        
        <?php if (empty($recentPastes)): ?>
            <div class="text-center py-5">
                <p class="text-muted">No public pastes yet. <a href="create.php">Be the first to share something!</a></p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($recentPastes as $paste): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card paste-preview h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="paste.php?id=<?php echo htmlspecialchars($paste['paste_id']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($paste['title']); ?>
                                    </a>
                                </h6>
                                <div class="small text-muted mb-2">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($languages[$paste['language']] ?? $paste['language']); ?></span>
                                    <?php if ($paste['username']): ?>
                                        by <?php echo htmlspecialchars($paste['username']); ?>
                                    <?php else: ?>
                                        by Anonymous
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-eye"></i> <?php echo number_format($paste['views']); ?> views
                                    • <?php echo date('M j, Y', strtotime($paste['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 CodeVault. A simple pastebin for developers and writers.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
