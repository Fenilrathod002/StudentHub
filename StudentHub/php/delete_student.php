<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

if (!verifyStudentHubCsrfToken("student", $_POST["csrf_token"] ?? null)) {
    http_response_code(403);
    exit("Invalid request token.");
}

$id = $_POST["id"] ?? "";

if ($id === "" || !is_numeric($id)) {
    exit("Invalid student ID.");
}

try {

    $sql = "DELETE FROM students
            WHERE id = :id";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":id" => $id
    ]);

    header("Location: view_registration.php");
    exit;

} catch (PDOException $e) {

    exit(
        "Unable to delete student: "
        . htmlspecialchars($e->getMessage())
    );
}

?>
