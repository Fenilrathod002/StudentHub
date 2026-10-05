<?php

require "../php/db_connect.php";

$courseQuery = "SELECT course_id, course_name FROM courses ORDER BY course_name";

$courseStatement = $pdo->prepare($courseQuery);

$courseStatement->execute();

$courses = $courseStatement->fetchAll(PDO::FETCH_ASSOC);

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>StudentHub | Register</title>
</head>

<body>
    <header class="site-header">

        <div class="nav-container">

            <a href="index.html" class="brand">
                <img src="../images/logo.png" alt="StudentHub Logo" class="brand-logo">
                <span class="brand-name">StudentHub</span>
            </a>

            <nav class="main-navigation" aria-label="Main Navigation">

                <ul class="nav-list">

                    <li>
                        <a href="index.html" class="nav-link">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="about.html" class="nav-link">
                            About
                        </a>
                    </li>

                    <li>
                        <a href="academics.html" class="nav-link">
                            Academics
                        </a>
                    </li>

                    <li>
                        <a href="attendance.html" class="nav-link">
                            Attendance
                        </a>
                    </li>

                    <li>
                        <a href="communication.html" class="nav-link">
                            Communication
                        </a>
                    </li>

                    <li>
                        <a href="contact.html" class="nav-link">
                            Contact
                        </a>
                    </li>

                </ul>

            </nav>

            <div class="nav-actions">

                <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Switch to dark mode"
                    title="Switch to dark mode">
                    🌙
                </button>

                <a href="login.html" class="nav-login">
                    Login
                </a>

                <a href="register.php" class="nav-register">
                    Register
                </a>

            </div>


        </div>

    </header>

    <main>
        <section>
            <h2>Registration Form</h2>
            <form action="../php/process_registration.php" method="POST" novalidate>
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="full-name">

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email">

                <label for="mobile">Mobile Number</label>
                <input type="tel" id="mobile" name="mobile">

                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Administrator</option>
                </select>

                <label for="course">Course</label>

                <select id="course" name="course" required>

                    <option value="">Select Course</option>

                    <?php foreach ($courses as $course): ?>

                        <option value="<?php echo htmlspecialchars($course["course_name"]); ?>">
                            <?php echo htmlspecialchars($course["course_name"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label for="year">Year</label>
                <select id="year" name="year">
                    <option value="">Select Year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>

                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="password">Password</label>
                <input type="password" id="password" name="password">

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password">

                <label><input type="checkbox" name="terms" value="accepted"> I agree to the terms and
                    conditions.</label>

                <button type="submit">Register</button>
            </form>
            <p>Already registered? <a href="login.html">Sign in</a>.</p>
        </section>
    </main>

    <footer>
        <p>Welcome to StudentHub.</p>
    </footer>
    <script src="../js/script.js"></script>
</body>

</html>