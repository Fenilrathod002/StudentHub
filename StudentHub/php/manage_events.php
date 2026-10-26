<?php
require_once __DIR__ . '/practical_helpers.php';
practicalRequireAdmin();
require __DIR__ . '/db_connect_mysqli.php';
$notice = '';
$error = '';
$editing = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_event'])) {
  if (!verifyStudentHubCsrfToken('event', $_POST['csrf_token'] ?? null)) {
    $error = 'Invalid request token.';
  } else {
    $id = filter_var($_POST['event_id'] ?? null, FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'open';
    if ($title === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $location === '' || $category === '' || !in_array($status, ['open', 'closed'], true)) {
      $error = 'Enter all required event details.';
    } else {
      $uploadError = null;
      $newPoster = practicalEventUpload($_FILES['poster'] ?? null, $uploadError);
      if ($uploadError) {
        $error = $uploadError;
      } else {
        try {
          $conn->begin_transaction();
          if ($id) {
            $old = $conn->prepare('SELECT poster_path FROM events WHERE event_id=?');
            $old->bind_param('i', $id);
            $old->execute();
            $previous = $old->get_result()->fetch_assoc();
            if (!$previous) throw new RuntimeException('Event not found.');
            $old->close();
            if ($newPoster) {
              $stmt = $conn->prepare('UPDATE events SET title=?,description=?,event_date=?,location=?,category=?,status=?,poster_path=? WHERE event_id=?');
              $stmt->bind_param('sssssssi', $title, $description, $date, $location, $category, $status, $newPoster, $id);
            } else {
              $stmt = $conn->prepare('UPDATE events SET title=?,description=?,event_date=?,location=?,category=?,status=? WHERE event_id=?');
              $stmt->bind_param('ssssssi', $title, $description, $date, $location, $category, $status, $id);
            }
            if (!$stmt->execute()) throw new RuntimeException('Could not update event.');
            $stmt->close();
            if (!practicalAudit($conn, 'update', 'event', $id, 'Event updated')) throw new RuntimeException('Audit log could not be saved.');
            $conn->commit();
            if ($newPoster) practicalDeletePoster($previous['poster_path']);
            $notice = 'Event updated.';
          } else {
            $stmt = $conn->prepare('INSERT INTO events (title,description,event_date,location,category,status,poster_path) VALUES (?,?,?,?,?,?,?)');
            $stmt->bind_param('sssssss', $title, $description, $date, $location, $category, $status, $newPoster);
            if (!$stmt->execute()) throw new RuntimeException('Could not create event.');
            $newId = $conn->insert_id;
            $stmt->close();
            if (!practicalAudit($conn, 'create', 'event', $newId, 'Event created')) throw new RuntimeException('Audit log could not be saved.');
            $conn->commit();
            $notice = 'Event created.';
          }
        } catch (Throwable $e) {
          $conn->rollback();
          if ($newPoster) practicalDeletePoster($newPoster);
          $error = $e->getMessage();
        }
      }
    }
  }
}
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
  $stmt = $conn->prepare('SELECT * FROM events WHERE event_id=?');
  $id = (int)$_GET['edit'];
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $editing = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}
if (isset($_POST['delete_event']) && verifyStudentHubCsrfToken('event', $_POST['csrf_token'] ?? null)) {
  $id = (int)$_POST['delete_event'];
  $conn->begin_transaction();
  try {
    $stmt = $conn->prepare('SELECT poster_path FROM events WHERE event_id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    if (!$event) throw new RuntimeException('Event not found.');
    $check = $conn->prepare('SELECT 1 FROM registrations WHERE event_id=? LIMIT 1');
    $check->bind_param('i', $id);
    $check->execute();
    if ($check->get_result()->fetch_assoc()) throw new RuntimeException('This event has registrations and cannot be deleted.');
    $del = $conn->prepare('DELETE FROM events WHERE event_id=?');
    $del->bind_param('i', $id);
    $del->execute();
    if (!practicalAudit($conn, 'delete', 'event', $id, 'Event deleted')) throw new RuntimeException('Audit log could not be saved.');
    $conn->commit();
    practicalDeletePoster($event['poster_path']);
    $notice = 'Event deleted.';
  } catch (Throwable $e) {
    $conn->rollback();
    $error = $e->getMessage();
  }
}
$stmt = $conn->prepare('SELECT * FROM events ORDER BY event_date DESC');
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$token = htmlspecialchars(studentHubCsrfToken('event'), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>StudentHub | Manage Events</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <header class="site-header">
    <div class="nav-container"><a class="brand" href="../pages/admin_dashboard.php"><img class="brand-logo" src="../images/logo.png" alt="StudentHub"><span class="brand-name">StudentHub</span></a>
      <nav class="main-navigation">
        <ul class="nav-list">
          <li><a class="nav-link" href="manage_students.php">Manage Students</a></li>
          <li><a class="nav-link active" href="manage_events.php">Manage Events</a></li>
          <li><a class="nav-link" href="audit_logs.php">Audit Logs</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
    <section>
      <p class="eyebrow">Practical 12</p>
      <h1><?= $editing ? 'Edit' : 'Add' ?> Event</h1><?php if ($notice): ?><p class="data-status"><?= htmlspecialchars($notice) ?></p><?php endif ?><?php if ($error): ?><p class="auth-message"><?= htmlspecialchars($error) ?></p><?php endif ?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= $token ?>"><input type="hidden" name="event_id" value="<?= (int)($editing['event_id'] ?? 0) ?>"><label>Title<input name="title" required value="<?= htmlspecialchars($editing['title'] ?? '') ?>"></label><label>Description<textarea name="description"><?= htmlspecialchars($editing['description'] ?? '') ?></textarea></label><label>Date<input name="event_date" type="date" required value="<?= htmlspecialchars($editing['event_date'] ?? '') ?>"></label><label>Location<input name="location" required value="<?= htmlspecialchars($editing['location'] ?? '') ?>"></label><label>Category<input name="category" required value="<?= htmlspecialchars($editing['category'] ?? '') ?>"></label><label>Status<select name="status">
            <option value="open" <?= ($editing['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
            <option value="closed" <?= ($editing['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
          </select></label><label>Poster (JPEG, PNG or WebP; 2 MB max)<input type="file" name="poster" accept="image/jpeg,image/png,image/webp"></label><button class="btn-primary" type="submit">Save Event</button><?php if ($editing): ?><a class="btn-secondary" href="manage_events.php">Cancel</a><?php endif ?></form>
    </section>
    <section>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Poster</th>
              <th>Title</th>
              <th>Date</th>
              <th>Location</th>
              <th>Category</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody><?php foreach ($events as $e): ?><tr>
                <td><?php if ($e['poster_path']): ?><img style="max-width:90px" src="../<?= htmlspecialchars($e['poster_path']) ?>" alt="Event poster"><?php endif ?></td>
                <td><?= htmlspecialchars($e['title']) ?></td>
                <td><?= htmlspecialchars($e['event_date']) ?></td>
                <td><?= htmlspecialchars($e['location']) ?></td>
                <td><?= htmlspecialchars($e['category']) ?></td>
                <td><?= htmlspecialchars($e['status']) ?></td>
                <td><a href="?edit=<?= (int)$e['event_id'] ?>">Edit</a>
                  <form method="post" class="inline-form" onsubmit="return confirm('Delete this event?')"><input type="hidden" name="csrf_token" value="<?= $token ?>"><button name="delete_event" value="<?= (int)$e['event_id'] ?>">Delete</button></form>
                </td>
              </tr><?php endforeach ?></tbody>
        </table>
      </div>
    </section>
  </main>
</body>

</html>
