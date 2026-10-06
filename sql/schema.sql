CREATE DATABASE IF NOT EXISTS scraplog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scraplog;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(50) NOT NULL,
  role ENUM('admin','staff') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  label VARCHAR(60) NOT NULL,
  short_name VARCHAR(30) NOT NULL,
  color CHAR(7) NOT NULL,
  buy_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  sell_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  capacity_kg DECIMAL(10,2) NOT NULL DEFAULT 500,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stock_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  kg DECIMAL(10,2) NOT NULL,
  source VARCHAR(100) NOT NULL DEFAULT '',
  note VARCHAR(255) NOT NULL DEFAULT '',
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_stock_created (created_at),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  kg DECIMAL(10,2) NOT NULL,
  price_per_kg DECIMAL(10,2) NOT NULL,
  buyer VARCHAR(100) NOT NULL DEFAULT '',
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sales_created (created_at),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (code, label, short_name, color, sort_order) VALUES
  ('bakal', 'Bakal (Iron/Steel)', 'Bakal', '#f4c430', 1),
  ('tanso', 'Tanso (Copper)', 'Tanso', '#e5aa70', 2),
  ('aluminum', 'Aluminum', 'Aluminum', '#c9a227', 3),
  ('plastik', 'Plastik (Plastic)', 'Plastik', '#8c7350', 4),
  ('karton', 'Karton/Papel (Paper)', 'Karton', '#6fa98a', 5),
  ('bote', 'Bote (Glass)', 'Bote', '#7c8a82', 6),
  ('ewaste', 'E-waste', 'E-waste', '#b87333', 7);

INSERT IGNORE INTO users (username, password_hash, name, role) VALUES
  ('admin', '$2b$10$u6SDsKM5FcUzqYsjMY4FWO2l.JY73NBhENrz5PemaaZlv3GDDz1Ne', 'Admin', 'admin'),
  ('staff', '$2b$10$BQSgKYqEQrgANiLD0kD92.ABzQWwRgUlWpipz8KYy8j/lKAU/Bt5G', 'Staff', 'staff');
