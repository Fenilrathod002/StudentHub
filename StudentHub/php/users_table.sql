-- Practical 9: run this table definition only when the users table does not exist.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- For an existing compatible users table, first check that no duplicate emails exist:
-- SELECT email, COUNT(*) AS duplicate_count FROM users GROUP BY email HAVING COUNT(*) > 1;
-- If the query returns no rows and there is no unique index on email, run this once:
-- ALTER TABLE users ADD CONSTRAINT uq_users_email UNIQUE (email);

-- Practical 10: for an existing users table, see practical_10_migration.sql.
