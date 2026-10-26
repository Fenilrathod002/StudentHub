<?php

const STUDENTHUB_SESSION_TIMEOUT = 1200;

function startStudentHubSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $cookieOptions = [
            "lifetime" => 0,
            "path" => "/",
            "secure" => (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off"),
            "httponly" => true,
            "samesite" => "Lax",
        ];
        session_set_cookie_params($cookieOptions);
        session_start();
    }
}

function clearStudentHubSession(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", [
            "expires" => time() - 42000,
            "path" => $params["path"] ?? "/",
            "domain" => $params["domain"] ?? "",
            "secure" => $params["secure"] ?? false,
            "httponly" => $params["httponly"] ?? true,
            "samesite" => $params["samesite"] ?? "Lax",
        ]);
    }

    session_destroy();
}

function requireStudentHubAuthentication(string $loginPath): void
{
    startStudentHubSession();

    if (empty($_SESSION["user_id"]) || empty($_SESSION["user_role"])) {
        header("Location: " . $loginPath);
        exit;
    }

    $lastActivity = $_SESSION["last_activity"] ?? 0;
    if (!is_int($lastActivity) && !ctype_digit((string) $lastActivity)) {
        clearStudentHubSession();
        header("Location: " . $loginPath . "?timeout=1");
        exit;
    }

    if ((time() - (int) $lastActivity) > STUDENTHUB_SESSION_TIMEOUT) {
        clearStudentHubSession();
        header("Location: " . $loginPath . "?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
}

function studentHubLogoutToken(): string
{
    if (empty($_SESSION["logout_csrf_token"])) {
        $_SESSION["logout_csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["logout_csrf_token"];
}

function studentHubCsrfToken(string $purpose): string
{
    $key = "csrf_" . $purpose;
    if (empty($_SESSION[$key])) {
        $_SESSION[$key] = bin2hex(random_bytes(32));
    }

    return $_SESSION[$key];
}

function verifyStudentHubCsrfToken(string $purpose, mixed $submittedToken): bool
{
    $key = "csrf_" . $purpose;
    $sessionToken = $_SESSION[$key] ?? "";

    return is_string($submittedToken) && is_string($sessionToken)
        && $sessionToken !== "" && hash_equals($sessionToken, $submittedToken);
}

function requireStudentHubRole(string $loginPath, array $allowedRoles): void
{
    requireStudentHubAuthentication($loginPath);

    if (!in_array($_SESSION["user_role"], $allowedRoles, true)) {
        http_response_code(403);
        exit("You do not have permission to access this page.");
    }
}

function redirectStudentHubAuthenticatedUser(): void
{
    startStudentHubSession();

    $role = $_SESSION["user_role"] ?? "";
    if (empty($_SESSION["user_id"]) || !in_array($role, ["student", "teacher", "admin"], true)) {
        return;
    }

    $lastActivity = $_SESSION["last_activity"] ?? 0;
    if ((!is_int($lastActivity) && !ctype_digit((string) $lastActivity)) || (time() - (int) $lastActivity) > STUDENTHUB_SESSION_TIMEOUT) {
        clearStudentHubSession();
        return;
    }

    $_SESSION["last_activity"] = time();
    $dashboardPaths = [
        "student" => "student_dashboard.php",
        "teacher" => "teacher_dashboard.php",
        "admin" => "admin_dashboard.php",
    ];

    header("Location: " . $dashboardPaths[$role]);
    exit;
}
