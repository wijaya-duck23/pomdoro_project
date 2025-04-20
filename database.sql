-- Create database
CREATE DATABASE IF NOT EXISTS pomodoro_db;

-- Select database
USE pomodoro_db;

-- Create sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    type ENUM('pomodoro', 'short_break', 'long_break') NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in seconds',
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes
CREATE INDEX idx_user_id ON sessions (user_id);
CREATE INDEX idx_type ON sessions (type);
CREATE INDEX idx_start_time ON sessions (start_time); 