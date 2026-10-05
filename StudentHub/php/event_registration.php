<?php

require "db_connect.php";

try {

    // Get students
    $studentStatement = $pdo->prepare(
        "SELECT id, name, email
         FROM students
         ORDER BY name"
    );

    $studentStatement->execute();

    $students = $studentStatement->fetchAll(PDO::FETCH_ASSOC);


    // Get events
    $eventStatement = $pdo->prepare(
        "SELECT event_id, title, event_date
         FROM events
         ORDER BY event_date"
    );

    $eventStatement->execute();

    $events = $eventStatement->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    exit(
        "Database error: "
        . htmlspecialchars($e->getMessage())
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StudentHub | Event Registration</title>

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

    <h2>Event Registration</h2>

    <form action="process_event_registration.php"
          method="POST">

        <label for="student">
            Student
        </label>

        <select id="student"
                name="student_id"
                required>

            <option value="">
                Select Student
            </option>

            <?php foreach ($students as $student): ?>

                <option value="<?php
                    echo htmlspecialchars($student["id"]);
                ?>">

                    <?php
                    echo htmlspecialchars(
                        $student["name"]
                    );
                    ?>

                    -
                    <?php
                    echo htmlspecialchars(
                        $student["email"]
                    );
                    ?>

                </option>

            <?php endforeach; ?>

        </select>


        <label for="event">
            Event
        </label>

        <select id="event"
                name="event_id"
                required>

            <option value="">
                Select Event
            </option>

            <?php foreach ($events as $event): ?>

                <option value="<?php
                    echo htmlspecialchars(
                        $event["event_id"]
                    );
                ?>">

                    <?php
                    echo htmlspecialchars(
                        $event["title"]
                    );
                    ?>

                    -
                    <?php
                    echo htmlspecialchars(
                        $event["event_date"]
                    );
                    ?>

                </option>

            <?php endforeach; ?>

        </select>


        <button type="submit">
            Register for Event
        </button>

    </form>

    <p>
        <a href="view_event_registrations.php">
            View Event Registrations
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