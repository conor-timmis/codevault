<?php
require_once '../includes/functions.php';

// Logout the user
$result = logoutUser();

// Redirect to login page with success message
header('Location: login.php?message=' . urlencode($result['message']));
exit();
