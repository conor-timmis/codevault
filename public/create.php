<?php
require_once '../includes/functions.php';
require_once '../includes/paste-functions.php';

// Initialize session first
initSecureSession();

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?message=' . urlencode('Please login to create pastes'));
    exit();
}

$message = '';
$messageType = '';
$languages = getSupportedLanguages();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // Don't sanitize content as it may contain code
    $language = $_POST['language'] ?? 'text';
    $visibility = $_POST['visibility'] ?? 'public';
    $expires = $_POST['expires'] ?? 'never';
    
    // Create the paste
    $result = createPaste($title, $content, $language, $visibility, $expires);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
    
    // Redirect to the paste if successful
    if ($result['success']) {
        header('Location: paste.php?id=' . $result['paste_id']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Paste - CodeVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        #content {
            font-family: 'Courier New', monospace;
            line-height: 1.5;
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
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

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Create New Paste</h1>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" id="title" name="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($title ?? ''); ?>" 
                                           placeholder="Enter a descriptive title..." required>
                                </div>
                                <div class="col-md-4">
                                    <label for="language" class="form-label">Language</label>
                                    <select id="language" name="language" class="form-select">
                                        <?php foreach ($languages as $code => $name): ?>
                                            <option value="<?php echo htmlspecialchars($code); ?>" 
                                                    <?php echo (isset($language) && $language === $code) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content *</label>
                                <textarea id="content" name="content" class="form-control" rows="15" 
                                          placeholder="Paste your code or text here..." required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                                <div class="char-counter mt-1">
                                    <span id="charCount">0</span> characters (max: 1,000,000)
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select id="visibility" name="visibility" class="form-select">
                                        <option value="public" <?php echo (isset($visibility) && $visibility === 'public') ? 'selected' : ''; ?>>
                                            🌍 Public - Listed in recent pastes
                                        </option>
                                        <option value="unlisted" <?php echo (isset($visibility) && $visibility === 'unlisted') ? 'selected' : ''; ?>>
                                            🔗 Unlisted - Only accessible via direct link
                                        </option>
                                        <option value="private" <?php echo (isset($visibility) && $visibility === 'private') ? 'selected' : ''; ?>>
                                            🔒 Private - Only you can see it
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="expires" class="form-label">Expiration</label>
                                    <select id="expires" name="expires" class="form-select">
                                        <option value="never" <?php echo (isset($expires) && $expires === 'never') ? 'selected' : ''; ?>>
                                            ♾️ Never expires
                                        </option>
                                        <option value="1hour" <?php echo (isset($expires) && $expires === '1hour') ? 'selected' : ''; ?>>
                                            ⏰ 1 Hour
                                        </option>
                                        <option value="1day" <?php echo (isset($expires) && $expires === '1day') ? 'selected' : ''; ?>>
                                            📅 1 Day
                                        </option>
                                        <option value="1week" <?php echo (isset($expires) && $expires === '1week') ? 'selected' : ''; ?>>
                                            📆 1 Week
                                        </option>
                                        <option value="1month" <?php echo (isset($expires) && $expires === '1month') ? 'selected' : ''; ?>>
                                            🗓️ 1 Month
                                        </option>
                                    </select>
                                </div>
                            </div>



                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-outline-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Create Paste</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counter
        const contentTextarea = document.getElementById('content');
        const charCountSpan = document.getElementById('charCount');
        
        function updateCharCount() {
            const count = contentTextarea.value.length;
            charCountSpan.textContent = count.toLocaleString();
            
            if (count > 1000000) {
                charCountSpan.style.color = '#dc3545';
            } else if (count > 900000) {
                charCountSpan.style.color = '#fd7e14';
            } else {
                charCountSpan.style.color = '#6c757d';
            }
        }
        
        contentTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
    </script>
</body>
</html> 