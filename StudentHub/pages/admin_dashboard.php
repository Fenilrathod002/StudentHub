<?php
require_once __DIR__ . "/../php/auth_check.php";
requireStudentHubAuthentication("login_user.html");
require_once __DIR__ . "/../php/db_connect_mysqli.php";

if ($_SESSION["user_role"] === "student") {
    header("Location: student_dashboard.php");
    exit;
}
if ($_SESSION["user_role"] === "teacher") {
    header("Location: teacher_dashboard.php");
    exit;
}
if ($_SESSION["user_role"] !== "admin") {
    clearStudentHubSession();
    header("Location: login_user.html");
    exit;
}

$name = htmlspecialchars((string) $_SESSION["user_name"], ENT_QUOTES, "UTF-8");
$logoutToken = htmlspecialchars(studentHubLogoutToken(), ENT_QUOTES, "UTF-8");
$stats = ['students' => 0, 'events' => 0, 'admins' => 0, 'registrations' => 0];
foreach (['students' => "SELECT COUNT(*) count FROM students WHERE role = 'student'", 'events' => 'SELECT COUNT(*) count FROM events', 'admins' => 'SELECT COUNT(*) count FROM administrators', 'registrations' => 'SELECT COUNT(*) count FROM registrations'] as $key => $sql) {
    $result = $conn->query($sql);
    if ($result) $stats[$key] = (int) $result->fetch_assoc()['count'];
}
$latest = $conn->query('SELECT r.registered_on, s.name student_name, e.title event_title FROM registrations r JOIN students s ON s.id=r.student_id JOIN events e ON e.event_id=r.event_id ORDER BY r.registered_on DESC LIMIT 5');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="../css/style.css"><title>StudentHub | Admin Dashboard</title></head>
<body class="dashboard-page">
    <header class="dashboard-header"><a href="admin_dashboard.php" class="brand"><img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo"><span class="brand-name">StudentHub</span></a><form action="../php/logout.php" method="POST" class="logout-form"><input type="hidden" name="csrf_token" value="<?= $logoutToken ?>"><button type="submit" class="dashboard-logout">Log out</button></form></header>
    <main class="dashboard-content">
        <section class="dashboard-welcome"><p class="eyebrow">Administrator area</p><h1>StudentHub — Admin Dashboard</h1><p>Welcome back, <?= $name ?>. Manage StudentHub records and review campus activity.</p></section>
        <section class="dashboard-info"><h2>Live overview</h2><div class="dashboard-links"><p><strong><?= $stats['students'] ?></strong> students</p><p><strong><?= $stats['events'] ?></strong> events</p><p><strong><?= $stats['admins'] ?></strong> administrators</p><p><strong><?= $stats['registrations'] ?></strong> registrations</p></div></section>
        <section class="dashboard-info"><h2>Student management</h2><p>Add, edit, delete, search, filter, and update student records without page reloads.</p><a class="btn-primary" href="../php/manage_students.php">Manage students</a></section>
        <section class="dashboard-info"><h2>Event management</h2><p>Create and manage events with validated poster uploads.</p><a class="btn-secondary" href="../php/manage_events.php">Manage events</a></section>
        <section class="dashboard-info"><h2>Staff accounts</h2><p>Create Teacher and Administrator accounts with the correct role and dashboard access.</p><a class="btn-secondary" href="register_staff.php">Create staff account</a></section>
        <section class="dashboard-info"><h2>Course management</h2><p>Add courses and remove courses that have no student registrations.</p><a class="btn-secondary" href="../php/manage_courses.php">Manage courses</a></section>
        <section class="dashboard-info"><h2>Event registrations</h2><p>Review existing event registration records.</p><a class="btn-secondary" href="../php/view_event_registrations.php">View event registrations</a></section>
        <section class="dashboard-info"><h2>Latest registrations</h2><?php if ($latest && $latest->num_rows): ?><ul><?php while ($registration = $latest->fetch_assoc()): ?><li><?= htmlspecialchars($registration['student_name']) ?> — <?= htmlspecialchars($registration['event_title']) ?> (<?= htmlspecialchars($registration['registered_on']) ?>)</li><?php endwhile; ?></ul><?php else: ?><p>No registrations yet.</p><?php endif; ?><a class="btn-secondary" href="../php/audit_logs.php">View audit logs</a></section>
    </main>
    <footer><p>Admin tools are available for daily StudentHub operations.</p></footer><script src="../js/script.js"></script>
</body>
</html>
