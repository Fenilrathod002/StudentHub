<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

if (!verifyStudentHubCsrfToken("course", $_POST["csrf_token"] ?? null)) {
    http_response_code(403);
    exit("Invalid request token.");
}

$id = $_POST["id"] ?? "";

if ($id === "" || !is_numeric($id)) {
    exit("Invalid course ID.");
}

try {

    $sql = "DELETE FROM courses
            WHERE course_id = :id";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":id" => $id
    ]);

    header("Location: manage_courses.php");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {

        exit(
            "This course cannot be deleted because students "
            . "are currently associated with it."
        );
    }

    exit(
        "Unable to delete course: "
        . htmlspecialchars($e->getMessage())
    );
}

?>
