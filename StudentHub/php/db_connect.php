<?php

$host = "localhost";
$dbname = "studenthub";
$username = "root";
$dbPassword = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $dbPassword
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    error_log("StudentHub database connection failed: " . $e->getMessage());
    http_response_code(500);
    exit("Database connection failed. Please try again later.");

}

?>
