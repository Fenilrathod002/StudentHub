# Running StudentHub

The registration screens use PHP and MySQL. Do **not** use VS Code Live Server
or open files directly: static servers do not execute PHP and can download
`.php` files instead.

1. Install and start Apache and MySQL using XAMPP (or another PHP-enabled web server).
2. Put this `StudentHub` folder inside XAMPP's `htdocs` directory.
3. Import `php/studenthub_schema.sql` in phpMyAdmin to create the `studenthub` database and its core tables, including separate `students`, `teachers`, and `administrators` account tables. This script only creates missing tables; it does not drop data or add sample records.
4. Open `http://localhost/StudentHub/pages/index.html`.
5. For an older installation with a `users` table, import `php/role_tables_migration.sql` once to copy legacy accounts into the separate role tables. The old `users` table is retained as a backup; new accounts are never saved there. Then open `http://localhost/StudentHub/pages/login_user.html` to sign in.

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
