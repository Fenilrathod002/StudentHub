-- Separate StudentHub accounts by role without deleting existing records.
-- Run once in phpMyAdmin after backing up the studenthub database.
USE studenthub;

CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS administrators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Move legacy staff accounts to their role-specific tables. Existing source rows
-- remain untouched as a rollback-safe archive.
INSERT INTO teachers (name, email, password)
SELECT name, email, password FROM users WHERE role = 'teacher'
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO administrators (name, email, password)
SELECT name, email, password FROM users WHERE role = 'admin'
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO teachers (name, email, password)
SELECT name, email, password FROM students WHERE role = 'teacher'
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO administrators (name, email, password)
SELECT name, email, password FROM students WHERE role = 'admin'
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Move legacy generic student accounts. Their unavailable profile fields are
-- intentionally left blank/NULL; no information is invented.
INSERT INTO students (name, email, mobile, role, course_id, year, gender, password)
SELECT name, email, '', 'student', NULL, NULL, NULL, password
FROM users WHERE role = 'student'
ON DUPLICATE KEY UPDATE email = VALUES(email);
