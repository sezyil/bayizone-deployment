-- Bayizone Database Initialization
CREATE DATABASE IF NOT EXISTS bayi_sistemi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user if not exists
CREATE USER IF NOT EXISTS 'bayi_user'@'%' IDENTIFIED BY 'bayi_password_2024';

-- Grant privileges
GRANT ALL PRIVILEGES ON bayi_sistemi.* TO 'bayi_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Use the database
USE bayi_sistemi; 