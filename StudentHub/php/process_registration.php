<?php

require "db_connect.php";

// Step 1: Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request method.");
}

// Step 2: Read form data
$name = trim($_POST["full-name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$role = trim($_POST["role"] ?? "");
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");

$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm-password"] ?? "";

$termsAccepted = isset($_POST["terms"]);

// Step 3: Validation
$errors = [];

// Name
if ($name === "") {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
    $errors[] = "Name must contain letters and spaces only.";
}

// Email
if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// Mobile
if ($mobile === "") {
    $errors[] = "Mobile number is required.";
} elseif (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Mobile number must be 10 digits and start with 6-9.";
}

// Role
if ($role === "") {
    $errors[] = "Role is required.";
}

// Course
if ($course === "") {
    $errors[] = "Course is required.";
}

// Year
if ($year === "") {
    $errors[] = "Year is required.";
}

// Gender
if ($gender === "") {
    $errors[] = "Gender is required.";
}

// Password
if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

if (!preg_match("/[A-Z]/", $password)) {
    $errors[] = "Password must contain at least one uppercase letter.";
}

if (!preg_match("/[a-z]/", $password)) {
    $errors[] = "Password must contain at least one lowercase letter.";
}

if (!preg_match("/[0-9]/", $password)) {
    $errors[] = "Password must contain at least one digit.";
}

if (!preg_match("/[^A-Za-z0-9]/", $password)) {
    $errors[] = "Password must contain at least one special character.";
}

// Confirm password
if ($password !== $confirmPassword) {
    $errors[] = "Password and confirm password do not match.";
}

// Terms
if (!$termsAccepted) {
    $errors[] = "You must accept the terms and conditions.";
}


// Step 4: Display validation errors
if (!empty($errors)) {

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";

    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>StudentHub | Registration Failed</title>";
    echo "<link rel='stylesheet' href='../css/style.css'>";
    echo "</head>";

    echo "<body>";

    echo "<header class='site-header'>";
    echo "<div class='nav-container'>";

    echo "<a href='../pages/index.html' class='brand'>";
    echo "<img src='../images/logo.png' alt='StudentHub Logo' class='brand-logo'>";
    echo "<span class='brand-name'>StudentHub</span>";
    echo "</a>";

    echo "</div>";
    echo "</header>";

    echo "<main>";

    echo "<section>";

    echo "<h2>Registration Failed</h2>";
    echo "<p>Please correct the following errors:</p>";

    echo "<ul>";

    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }

    echo "</ul>";

    echo "<p>";
    echo "<a href='../pages/register.php'>Go Back to Registration</a>";
    echo "</p>";

    echo "</section>";

    echo "</main>";

    echo "<footer>";
    echo "<p>Welcome to StudentHub.</p>";
    echo "</footer>";

    echo "</body>";
    echo "</html>";

    exit;
}


// Step 5: Find Course ID
try {

    $courseQuery = "SELECT course_id FROM courses WHERE course_name = :course";

    $courseStatement = $pdo->prepare($courseQuery);

    $courseStatement->execute([
        ":course" => $course
    ]);

    $courseData = $courseStatement->fetch(PDO::FETCH_ASSOC);

    if (!$courseData) {
        exit("Selected course was not found in the database.");
    }

    $courseId = $courseData["course_id"];


// Step 6: Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);


// Step 7: Insert student into database
    $sql = "INSERT INTO students
            (name, email, mobile, role, course_id, year, gender, password)
            VALUES
            (:name, :email, :mobile, :role, :course_id, :year, :gender, :password)";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":name" => $name,
        ":email" => $email,
        ":mobile" => $mobile,
        ":role" => $role,
        ":course_id" => $courseId,
        ":year" => $year,
        ":gender" => $gender,
        ":password" => $passwordHash
    ]);


// Step 8: Success response
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";

    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>StudentHub | Registration Successful</title>";
    echo "<link rel='stylesheet' href='../css/style.css'>";
    echo "</head>";

    echo "<body>";

    echo "<header class='site-header'>";
    echo "<div class='nav-container'>";

    echo "<a href='../pages/index.html' class='brand'>";
    echo "<img src='../images/logo.png' alt='StudentHub Logo' class='brand-logo'>";
    echo "<span class='brand-name'>StudentHub</span>";
    echo "</a>";

    echo "</div>";
    echo "</header>";

    echo "<main>";

    echo "<section>";

    echo "<h2>Registration Successful</h2>";

    echo "<p>Your StudentHub registration has been completed successfully.</p>";

    echo "<p>";
    echo "<a href='../pages/register.html'>Register Another Student</a>";
    echo "</p>";

    echo "<p>";
    echo "<a href='view_registration.php'>View Registered Students</a>";
    echo "</p>";

    echo "</section>";

    echo "</main>";

    echo "<footer>";
    echo "<p>Welcome to StudentHub.</p>";
    echo "</footer>";

    echo "</body>";
    echo "</html>";

} catch (PDOException $e) {

    // Handle duplicate email
    if ($e->getCode() == 23000) {

        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";

        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>StudentHub | Registration Failed</title>";
        echo "<link rel='stylesheet' href='../css/style.css'>";
        echo "</head>";

        echo "<body>";

        echo "<header class='site-header'>";
        echo "<div class='nav-container'>";

        echo "<a href='../pages/index.html' class='brand'>";
        echo "<img src='../images/logo.png' alt='StudentHub Logo' class='brand-logo'>";
        echo "<span class='brand-name'>StudentHub</span>";
        echo "</a>";

        echo "</div>";
        echo "</header>";

        echo "<main>";

        echo "<section>";

        echo "<h2>Registration Failed</h2>";

        echo "<p>This email address is already registered.</p>";

        echo "<p>";
        echo "<a href='../pages/register.html'>Go Back to Registration</a>";
        echo "</p>";

        echo "</section>";

        echo "</main>";

        echo "<footer>";
        echo "<p>Welcome to StudentHub.</p>";
        echo "</footer>";

        echo "</body>";
        echo "</html>";

    } else {

        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
}

?>