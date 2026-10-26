<?php

require_once __DIR__ . "/auth_check.php";
requireStudentHubRole("../pages/login_user.html", ["admin"]);
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

if (!verifyStudentHubCsrfToken("event_registration", $_POST["csrf_token"] ?? null)) {
    http_response_code(403);
    exit("Invalid request token.");
}

$studentId = $_POST["student_id"] ?? "";
$eventId = $_POST["event_id"] ?? "";


if ($studentId === "" || !is_numeric($studentId)) {
    exit("Please select a valid student.");
}

if ($eventId === "" || !is_numeric($eventId)) {
    exit("Please select a valid event.");
}


try {

    $sql = "INSERT INTO registrations
            (student_id, event_id)
            VALUES
            (:student_id, :event_id)";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ":student_id" => $studentId,
        ":event_id" => $eventId
    ]);


    header("Location: view_event_registrations.php");
    exit;

} catch (PDOException $e) {

    // Duplicate student-event registration
    if ($e->getCode() == 23000) {

        exit(
            "This student is already registered "
            . "for this event."
        );
    }

    exit(
        "Unable to register for event: "
        . htmlspecialchars($e->getMessage())
    );
}

?>
