-- Hostel Management System (Simple Schema)
-- Run in phpMyAdmin / MySQL

CREATE DATABASE IF NOT EXISTS hostel;
USE hostel;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(200) NOT NULL,
  student_id VARCHAR(50),
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL,  -- 'student' or 'admin'
  gender VARCHAR(10),         -- 'male' or 'female'
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blocks table
CREATE TABLE blocks (
  block_id VARCHAR(10) PRIMARY KEY,
  block_name VARCHAR(50),
  gender VARCHAR(10),   -- 'male' or 'female'
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE rooms (
  room_id INT AUTO_INCREMENT PRIMARY KEY,
  block_id VARCHAR(10),
  floor_no VARCHAR(10),  -- keep simple
  room_no VARCHAR(10),
  partition_capacity VARCHAR(10) DEFAULT '6',
  status VARCHAR(50) DEFAULT 'Unoccupied', -- Unoccupied, Occupied, etc.
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (block_id) REFERENCES blocks(block_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Student-Room relationship
CREATE TABLE student_rooms (
  sr_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  room_id INT NOT NULL,
  semester VARCHAR(50),
  status VARCHAR(50) DEFAULT 'Active',
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  released_at TIMESTAMP NULL,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Tickets (reports/issues)
CREATE TABLE tickets (
  ticket_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  category VARCHAR(100),   -- e.g., Broken Furniture, Water, etc.
  description TEXT,
  status VARCHAR(20) DEFAULT 'Open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin account
INSERT INTO users (full_name, email, password, role)
VALUES ('System Admin', 'admin@gmi.edu.my', 'admin123', 'admin');

-- Insert blocks
INSERT INTO blocks (block_id, block_name, gender) VALUES
('A1', 'Block A1', 'female'),
('A2', 'Block A2', 'male'),
('A3', 'Block A3', 'male'),
('A4', 'Block A4', 'male'),
('A5', 'Block A5', 'male'),
('A7', 'Block A7', 'female');
