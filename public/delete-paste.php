<?php
require_once '../includes/functions.php';
require_once '../includes/paste-functions.php';

// Initialize session first
initSecureSession();

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$pasteId = $_POST['paste_id'] ?? '';

if (empty($pasteId)) {
    header('Location: my-pastes.php?error=' . urlencode('Invalid paste ID'));
    exit();
}

// Attempt to delete the paste
$result = deletePaste($pasteId);

if ($result['success']) {
    header('Location: my-pastes.php?success=' . urlencode($result['message']));
} else {
    header('Location: my-pastes.php?error=' . urlencode($result['message']));
}
exit();
?> 