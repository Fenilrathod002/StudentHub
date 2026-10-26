-- Legacy account-table backup for older StudentHub installations.
-- New StudentHub registrations use students, teachers, and administrators.
-- Do not import this for a fresh installation unless you need to preserve older accounts.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student', 'teacher') NOT NULL DEFAULT 'student',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- For an existing compatible users table, first check that no duplicate emails exist:
-- SELECT email, COUNT(*) AS duplicate_count FROM users GROUP BY email HAVING COUNT(*) > 1;
-- If the query returns no rows and there is no unique index on email, run this once:
-- ALTER TABLE users ADD CONSTRAINT uq_users_email UNIQUE (email);

-- Practical 10: for an existing users table, see practical_10_migration.sql.
