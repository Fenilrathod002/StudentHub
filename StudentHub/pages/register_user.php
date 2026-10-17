<?php

require_once __DIR__ . "/../php/auth_check.php";
startStudentHubSession();

if (empty($_SESSION["registration_csrf_token"])) {
    $_SESSION["registration_csrf_token"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["registration_csrf_token"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>StudentHub | Secure User Registration</title>
</head>
<body>
    <header class="site-header">
        <div class="nav-container">
            <a href="index.html" class="brand">
                <img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo">
                <span class="brand-name">StudentHub</span>
            </a>
            <nav class="main-navigation" aria-label="Main Navigation">
                <ul class="nav-list">
                    <li><a href="index.html" class="nav-link">Home</a></li>
                    <li><a href="about.html" class="nav-link">About</a></li>
                    <li><a href="academics.html" class="nav-link">Academics</a></li>
                    <li><a href="events.php" class="nav-link">Events</a></li>
                    <li><a href="contact.html" class="nav-link">Contact</a></li>
                </ul>
            </nav>
            <div class="nav-actions">
                <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Switch to dark mode" title="Switch to dark mode">🌙</button>
                <a href="login_user.html" class="nav-login">Login</a>
                <a href="register_user.php" class="nav-register" aria-current="page">Register</a>
            </div>
        </div>
    </header>
    <main>
        <section>
            <h1>Secure User Registration</h1>
            <p>Create a StudentHub account. Your password is stored securely.</p>
            <form action="../php/process_user_registration.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, "UTF-8") ?>">

                <label for="name">Name</label>
                <input type="text" id="name" name="name" required maxlength="100" autocomplete="name">

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required maxlength="150" autocomplete="email">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6" maxlength="4096" autocomplete="new-password">

                <button type="submit">Register</button>
            </form>
        </section>
    </main>
    <footer><p>Welcome to StudentHub.</p></footer>
    <script src="../js/script.js"></script>
</body>
</html>
