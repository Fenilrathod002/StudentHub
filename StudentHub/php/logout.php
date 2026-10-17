<?php

require_once __DIR__ . "/auth_check.php";

startStudentHubSession();

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
    header("Location: ../pages/login_user.html");
    exit;
}

$submittedToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["logout_csrf_token"] ?? "";
if (!is_string($submittedToken) || !is_string($sessionToken) || $sessionToken === "" || !hash_equals($sessionToken, $submittedToken)) {
    http_response_code(403);
    exit("Unable to log out securely. Please return to your dashboard and try again.");
}

clearStudentHubSession();
header("Location: ../pages/login_user.html");
exit;
