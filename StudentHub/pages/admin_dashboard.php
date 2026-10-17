<?php
require_once __DIR__ . "/../php/auth_check.php";
requireStudentHubAuthentication("login_user.html");

if ($_SESSION["user_role"] === "student") {
    header("Location: student_dashboard.php");
    exit;
}
if ($_SESSION["user_role"] !== "admin") {
    clearStudentHubSession();
    header("Location: login_user.html");
    exit;
}

$name = htmlspecialchars((string) $_SESSION["user_name"], ENT_QUOTES, "UTF-8");
$logoutToken = htmlspecialchars(studentHubLogoutToken(), ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="../css/style.css"><title>StudentHub | Admin Dashboard</title></head>
<body class="dashboard-page">
    <header class="dashboard-header"><a href="admin_dashboard.php" class="brand"><img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo"><span class="brand-name">StudentHub</span></a><form action="../php/logout.php" method="POST" class="logout-form"><input type="hidden" name="csrf_token" value="<?= $logoutToken ?>"><button type="submit" class="dashboard-logout">Log out</button></form></header>
    <main class="dashboard-content">
        <section class="dashboard-welcome"><p class="eyebrow">Administrator area</p><h1>StudentHub — Admin Dashboard</h1><p>Welcome back, <?= $name ?>. Manage StudentHub records and review campus activity.</p></section>
        <section class="dashboard-info"><h2>Student management</h2><p>Open the existing registration records to view, edit, or remove student entries.</p><a class="btn-primary" href="../php/view_registration.php">Manage registered students</a></section>
        <section class="dashboard-info"><h2>Event registrations</h2><p>Review existing event registration records.</p><a class="btn-secondary" href="../php/view_event_registrations.php">View event registrations</a></section>
    </main>
    <footer><p>Admin tools are available for daily StudentHub operations.</p></footer><script src="../js/script.js"></script>
</body>
</html>
