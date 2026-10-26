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
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$role = trim($_POST["role"] ?? "");
$courseId = $_POST["course_id"] ?? "";
$year = $_POST["year"] ?? "";
$gender = trim($_POST["gender"] ?? "");


if ($id === "" || !is_numeric($id)) {
    exit("Invalid student ID.");
}

if ($name === "") {
    exit("Name is required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Please enter a valid email address.");
}

if (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    exit("Invalid mobile number.");
}

if ($courseId === "" || !is_numeric($courseId)) {
    exit("Invalid course.");
}

if ($year === "") {
    exit("Year is required.");
}

if (!in_array((string) $year, ["1", "2", "3", "4"], true)) {
    exit("Invalid year.");
}

if (!in_array($role, ["student", "teacher", "admin"], true)) {
    exit("Invalid role.");
}

if (!in_array($gender, ["Male", "Female", "Other"], true)) {
    exit("Invalid gender.");
}


try {

    $sql = "UPDATE students
            SET
                name = :name,
                email = :email,
                mobile = :mobile,
                role = :role,
                course_id = :course_id,
                year = :year,
                gender = :gender
            WHERE id = :id";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":name" => $name,
        ":email" => $email,
        ":mobile" => $mobile,
        ":role" => $role,
        ":course_id" => $courseId,
        ":year" => $year,
        ":gender" => $gender,
        ":id" => $id
    ]);


    header("Location: view_registration.php");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {

        exit("This email address is already registered.");

    }

    exit(
        "Unable to update student: "
        . htmlspecialchars($e->getMessage())
    );
}

?>
