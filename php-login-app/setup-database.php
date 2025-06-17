<?php
// Database setup script
require_once __DIR__ . '/includes/config.php';

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/sql/create_users_table.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read SQL file");
    }
    
    // Execute the SQL statements
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!\n";
    echo "Users table has been created.\n";
    
} catch (Exception $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?> 