<?php
require_once '../includes/functions.php';
require_once '../includes/paste-functions.php';

// Initialize session first
initSecureSession();

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userPastes = getUserPastes($userId, 50); // Get up to 50 pastes
$languages = getSupportedLanguages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pastes - CodeVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .paste-row {
            transition: background-color 0.2s;
        }
        .paste-row:hover {
            background-color: #f8f9fa;
        }
        .expired {
            opacity: 0.6;
        }
        .visibility-badge {
            font-size: 0.75rem;
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
                <a class="nav-link active" href="my-pastes.php">My Pastes</a>
                <a class="nav-link" href="profile.php">Profile</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">My Pastes</h1>
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Paste
                    </a>
                </div>

                <?php if (empty($userPastes)): ?>
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-paste fa-4x text-muted"></i>
                        </div>
                        <h3 class="text-muted">No pastes yet</h3>
                        <p class="text-muted mb-4">You haven't created any pastes yet. Start sharing your code and text!</p>
                        <a href="create.php" class="btn btn-primary">Create Your First Paste</a>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Language</th>
                                            <th>Visibility</th>
                                            <th>Views</th>
                                            <th>Created</th>
                                            <th>Expires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userPastes as $paste): ?>
                                            <tr class="paste-row <?php echo $paste['is_expired'] ? 'expired' : ''; ?>">
                                                <td>
                                                    <a href="paste.php?id=<?php echo htmlspecialchars($paste['paste_id']); ?>" 
                                                       class="text-decoration-none fw-semibold">
                                                        <?php echo htmlspecialchars($paste['title']); ?>
                                                    </a>
                                                    <?php if ($paste['is_expired']): ?>
                                                        <span class="badge bg-danger ms-2">Expired</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($languages[$paste['language']] ?? $paste['language']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $visibilityColors = [
                                                        'public' => 'success',
                                                        'unlisted' => 'warning',
                                                        'private' => 'danger'
                                                    ];
                                                    $visibilityIcons = [
                                                        'public' => '🌍',
                                                        'unlisted' => '🔗',
                                                        'private' => '🔒'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?php echo $visibilityColors[$paste['visibility']]; ?> visibility-badge">
                                                        <?php echo $visibilityIcons[$paste['visibility']]; ?> 
                                                        <?php echo ucfirst($paste['visibility']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-eye text-muted"></i> 
                                                    <?php echo number_format($paste['views']); ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($paste['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($paste['expires_at']): ?>
                                                        <small class="text-<?php echo $paste['is_expired'] ? 'danger' : 'warning'; ?>">
                                                            <?php echo date('M j, Y', strtotime($paste['expires_at'])); ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-muted">Never</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="paste.php?id=<?php echo htmlspecialchars($paste['paste_id']); ?>" 
                                                           class="btn btn-outline-primary" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                onclick="copyLink('<?php echo htmlspecialchars($paste['paste_id']); ?>')" title="Copy Link">
                                                            <i class="fas fa-link"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deletePaste('<?php echo htmlspecialchars($paste['paste_id']); ?>')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?php echo count($userPastes); ?></h5>
                                    <p class="card-text">Total Pastes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <?php echo array_sum(array_column($userPastes, 'views')); ?>
                                    </h5>
                                    <p class="card-text">Total Views</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">
                                        <?php echo count(array_filter($userPastes, function($p) { return $p['visibility'] === 'public'; })); ?>
                                    </h5>
                                    <p class="card-text">Public Pastes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">
                                        <?php echo count(array_filter($userPastes, function($p) { return $p['is_expired']; })); ?>
                                    </h5>
                                    <p class="card-text">Expired</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <p class="mb-0" id="successMessage">Success!</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <script>
        // Copy paste link to clipboard
        function copyLink(pasteId) {
            const url = window.location.origin + window.location.pathname.replace('my-pastes.php', 'paste.php') + '?id=' + pasteId;
            navigator.clipboard.writeText(url).then(function() {
                showSuccess('Link copied to clipboard!');
            }, function(err) {
                alert('Failed to copy link');
            });
        }

        // Delete paste function
        function deletePaste(pasteId) {
            if (confirm('Are you sure you want to delete this paste? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete-paste.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'paste_id';
                input.value = pasteId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Show success modal
        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
            
            setTimeout(() => {
                modal.hide();
            }, 2000);
        }
    </script>
</body>
</html> 