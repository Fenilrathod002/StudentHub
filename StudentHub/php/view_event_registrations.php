<?php

require "db_connect.php";

try {

    $sql = "SELECT
                registrations.reg_id,
                students.name AS student_name,
                students.email,
                events.title AS event_title,
                events.event_date,
                events.location,
                registrations.registered_on

            FROM registrations

            INNER JOIN students
                ON registrations.student_id = students.id

            INNER JOIN events
                ON registrations.event_id = events.event_id

            ORDER BY registrations.registered_on DESC";


    $statement = $pdo->prepare($sql);

    $statement->execute();

    $registrations = $statement->fetchAll(PDO::FETCH_ASSOC);

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

    <title>StudentHub | Event Registrations</title>

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

    <h2>Event Registrations</h2>

    <p>
        <a href="event_register.php">
            Register Student for Event
        </a>
    </p>


    <?php if (empty($registrations)): ?>

        <p>
            No event registrations found.
        </p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Student</th>

                        <th>Email</th>

                        <th>Event</th>

                        <th>Date</th>

                        <th>Location</th>

                        <th>Registered On</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($registrations as $registration): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["reg_id"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["student_name"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["email"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["event_title"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["event_date"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["location"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["registered_on"]
                            );
                            ?>
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