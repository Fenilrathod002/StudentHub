-- Practical 10 migration for an existing StudentHub users table.
-- Run the SELECT first. It must return no rows before running the ALTER statement.
SELECT id, email, role
FROM users
WHERE role NOT IN ('admin', 'student', 'teacher') OR role IS NULL;

-- After the check above returns no rows, restrict future roles to supported values.
ALTER TABLE users
    MODIFY role ENUM('admin', 'student', 'teacher') NOT NULL DEFAULT 'student';
