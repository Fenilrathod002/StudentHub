<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);
require "db_connect.php";
$studentCsrfToken = htmlspecialchars(studentHubCsrfToken("student"), ENT_QUOTES, "UTF-8");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub | Registered Students</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <header class="site-header">

        <div class="nav-container">

            <a href="../pages/index.html" class="brand">

                <img src="../images/logo.png"
                    alt="StudentHub Logo"
                    class="brand-logo">

                <span class="brand-name">
                    StudentHub
                </span>

            </a>

            <nav class="main-navigation">

                <ul class="nav-list">

                    <li>
                        <a href="../pages/index.html" class="nav-link">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="../pages/about.html" class="nav-link">
                            About
                        </a>
                    </li>

                    <li>
                        <a href="../pages/students.html" class="nav-link">
                            Students
                        </a>
                    </li>

                </ul>

            </nav>

        </div>

    </header>


    <main>

        <section>

            <h2>Registered Students</h2>

            <?php

            try {

                // JOIN students with courses
                $sql = "SELECT
                            students.id,
                            students.name,
                            students.email,
                            students.mobile,
                            students.role,
                            courses.course_name,
                            students.year,
                            students.gender
                        FROM students
                        INNER JOIN courses
                        ON students.course_id = courses.course_id
                        ORDER BY students.id DESC";

                // Prepare SQL statement
                $statement = $pdo->prepare($sql);

                // Execute query
                $statement->execute();

                // Fetch all records
                $students = $statement->fetchAll(PDO::FETCH_ASSOC);


                if (empty($students)) {

                    echo "<p>No registered students found.</p>";
                } else {

                    echo "<div class='table-container'>";

                    echo "<table>";

                    echo "<thead>";

                    echo "<tr>";

                    echo "<th>ID</th>";
                    echo "<th>Name</th>";
                    echo "<th>Email</th>";
                    echo "<th>Mobile</th>";
                    echo "<th>Role</th>";
                    echo "<th>Course</th>";
                    echo "<th>Year</th>";
                    echo "<th>Gender</th>";
                    echo "<th>Actions</th>";

                    echo "</tr>";

                    echo "</thead>";


                    echo "<tbody>";

                    foreach ($students as $student) {

                        echo "<tr>";

                        echo "<td>"
                            . htmlspecialchars($student["id"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["name"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["email"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["mobile"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["role"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["course_name"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["year"])
                            . "</td>";

                        echo "<td>"
                            . htmlspecialchars($student["gender"])
                            . "</td>";

                        echo "<td>";

                        echo "<a href='edit_student.php?id="
                            . urlencode($student["id"])
                            . "'>
                            Edit
                            </a>";

                        echo " | ";

                        echo "<form action='delete_student.php' method='POST' class='inline-form' onsubmit=\"return confirm('Are you sure you want to delete this student?');\">"
                            . "<input type='hidden' name='csrf_token' value='" . $studentCsrfToken . "'>"
                            . "<input type='hidden' name='id' value='" . htmlspecialchars($student["id"], ENT_QUOTES, "UTF-8") . "'>"
                            . "<button type='submit'>Delete</button></form>";

                        echo "</td>";

                        echo "</tr>";
                    }

                    echo "</tbody>";

                    echo "</table>";

                    echo "</div>";
                }
            } catch (PDOException $e) {

                echo "<p>Unable to load registered students.</p>";

                echo "<p>"
                    . htmlspecialchars($e->getMessage())
                    . "</p>";
            }

            ?>

        </section>

    </main>


    <footer>

        <p>
            Welcome to StudentHub.
        </p>

    </footer>

</body>

</html>
