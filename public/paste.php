<?php
require_once '../includes/functions.php';
require_once '../includes/paste-functions.php';

// Initialize session first
initSecureSession();

// Get paste ID from URL
$pasteId = $_GET['id'] ?? '';

if (empty($pasteId)) {
    header('Location: index.php');
    exit();
}

// Get the paste
$paste = getPaste($pasteId);

if (!$paste) {
    header('Location: 404.php');
    exit();
}

// Check if user can view this paste
if ($paste['visibility'] === 'private' && (!isLoggedIn() || $_SESSION['user_id'] != $paste['user_id'])) {
    header('Location: 404.php');
    exit();
}

// Handle raw view - must be before any HTML output
if (isset($_GET['raw'])) {
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: public, max-age=3600');
    echo $paste['content'];
    exit();
}

$languages = getSupportedLanguages();
$languageName = $languages[$paste['language']] ?? $paste['language'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($paste['title']); ?> - CodeVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .paste-content {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        .paste-header {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }
        .line-numbers {
            color: #6c757d;
            user-select: none;
            margin-right: 1rem;
        }
        pre {
            margin: 0;
            background: transparent !important;
        }
        .copy-btn {
            transition: all 0.2s;
        }
        .copy-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">📋 CodeVault</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
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

    <div class="container py-4">
        <!-- Paste Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-2"><?php echo htmlspecialchars($paste['title']); ?></h1>
                        <div class="text-muted">
                            <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($languageName); ?></span>
                            <?php if ($paste['username']): ?>
                                by <strong><?php echo htmlspecialchars($paste['username']); ?></strong>
                            <?php else: ?>
                                by <strong>Anonymous</strong>
                            <?php endif; ?>
                            • <?php echo date('M j, Y \a\t g:i A', strtotime($paste['created_at'])); ?>
                            • <i class="fas fa-eye"></i> <?php echo number_format($paste['views']); ?> views
                            
                            <?php if ($paste['expires_at']): ?>
                                <br><small class="text-warning">
                                    <i class="fas fa-clock"></i> Expires: <?php echo date('M j, Y \a\t g:i A', strtotime($paste['expires_at'])); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary copy-btn" onclick="copyToClipboard()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <a href="paste.php?id=<?php echo htmlspecialchars($paste['paste_id']); ?>&raw=1" class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-file-alt"></i> Raw
                            </a>
                        </div>
                        
                        <?php if (isLoggedIn() && $_SESSION['user_id'] == $paste['user_id']): ?>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deletePaste()">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paste Content -->
        <div class="card shadow-sm">
            <div class="paste-content">
                <pre class="p-3"><code class="language-<?php echo htmlspecialchars($paste['language']); ?>" id="paste-content"><?php echo htmlspecialchars($paste['content']); ?></code></pre>
            </div>
        </div>

        <!-- Related Actions -->
        <div class="mt-4 text-center">
            <a href="create.php" class="btn btn-primary">Create Your Own Paste</a>
            <a href="index.php" class="btn btn-outline-secondary">Browse Recent Pastes</a>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <p class="mb-0">Copied to clipboard!</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <script>
        // Copy to clipboard function
        function copyToClipboard() {
            const content = document.getElementById('paste-content').textContent;
            navigator.clipboard.writeText(content).then(function() {
                // Show success modal
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                
                // Auto-hide after 1.5 seconds
                setTimeout(() => {
                    modal.hide();
                }, 1500);
            }, function(err) {
                alert('Failed to copy to clipboard');
            });
        }

        // Delete paste function
        function deletePaste() {
            if (confirm('Are you sure you want to delete this paste? This action cannot be undone.')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete-paste.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'paste_id';
                input.value = '<?php echo htmlspecialchars($paste['paste_id']); ?>';
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 