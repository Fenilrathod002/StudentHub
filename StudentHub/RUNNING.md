# Running StudentHub

The registration screens use PHP and MySQL. Do **not** use VS Code Live Server
or open files directly: static servers do not execute PHP and can download
`.php` files instead.

1. Install and start Apache and MySQL using XAMPP (or another PHP-enabled web server).
2. Put this `StudentHub` folder inside XAMPP's `htdocs` directory.
3. Create/import the `studenthub` MySQL database expected by `php/db_connect.php`.
4. Open `http://localhost/StudentHub/pages/index.html`.
5. Import `php/users_table.sql` if the `users` table has not already been created, then open `http://localhost/StudentHub/pages/login_user.html` to sign in. New secure accounts are students by default.

To create an administrator for a local practical demonstration, first register an account normally, then use phpMyAdmin to run the following for that account's email:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your-admin-email@example.com';
```

Do not use this as an application feature or share a real account password.

Alternatively, after installing PHP and adding it to your PATH, run this from
the project root:

```powershell
php -S localhost:8000
```

Then open `http://localhost:8000/pages/index.html`.
