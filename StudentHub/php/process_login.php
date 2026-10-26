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

function findStudentHubAccount(mysqli $conn, string $table, string $role, string $email): ?array
{
    $whereClause = "email = ?";
    if ($table === "students") {
        $whereClause .= " AND role = 'student'";
    }

    $statement = $conn->prepare("SELECT id, name, email, password FROM {$table} WHERE {$whereClause} LIMIT 1");
    if ($statement === false) {
        return null;
    }

    $statement->bind_param("s", $email);
    if (!$statement->execute()) {
        $statement->close();
        return null;
    }

    $statement->bind_result($id, $name, $storedEmail, $passwordHash);
    $account = $statement->fetch()
        ? [
            "id" => $id,
            "name" => $name,
            "email" => $storedEmail,
            "password" => $passwordHash,
            "role" => $role,
        ]
        : null;
    $statement->close();

    return $account ?: null;
}

$account = null;
$accountSources = [
    ["table" => "administrators", "role" => "admin"],
    ["table" => "teachers", "role" => "teacher"],
    ["table" => "students", "role" => "student"],
];
foreach ($accountSources as $source) {
    $candidate = findStudentHubAccount($conn, $source["table"], $source["role"], $email);
    $verified = $candidate !== null && password_verify($password, $candidate["password"]);

    if ($verified) {
        $account = $candidate;
        break;
    }
}
$conn->close();

if ($account === null) {
    header("Location: ../pages/login_user.html?error=invalid_credentials");
    exit;
}

if (!in_array($account["role"], ["admin", "student", "teacher"], true)) {
    error_log("StudentHub login rejected unsupported role for user ID " . (int) $account["id"]);
    header("Location: ../pages/login_user.html?error=invalid_credentials");
    exit;
}

session_regenerate_id(true);
$_SESSION["user_id"] = (int) $account["id"];
$_SESSION["user_name"] = $account["name"];
$_SESSION["user_email"] = $account["email"];
$_SESSION["user_role"] = $account["role"];
$_SESSION["last_activity"] = time();
$_SESSION["logout_csrf_token"] = bin2hex(random_bytes(32));

if ($account["role"] === "admin") {
    header("Location: ../pages/admin_dashboard.php");
    exit;
}

if ($account["role"] === "teacher") {
    header("Location: ../pages/teacher_dashboard.php");
    exit;
}

header("Location: ../pages/student_dashboard.php");
exit;
