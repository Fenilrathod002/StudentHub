# Practical 10: Secure Login, Logout, PHP Sessions and Role-Based Access Control

## Aim

To build a secure StudentHub login system using PHP sessions, MySQLi prepared statements, password hashing, role-based dashboards, session timeout, and secure logout.

## Concepts implemented

- MySQLi connection with `utf8mb4`
- Prepared statements and `password_verify()`
- PHP sessions with session ID regeneration after login
- Student and administrator role checks
- Fifteen-minute inactivity timeout (900 seconds)
- POST logout protected with a CSRF token
- Session cookie removal and safe HTML output with `htmlspecialchars()`

## Files used

- `php/db_connect_mysqli.php` connects the existing `studenthub` database.
- `php/process_login.php` validates and authenticates login requests.
- `php/auth_check.php` shares timeout, session cleanup, and logout-token logic.
- `php/logout.php` securely ends a session.
- `pages/login_user.html` is the sign-in form.
- `pages/admin_dashboard.php` and `pages/student_dashboard.php` are protected dashboards.
- `php/practical_10_migration.sql` optionally restricts existing database roles.

## Database requirement

The `users` table needs `id`, `name`, `email`, `password`, and `role` fields. Passwords must be hashes, not plaintext. New secure registrations are always assigned the `student` role. Run the migration only after its first query confirms that no unsupported roles exist.

## How to execute

1. Start Apache and MySQL in XAMPP.
2. Import or create the `studenthub` database and import `php/users_table.sql` if the `users` table is missing.
3. Place the project in XAMPP `htdocs` and open `http://localhost/StudentHub/pages/register_user.php` to create a student account.
4. Open `http://localhost/StudentHub/pages/login_user.html` and sign in.
5. For an administrator demonstration only, change a registered account role to `admin` in phpMyAdmin as documented in `RUNNING.md`.

## Inputs and expected outputs

| Input | Expected output |
| --- | --- |
| Valid student email and password | Student dashboard |
| Valid administrator email and password | Admin dashboard |
| Unknown email or wrong password | Generic invalid-credentials message |
| Inactive session for over 900 seconds | Redirect to login with timeout message |
| Logout button | Session cleared and login page displayed |

## Test cases and results

| Test case | Result |
| --- | --- |
| PHP syntax check for all Practical 10 PHP files | Passed with the local XAMPP PHP executable. |
| Valid student login | Must be tested manually with a real registered `users` record. |
| Valid admin login | Must be tested manually after safely assigning a test record the `admin` role. |
| Invalid credentials, role redirects, timeout, and logout | Must be tested manually through Apache/XAMPP because they depend on HTTP sessions and database records. |

## Security features

Passwords are hashed by the existing registration handler and checked with `password_verify()`. Login SQL uses a prepared statement. Session IDs regenerate after successful login. Dashboard access is enforced on the server, roles are checked explicitly, user names are escaped before display, and logout clears the session and expires its cookie.
