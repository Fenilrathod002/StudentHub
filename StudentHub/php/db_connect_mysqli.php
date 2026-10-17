<?php

// This MySQLi connection is shared by Practical 9 registration and Practical 10 authentication.
$host = "localhost";
$username = "root";
$password = "";
$dbname = "studenthub";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("StudentHub registration database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("Database connection failed. Please try again later.");
}

if (!$conn->set_charset("utf8mb4")) {
    error_log("StudentHub registration could not set utf8mb4: " . $conn->error);
    $conn->close();
    http_response_code(500);
    exit("Database connection failed. Please try again later.");
}

