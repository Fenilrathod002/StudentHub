<?php

require "../php/db_connect.php";

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
        "Unable to load events: "
        . htmlspecialchars($e->getMessage())
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/style.css">

    <title>StudentHub | Events</title>

</head>

<body>

    <header class="site-header">

        <div class="nav-container">

            <a href="index.html" class="brand">

                <img
                    src="../images/logo.png"
                    alt="StudentHub Logo"
                    class="brand-logo"
                >

                <span class="brand-name">
                    StudentHub
                </span>

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
                        <a href="events.php" class="nav-link active">
                            Events
                        </a>
                    </li>

                    <li>
                        <a href="students.html" class="nav-link">
                            Students
                        </a>
                    </li>

                    <li>
                        <a href="faq.html" class="nav-link">
                            FAQ
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

                <button
                    type="button"
                    id="theme-toggle"
                    class="theme-toggle"
                    aria-label="Switch to dark mode"
                    title="Switch to dark mode"
                >
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

        <section class="data-page-intro">

            <p class="eyebrow">
                Practical 8
            </p>

            <h1>
                Campus Events
            </h1>

            <p>
                Discover upcoming StudentHub events loaded from MySQL.
            </p>

        </section>

        <section class="data-section" id="events-page">

            <?php if (empty($events)): ?>

                <p class="data-status">
                    No events available.
                </p>

            <?php else: ?>

                <div class="data-card-grid">

                    <?php foreach ($events as $event): ?>

                        <article class="data-card">

                            <h2>
                                <?php
                                echo htmlspecialchars($event["title"]);
                                ?>
                            </h2>

                            <p>

                                <strong>
                                    Date:
                                </strong>

                                <?php
                                echo htmlspecialchars($event["event_date"]);
                                ?>

                            </p>

                            <p>

                                <strong>
                                    Location:
                                </strong>

                                <?php
                                echo htmlspecialchars($event["location"]);
                                ?>

                            </p>

                            <p>

                                <strong>
                                    Category:
                                </strong>

                                <?php
                                echo htmlspecialchars($event["category"]);
                                ?>

                            </p>

                            <a href="../php/event_register.php">

                                Register for Event

                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

    <footer>

        <p>
            Discover more campus opportunities with StudentHub.
        </p>

    </footer>

    <script src="../js/script.js"></script>

</body>

</html>