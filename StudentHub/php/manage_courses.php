<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);
require "db_connect.php";
$courseCsrfToken = htmlspecialchars(studentHubCsrfToken("course"), ENT_QUOTES, "UTF-8");

try {

    $statement = $pdo->prepare(
        "SELECT course_id, course_name
         FROM courses
         ORDER BY course_name"
    );

    $statement->execute();

    $courses = $statement->fetchAll(PDO::FETCH_ASSOC);

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

    <title>StudentHub | Manage Courses</title>

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

    <h2>Manage Courses</h2>

    <form action="add_course.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(studentHubCsrfToken('course'), ENT_QUOTES, 'UTF-8'); ?>">

        <label for="course-name">
            Course Name
        </label>

        <input
            type="text"
            id="course-name"
            name="course_name"
            required
        >

        <button type="submit">
            Add Course
        </button>

    </form>


    <h3>Available Courses</h3>

    <?php if (empty($courses)): ?>

        <p>No courses found.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($courses as $course): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $course["course_id"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $course["course_name"]
                            );
                            ?>
                        </td>

                        <td>

                            <form action="delete_course.php" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                <input type="hidden" name="csrf_token" value="<?php echo $courseCsrfToken; ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($course["course_id"], ENT_QUOTES, "UTF-8"); ?>">
                                <button type="submit">Delete</button>
                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

</main>


<footer>

    <p>
        Welcome to StudentHub.
    </p>

</footer>

</body>

</html>
