<?php

require "db_connect.php";

$id = $_GET["id"] ?? "";

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