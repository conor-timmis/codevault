-- Create pastes table for pastebin functionality
CREATE TABLE IF NOT EXISTS pastes (
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
);

-- Create indexes for better performance (if not exists)
CREATE INDEX IF NOT EXISTS idx_paste_id ON pastes(paste_id);
CREATE INDEX IF NOT EXISTS idx_user_id ON pastes(user_id);
CREATE INDEX IF NOT EXISTS idx_visibility ON pastes(visibility);
CREATE INDEX IF NOT EXISTS idx_created_at ON pastes(created_at);
CREATE INDEX IF NOT EXISTS idx_expires_at ON pastes(expires_at); 