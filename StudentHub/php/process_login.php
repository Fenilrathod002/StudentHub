<?php

require_once __DIR__ . "/auth_check.php";

startStudentHubSession();

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
    header("Location: ../pages/login_user.html?error=invalid_request");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (!is_string($email) || !is_string($password) || $email === "" || $password === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/login_user.html?error=invalid_credentials");
    exit;
}

require __DIR__ . "/db_connect_mysqli.php";

$statement = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
if ($statement === false) {
    error_log("StudentHub login query preparation failed: " . $conn->error);
    $conn->close();
    header("Location: ../pages/login_user.html?error=service");
    exit;
}

$statement->bind_param("s", $email);
if (!$statement->execute()) {
    error_log("StudentHub login query failed: " . $statement->error);
    $statement->close();
    $conn->close();
    header("Location: ../pages/login_user.html?error=service");
    exit;
}

$statement->bind_result($userId, $name, $storedEmail, $passwordHash, $role);
$userFound = $statement->fetch();
$statement->close();
$conn->close();

if (!$userFound || !password_verify($password, $passwordHash)) {
    header("Location: ../pages/login_user.html?error=invalid_credentials");
    exit;
}

if (!in_array($role, ["admin", "student"], true)) {
    error_log("StudentHub login rejected unsupported role for user ID " . (int) $userId);
    header("Location: ../pages/login_user.html?error=invalid_credentials");
    exit;
}

session_regenerate_id(true);
$_SESSION["user_id"] = (int) $userId;
$_SESSION["user_name"] = $name;
$_SESSION["user_email"] = $storedEmail;
$_SESSION["user_role"] = $role;
$_SESSION["last_activity"] = time();
$_SESSION["logout_csrf_token"] = bin2hex(random_bytes(32));

if ($role === "admin") {
    header("Location: ../pages/admin_dashboard.php");
    exit;
}

header("Location: ../pages/student_dashboard.php");
exit;
