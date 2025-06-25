<?php
// Database setup script
require_once __DIR__ . '/includes/config.php';

try {
    // Create users table and add missing columns if needed
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE
    )");
    
    // Add display_name column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN display_name VARCHAR(100)");
        echo "Added display_name column to users table.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') === false) {
            throw $e;
        }
    }
    
    // Add last_login column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL");
        echo "Added last_login column to users table.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') === false) {
            throw $e;
        }
    }
    
    echo "Users table is ready.\n";
    
    // Create pastes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS pastes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paste_id VARCHAR(8) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        language VARCHAR(50) DEFAULT 'text',
        visibility ENUM('public', 'private', 'unlisted') DEFAULT 'public',
        expires_at TIMESTAMP NULL,
        user_id INT NULL,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    
    echo "Pastes table is ready.\n";
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?> 