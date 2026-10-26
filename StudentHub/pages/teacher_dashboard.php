<?php
require_once __DIR__ . "/../php/auth_check.php";
requireStudentHubAuthentication("login_user.html");

if ($_SESSION["user_role"] === "admin") {
    header("Location: admin_dashboard.php");
    exit;
}
if ($_SESSION["user_role"] === "student") {
    header("Location: student_dashboard.php");
    exit;
}
if ($_SESSION["user_role"] !== "teacher") {
    clearStudentHubSession();
    header("Location: login_user.html");
    exit;
}

$name = htmlspecialchars((string) $_SESSION["user_name"], ENT_QUOTES, "UTF-8");
$logoutToken = htmlspecialchars(studentHubLogoutToken(), ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="../css/style.css"><title>StudentHub | Teacher Dashboard</title></head>
<body class="dashboard-page">
    <header class="dashboard-header"><a href="teacher_dashboard.php" class="brand"><img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo"><span class="brand-name">StudentHub</span></a><form action="../php/logout.php" method="POST" class="logout-form"><input type="hidden" name="csrf_token" value="<?= $logoutToken ?>"><button type="submit" class="dashboard-logout">Log out</button></form></header>
    <main class="dashboard-content">
        <section class="dashboard-welcome"><p class="eyebrow">Teacher area</p><h1>StudentHub — Teacher Dashboard</h1><p>Welcome back, <?= $name ?>. Your teaching tools are ready.</p></section>
        <section class="dashboard-info"><h2>Teaching overview</h2><p>Review academic resources, attendance, and communication updates for your classes.</p><div class="dashboard-links"><a class="btn-primary" href="academics.html">Academics</a><a class="btn-secondary" href="attendance.html">Attendance</a><a class="btn-secondary" href="communication.html">Communication</a></div></section>
    </main>
    <footer><p>Support your students efficiently through StudentHub.</p></footer><script src="../js/script.js"></script>
</body>
</html>
