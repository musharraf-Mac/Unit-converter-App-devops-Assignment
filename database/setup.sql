-- Create database
CREATE DATABASE IF NOT EXISTS unit_converter;

USE unit_converter;

-- Create conversion_history table
CREATE TABLE IF NOT EXISTS conversion_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    input_value DECIMAL(20, 6) NOT NULL,
    from_unit VARCHAR(50) NOT NULL,
    to_unit VARCHAR(50) NOT NULL,
    result DECIMAL(20, 6) NOT NULL,
    conversion_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at DESC)
);
