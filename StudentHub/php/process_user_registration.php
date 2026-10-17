<?php

function showRegistrationResult(string $heading, string $message, bool $success = false, int $statusCode = 200): void
{
    http_response_code($statusCode);
    $title = $success ? "StudentHub | Registration Successful" : "StudentHub | Registration Failed";
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/style.css">
        <title><?= htmlspecialchars($title, ENT_QUOTES, "UTF-8") ?></title>
    </head>
    <body>
        <header class="site-header"><div class="nav-container"><a href="../pages/index.html" class="brand"><img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo"><span class="brand-name">StudentHub</span></a></div></header>
        <main><section>
            <h1><?= htmlspecialchars($heading, ENT_QUOTES, "UTF-8") ?></h1>
            <p><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
            <?php if ($success): ?><p>User has been registered securely using MySQLi.</p><?php endif; ?>
            <p><a href="../pages/register_user.php">Register Another User</a></p>
        </section></main>
        <footer><p>Welcome to StudentHub.</p></footer>
    </body>
    </html>
    <?php
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
    showRegistrationResult("Registration Failed", "This page only accepts registration forms submitted with POST.", false, 405);
    exit;
}

require_once __DIR__ . "/auth_check.php";
startStudentHubSession();
$submittedToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["registration_csrf_token"] ?? "";

if (!is_string($submittedToken) || !is_string($sessionToken) || $sessionToken === "" || !hash_equals($sessionToken, $submittedToken)) {
    showRegistrationResult("Registration Failed", "Your form session has expired. Please open the registration form and try again.", false, 403);
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (!is_string($name) || !is_string($email) || !is_string($password) || $name === "" || $email === "" || $password === "") {
    showRegistrationResult("Registration Failed", "All fields are required.", false, 422);
    exit;
}

$nameLength = function_exists("mb_strlen") ? mb_strlen($name, "UTF-8") : strlen($name);
$emailLength = function_exists("mb_strlen") ? mb_strlen($email, "UTF-8") : strlen($email);

if ($nameLength > 100 || $emailLength > 150 || strlen($password) > 4096) {
    showRegistrationResult("Registration Failed", "One or more fields are too long.", false, 422);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    showRegistrationResult("Registration Failed", "Please enter a valid email address.", false, 422);
    exit;
}

if (strlen($password) < 6) {
    showRegistrationResult("Registration Failed", "Password must contain at least 6 characters.", false, 422);
    exit;
}

require "db_connect_mysqli.php";

$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if ($checkStmt === false) {
    error_log("StudentHub registration duplicate check prepare failed: " . $conn->error);
    $conn->close();
    showRegistrationResult("Registration Failed", "Registration failed. Please try again later.", false, 500);
    exit;
}

$checkStmt->bind_param("s", $email);
if (!$checkStmt->execute()) {
    error_log("StudentHub registration duplicate check execute failed: " . $checkStmt->error);
    $checkStmt->close();
    $conn->close();
    showRegistrationResult("Registration Failed", "Registration failed. Please try again later.", false, 500);
    exit;
}

$checkStmt->store_result();
if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    showRegistrationResult("Registration Failed", "This email is already registered.", false, 409);
    exit;
}
$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if ($hashedPassword === false) {
    error_log("StudentHub registration password hashing failed.");
    $conn->close();
    showRegistrationResult("Registration Failed", "Registration failed. Please try again later.", false, 500);
    exit;
}

$role = "student";
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    error_log("StudentHub registration insert prepare failed: " . $conn->error);
    $conn->close();
    showRegistrationResult("Registration Failed", "Registration failed. Please try again later.", false, 500);
    exit;
}

$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
$registered = $stmt->execute();
$errorNumber = $stmt->errno;
if (!$registered) {
    error_log("StudentHub registration insert failed (MySQL error {$errorNumber}): " . $stmt->error);
}
$stmt->close();
$conn->close();

if (!$registered) {
    if ($errorNumber === 1062) {
        showRegistrationResult("Registration Failed", "This email is already registered.", false, 409);
    } else {
        showRegistrationResult("Registration Failed", "Registration failed. Please try again later.", false, 500);
    }
    exit;
}

unset($_SESSION["registration_csrf_token"]);
showRegistrationResult("Registration Successful!", "Your StudentHub account has been created.", true, 201);
