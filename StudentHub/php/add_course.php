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

$courseName = trim($_POST["course_name"] ?? "");

if ($courseName === "") {
    exit("Course name is required.");
}

try {

    $sql = "INSERT INTO courses (course_name)
            VALUES (:course_name)";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":course_name" => $courseName
    ]);

    header("Location: manage_courses.php");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        exit("This course already exists.");
    }

    exit(
        "Unable to add course: "
        . htmlspecialchars($e->getMessage())
    );
}

?>
