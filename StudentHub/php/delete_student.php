<?php

require "db_connect.php";

$id = $_GET["id"] ?? "";

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