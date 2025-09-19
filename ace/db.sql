-- gym_db schema for XAMPP (MySQL)
CREATE DATABASE IF NOT EXISTS gym_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_db;

-- users: admin and receptionist
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','receptionist') NOT NULL DEFAULT 'receptionist',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- members
CCREATE TABLE IF NOT EXISTS members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  phone VARCHAR(20),
  email VARCHAR(100),
  plan VARCHAR(100), -- using plan name directly
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- plans
CREATE TABLE IF NOT EXISTS plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  duration_months INT NOT NULL,
  price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- renewals
CREATE TABLE IF NOT EXISTS renewals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  plan_id INT NOT NULL,
  renewal_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  new_end_date DATE NOT NULL,
  notes TEXT,
  FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- logs
CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(200),
  details TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- seed an admin user (username: admin, password: admin123)
INSERT INTO users (username, password, role) VALUES
('admin', CONCAT('\$2y\$10\$','REPLACE_ME_WITH_HASH'), 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Seed plans
INSERT INTO plans (name, duration_months, price) VALUES
('1 Month', 1, 30.00),
('3 Months', 3, 80.00),
('6 Months', 6, 150.00)
ON DUPLICATE KEY UPDATE name = name;
