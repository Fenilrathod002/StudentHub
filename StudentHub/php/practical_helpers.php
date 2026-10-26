<?php
// Shared helpers for Practicals 11–14. All new database work uses MySQLi.
require_once __DIR__ . '/auth_check.php';

function practicalRequireAdmin(bool $json = false): void
{
    startStudentHubSession();
    $loggedIn = !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin';
    if ($loggedIn) {
        $last = $_SESSION['last_activity'] ?? 0;
        $loggedIn = (is_int($last) || ctype_digit((string) $last))
            && (time() - (int) $last) <= STUDENTHUB_SESSION_TIMEOUT;
    }
    if (!$loggedIn) {
        if ($json) {
            practicalJson(false, 'Administrator access is required.', null, 403);
        }
        header('Location: ../pages/login_user.html');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function practicalJson(bool $success, string $message, mixed $data = null, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function practicalInput(): array
{
    $input = json_decode(file_get_contents('php://input'), true);
    return is_array($input) ? $input : $_POST;
}

function practicalAudit(mysqli $conn, string $action, string $entity, int $recordId, string $description = ''): bool
{
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId < 1) return false;
    $stmt = $conn->prepare('INSERT INTO audit_logs (actor_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) return false;
    $stmt->bind_param('issis', $userId, $action, $entity, $recordId, $description);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function practicalEventUpload(?array $file, ?string &$error): ?string
{
    $error = null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) { $error = 'Poster upload failed.'; return null; }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) { $error = 'Poster must be 2 MB or smaller.'; return null; }
    $image = @getimagesize($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = $image['mime'] ?? '';
    if (!isset($allowed[$mime])) { $error = 'Use a valid JPEG, PNG, or WebP image.'; return null; }
    $directory = dirname(__DIR__) . '/uploads/event-posters';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) { $error = 'Poster storage is unavailable.'; return null; }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) { $error = 'Could not save the poster.'; return null; }
    return 'uploads/event-posters/' . $filename;
}

function practicalDeletePoster(?string $path): void
{
    if (!$path || !preg_match('#^uploads/event-posters/[a-f0-9]{32}\.(jpg|png|webp)$#', $path)) return;
    $full = dirname(__DIR__) . '/' . $path;
    if (is_file($full)) unlink($full);
}
