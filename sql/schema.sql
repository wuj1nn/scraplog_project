DROP DATABASE IF EXISTS scraplog;
CREATE DATABASE scraplog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scraplog;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(50) NOT NULL,
  role ENUM('admin','staff','customer') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  label VARCHAR(60) NOT NULL,
  short_name VARCHAR(30) NOT NULL,
  color CHAR(7) NOT NULL,
  capacity_kg DECIMAL(10,2) NOT NULL DEFAULT 500,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(60) NOT NULL,
  sell_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  markup_pct DECIMAL(5,2) NOT NULL DEFAULT 25,
  buy_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  is_other TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_item (category_id, name),
  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE stock_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  item_id INT NOT NULL,
  kg DECIMAL(10,2) NOT NULL,
  source VARCHAR(100) NOT NULL DEFAULT '',
  note VARCHAR(255) NOT NULL DEFAULT '',
  detail VARCHAR(100) NOT NULL DEFAULT '',
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_stock_created (created_at),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (item_id) REFERENCES items(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  item_id INT NOT NULL,
  kg DECIMAL(10,2) NOT NULL,
  price_per_kg DECIMAL(10,2) NOT NULL,
  buyer VARCHAR(100) NOT NULL DEFAULT '',
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sales_created (created_at),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (item_id) REFERENCES items(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE removals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  item_id INT NOT NULL,
  kg DECIMAL(10,2) NOT NULL,
  reason VARCHAR(40) NOT NULL,
  note VARCHAR(255) NOT NULL DEFAULT '',
  price_per_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_removals_created (created_at),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (item_id) REFERENCES items(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

INSERT INTO categories (code, label, short_name, color, sort_order) VALUES
  ('bakal', 'Bakal (Iron/Steel)', 'Bakal', '#f4c430', 1),
  ('tanso', 'Tanso (Copper)', 'Tanso', '#e5aa70', 2),
  ('aluminum', 'Aluminum', 'Aluminum', '#c9a227', 3),
  ('plastik', 'Plastik (Plastic)', 'Plastik', '#8c7350', 4),
  ('karton', 'Karton/Papel (Paper)', 'Karton', '#6fa98a', 5),
  ('bote', 'Bote (Glass)', 'Bote', '#7c8a82', 6),
  ('ewaste', 'E-waste', 'E-waste', '#b87333', 7);

INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT id, short_name, 0, 25, 0, 0, 1 FROM categories WHERE code = 'bakal';
INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT id, short_name, 0, 25, 0, 0, 1 FROM categories WHERE code = 'tanso';
INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT id, short_name, 0, 25, 0, 0, 1 FROM categories WHERE code = 'aluminum';

INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT c.id, n.name, 0, 25, 0, n.other, n.ord
FROM categories c, (
  SELECT 'PET bottles' AS name, 15.00 AS sell, 0 AS other, 1 AS ord
  UNION ALL SELECT 'HDPE containers', 12.50, 0, 2
  UNION ALL SELECT 'PP / hard plastic', 10.00, 0, 3
  UNION ALL SELECT 'Soft plastic / sando', 5.00, 0, 4
  UNION ALL SELECT 'Other', 5.00, 1, 5
) n WHERE c.code = 'plastik';

INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT c.id, n.name, 0, 25, 0, n.other, n.ord
FROM categories c, (
  SELECT 'Corrugated cardboard' AS name, 6.25 AS sell, 0 AS other, 1 AS ord
  UNION ALL SELECT 'Newspaper (Dyaryo)', 7.50, 0, 2
  UNION ALL SELECT 'White paper', 11.25, 0, 3
  UNION ALL SELECT 'Mixed paper', 5.00, 0, 4
  UNION ALL SELECT 'Other', 3.75, 1, 5
) n WHERE c.code = 'karton';

INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT c.id, n.name, 0, 25, 0, n.other, n.ord
FROM categories c, (
  SELECT 'Clear bottles' AS name, 2.50 AS sell, 0 AS other, 1 AS ord
  UNION ALL SELECT 'Brown / beer bottles', 2.50, 0, 2
  UNION ALL SELECT 'Green bottles', 1.25, 0, 3
  UNION ALL SELECT 'Jars & containers', 1.25, 0, 4
  UNION ALL SELECT 'Other', 1.25, 1, 5
) n WHERE c.code = 'bote';

INSERT INTO items (category_id, name, sell_price, markup_pct, buy_price, is_other, sort_order)
SELECT c.id, n.name, 0, 25, 0, n.other, n.ord
FROM categories c, (
  SELECT 'Circuit boards' AS name, 125.00 AS sell, 0 AS other, 1 AS ord
  UNION ALL SELECT 'Cellphones & tablets', 62.50, 0, 2
  UNION ALL SELECT 'Computers & laptops', 25.00, 0, 3
  UNION ALL SELECT 'Cables & chargers', 37.50, 0, 4
  UNION ALL SELECT 'Small appliances', 10.00, 0, 5
  UNION ALL SELECT 'Other', 10.00, 1, 6
) n WHERE c.code = 'ewaste';

INSERT INTO users (username, password_hash, name, role) VALUES
  ('admin', '$2b$10$u6SDsKM5FcUzqYsjMY4FWO2l.JY73NBhENrz5PemaaZlv3GDDz1Ne', 'Admin', 'admin'),
  ('staff', '$2b$10$BQSgKYqEQrgANiLD0kD92.ABzQWwRgUlWpipz8KYy8j/lKAU/Bt5G', 'Staff', 'staff'),
  ('customer', '$2y$10$be4Q47.NHqYLM2Dtcef3LunCG8MfXeJGyJSvijJWWZdVQF7ZLB6f6', 'Customer', 'customer');
