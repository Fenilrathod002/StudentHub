<?php

require "db_connect.php";

$id = $_GET["id"] ?? "";

if ($id === "" || !is_numeric($id)) {
    exit("Invalid student ID.");
}

try {

    // Get student information
    $sql = "SELECT
                students.id,
                students.name,
                students.email,
                students.mobile,
                students.role,
                students.course_id,
                students.year,
                students.gender
            FROM students
            WHERE students.id = :id";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":id" => $id
    ]);

    $student = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        exit("Student not found.");
    }


    // Get courses
    $courseStatement = $pdo->prepare(
        "SELECT course_id, course_name
         FROM courses
         ORDER BY course_name"
    );

    $courseStatement->execute();

    $courses = $courseStatement->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    exit("Database error: " . htmlspecialchars($e->getMessage()));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StudentHub | Edit Student</title>

    <link rel="stylesheet"
          href="../css/style.css">

</head>

<body>

    <header class="site-header">

        <div class="nav-container">

            <a href="../pages/index.html"
               class="brand">

                <img src="../images/logo.png"
                     alt="StudentHub Logo"
                     class="brand-logo">

                <span class="brand-name">
                    StudentHub
                </span>

            </a>

        </div>

    </header>


    <main>

        <section>

            <h2>Edit Student</h2>

            <form action="update_student.php"
                  method="POST">

                <input type="hidden"
                       name="id"
                       value="<?php echo htmlspecialchars($student["id"]); ?>">


                <label for="name">
                    Full Name
                </label>

                <input type="text"
                       id="name"
                       name="name"
                       value="<?php echo htmlspecialchars($student["name"]); ?>"
                       required>


                <label for="email">
                    Email Address
                </label>

                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($student["email"]); ?>"
                       required>


                <label for="mobile">
                    Mobile Number
                </label>

                <input type="tel"
                       id="mobile"
                       name="mobile"
                       value="<?php echo htmlspecialchars($student["mobile"]); ?>"
                       required>


                <label for="role">
                    Role
                </label>

                <select id="role"
                        name="role"
                        required>

                    <option value="student"
                        <?php echo $student["role"] === "student" ? "selected" : ""; ?>>
                        Student
                    </option>

                    <option value="teacher"
                        <?php echo $student["role"] === "teacher" ? "selected" : ""; ?>>
                        Teacher
                    </option>

                    <option value="admin"
                        <?php echo $student["role"] === "admin" ? "selected" : ""; ?>>
                        Administrator
                    </option>

                </select>


                <label for="course">
                    Course
                </label>

                <select id="course"
                        name="course_id"
                        required>

                    <?php foreach ($courses as $course): ?>

                        <option value="<?php echo htmlspecialchars($course["course_id"]); ?>"
                            <?php
                            echo $student["course_id"] == $course["course_id"]
                                ? "selected"
                                : "";
                            ?>>

                            <?php echo htmlspecialchars($course["course_name"]); ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <label for="year">
                    Year
                </label>

                <select id="year"
                        name="year"
                        required>

                    <option value="1"
                        <?php echo $student["year"] == 1 ? "selected" : ""; ?>>
                        1st Year
                    </option>

                    <option value="2"
                        <?php echo $student["year"] == 2 ? "selected" : ""; ?>>
                        2nd Year
                    </option>

                    <option value="3"
                        <?php echo $student["year"] == 3 ? "selected" : ""; ?>>
                        3rd Year
                    </option>

                    <option value="4"
                        <?php echo $student["year"] == 4 ? "selected" : ""; ?>>
                        4th Year
                    </option>

                </select>


                <label for="gender">
                    Gender
                </label>

                <select id="gender"
                        name="gender"
                        required>

                    <option value="Male"
                        <?php echo $student["gender"] === "Male" ? "selected" : ""; ?>>
                        Male
                    </option>

                    <option value="Female"
                        <?php echo $student["gender"] === "Female" ? "selected" : ""; ?>>
                        Female
                    </option>

                    <option value="Other"
                        <?php echo $student["gender"] === "Other" ? "selected" : ""; ?>>
                        Other
                    </option>

                </select>


                <button type="submit">
                    Update Student
                </button>

            </form>

            <p>
                <a href="view_registration.php">
                    Back to Registered Students
                </a>
            </p>

        </section>

    </main>


    <footer>

        <p>
            Welcome to StudentHub.
        </p>

    </footer>

</body>

</html>