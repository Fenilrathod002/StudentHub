<?php

require_once __DIR__ . "/../php/auth_check.php";
requireStudentHubRole("login_user.html", ["admin"]);

if (empty($_SESSION["staff_registration_csrf_token"])) {
    $_SESSION["staff_registration_csrf_token"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["staff_registration_csrf_token"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>StudentHub | Create Staff Account</title>
</head>
<body>
    <header class="dashboard-header">
        <a href="admin_dashboard.php" class="brand"><img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo"><span class="brand-name">StudentHub</span></a>
    </header>
    <main>
        <section>
            <h1>Create Staff Account</h1>
            <p>Only administrators can create Teacher and Administrator accounts.</p>
            <form action="../php/process_staff_registration.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, "UTF-8") ?>">

                <label for="name">Name</label>
                <input type="text" id="name" name="name" required maxlength="100" autocomplete="name">

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required maxlength="150" autocomplete="email">

                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Administrator</option>
                </select>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6" maxlength="4096" autocomplete="new-password">

                <button type="submit">Create Account</button>
            </form>
            <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
        </section>
    </main>
</body>
</html>
