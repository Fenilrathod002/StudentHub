<?php

require "db_connect.php";

try {

    $sql = "SELECT
                event_id,
                title,
                event_date,
                location,
                category
            FROM events
            ORDER BY event_date ASC";

    $statement = $pdo->prepare($sql);

    $statement->execute();

    $events = $statement->fetchAll(PDO::FETCH_ASSOC);

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

    <title>StudentHub | Events</title>

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

    <h2>StudentHub Events</h2>

    <?php if (empty($events)): ?>

        <p>No events found.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Category</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($events as $event): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $event["event_id"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $event["title"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $event["event_date"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $event["location"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $event["category"]
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