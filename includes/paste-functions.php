<?php
require_once 'config.php';

/**
 * Generate a unique paste ID
 */
function generatePasteId($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    do {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        // Check if this ID already exists
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM pastes WHERE paste_id = ?");
        $stmt->execute([$randomString]);
        
    } while ($stmt->rowCount() > 0);
    
    return $randomString;
}

/**
 * Create a new paste
 */
function createPaste($title, $content, $language = 'text', $visibility = 'public', $expires = null) {
    global $pdo;
    
    try {
        // Validate input
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => 'Title and content are required'];
        }
        
        if (strlen($title) > 255) {
            return ['success' => false, 'message' => 'Title must be 255 characters or less'];
        }
        
        if (strlen($content) > 1000000) { // 1MB limit
            return ['success' => false, 'message' => 'Content too large (max 1MB)'];
        }
        
        // Generate unique paste ID
        $pasteId = generatePasteId();
        
        // Require authentication to create pastes
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in to create pastes'];
        }
        
        $userId = $_SESSION['user_id'];
        
        // Calculate expiration timestamp
        $expiresAt = null;
        if ($expires && $expires !== 'never') {
            switch ($expires) {
                case '1hour':
                    $expiresAt = date('Y-m-d H:i:s', time() + 3600);
                    break;
                case '1day':
                    $expiresAt = date('Y-m-d H:i:s', time() + 86400);
                    break;
                case '1week':
                    $expiresAt = date('Y-m-d H:i:s', time() + 604800);
                    break;
                case '1month':
                    $expiresAt = date('Y-m-d H:i:s', time() + 2592000);
                    break;
            }
        }
        
        // Insert paste
        $stmt = $pdo->prepare("INSERT INTO pastes (paste_id, title, content, language, visibility, expires_at, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$pasteId, $title, $content, $language, $visibility, $expiresAt, $userId]);
        
        return ['success' => true, 'paste_id' => $pasteId, 'message' => 'Paste created successfully!'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to create paste: ' . $e->getMessage()];
    }
}

/**
 * Get a paste by ID
 */
function getPaste($pasteId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, u.username 
            FROM pastes p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.paste_id = ? AND (p.expires_at IS NULL OR p.expires_at > NOW())
        ");
        $stmt->execute([$pasteId]);
        $paste = $stmt->fetch();
        
        if (!$paste) {
            return null;
        }
        
        // Increment view count
        $stmt = $pdo->prepare("UPDATE pastes SET views = views + 1 WHERE paste_id = ?");
        $stmt->execute([$pasteId]);
        $paste['views']++;
        
        return $paste;
        
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Get user's pastes
 */
function getUserPastes($userId, $limit = 20, $offset = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT paste_id, title, language, visibility, views, created_at, expires_at,
                   CASE WHEN expires_at IS NULL OR expires_at > NOW() THEN 0 ELSE 1 END as is_expired
            FROM pastes 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get recent public pastes
 */
function getRecentPublicPastes($limit = 20) {
    global $pdo;
    
    try {
        $limit = (int)$limit; // Ensure it's an integer
        $stmt = $pdo->prepare("
            SELECT p.paste_id, p.title, p.language, p.views, p.created_at, u.username
            FROM pastes p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.visibility = 'public' 
            AND (p.expires_at IS NULL OR p.expires_at > NOW())
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Delete a paste (only if user owns it)
 */
function deletePaste($pasteId) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'You must be logged in to delete pastes'];
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM pastes WHERE paste_id = ? AND user_id = ?");
        $stmt->execute([$pasteId, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Paste deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Paste not found or you don\'t have permission to delete it'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete paste'];
    }
}

/**
 * Get supported programming languages for syntax highlighting
 */
function getSupportedLanguages() {
    return [
        'text' => 'Plain Text',
        'php' => 'PHP',
        'javascript' => 'JavaScript',
        'python' => 'Python',
        'java' => 'Java',
        'cpp' => 'C++',
        'c' => 'C',
        'csharp' => 'C#',
        'html' => 'HTML',
        'css' => 'CSS',
        'sql' => 'SQL',
        'json' => 'JSON',
        'xml' => 'XML',
        'bash' => 'Bash',
        'powershell' => 'PowerShell',
        'ruby' => 'Ruby',
        'go' => 'Go',
        'rust' => 'Rust',
        'swift' => 'Swift',
        'kotlin' => 'Kotlin',
        'typescript' => 'TypeScript'
    ];
} 