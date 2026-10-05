<?php

require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request method.");
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