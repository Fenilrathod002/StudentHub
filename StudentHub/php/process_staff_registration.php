<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);

function showStaffRegistrationResult(string $heading, string $message, int $statusCode): void
{
    http_response_code($statusCode);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="../css/style.css"><title>StudentHub | Staff Registration</title></head>
    <body><main><section><h1><?= htmlspecialchars($heading, ENT_QUOTES, "UTF-8") ?></h1><p><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><p><a href="../pages/register_staff.php">Create another staff account</a></p><p><a href="../pages/admin_dashboard.php">Back to Admin Dashboard</a></p></section></main></body>
    </html>
    <?php
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
    showStaffRegistrationResult("Registration Failed", "This page only accepts POST requests.", 405);
    exit;
}

$submittedToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["staff_registration_csrf_token"] ?? "";
if (!is_string($submittedToken) || !is_string($sessionToken) || $sessionToken === "" || !hash_equals($sessionToken, $submittedToken)) {
    showStaffRegistrationResult("Registration Failed", "Your form session has expired. Please try again.", 403);
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$role = trim($_POST["role"] ?? "");
$password = $_POST["password"] ?? "";

if (!is_string($name) || !is_string($email) || !is_string($role) || !is_string($password) || $name === "" || $email === "" || $password === "") {
    showStaffRegistrationResult("Registration Failed", "All fields are required.", 422);
    exit;
}

if ((function_exists("mb_strlen") ? mb_strlen($name, "UTF-8") : strlen($name)) > 100 || (function_exists("mb_strlen") ? mb_strlen($email, "UTF-8") : strlen($email)) > 150 || strlen($password) > 4096 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    showStaffRegistrationResult("Registration Failed", "Please provide valid account details. Passwords must contain at least 6 characters.", 422);
    exit;
}

$tableByRole = ["teacher" => "teachers", "admin" => "administrators"];
if (!array_key_exists($role, $tableByRole)) {
    showStaffRegistrationResult("Registration Failed", "Please select Teacher or Administrator.", 422);
    exit;
}

require "db_connect_mysqli.php";
$accountTables = ["students", "teachers", "administrators"];
foreach ($accountTables as $accountTable) {
    $check = $conn->prepare("SELECT id FROM {$accountTable} WHERE email = ? LIMIT 1");
    if ($check === false) {
        error_log("StudentHub staff registration duplicate check failed: " . $conn->error);
        $conn->close();
        showStaffRegistrationResult("Registration Failed", "Registration failed. Please try again later.", 500);
        exit;
    }
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();
    if ($exists) {
        $conn->close();
        showStaffRegistrationResult("Registration Failed", "This email is already registered.", 409);
        exit;
    }
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if ($passwordHash === false) {
    $conn->close();
    showStaffRegistrationResult("Registration Failed", "Registration failed. Please try again later.", 500);
    exit;
}

$targetTable = $tableByRole[$role];
$statement = $conn->prepare("INSERT INTO {$targetTable} (name, email, password) VALUES (?, ?, ?)");
if ($statement === false) {
    error_log("StudentHub staff registration insert prepare failed: " . $conn->error);
    $conn->close();
    showStaffRegistrationResult("Registration Failed", "Registration failed. Please try again later.", 500);
    exit;
}

$statement->bind_param("sss", $name, $email, $passwordHash);
$registered = $statement->execute();
if (!$registered) {
    error_log("StudentHub staff registration insert failed: " . $statement->error);
}
$statement->close();
$conn->close();

if (!$registered) {
    showStaffRegistrationResult("Registration Failed", "Registration failed. Please try again later.", 500);
    exit;
}

unset($_SESSION["staff_registration_csrf_token"]);
showStaffRegistrationResult("Registration Successful", ucfirst($role) . " account created successfully.", 201);
