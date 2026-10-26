<?php
require_once __DIR__ . '/practical_helpers.php';
practicalRequireAdmin(true);
require __DIR__ . '/db_connect_mysqli.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'GET') {
    $search = trim((string) ($_GET['search'] ?? ''));
    $course = filter_var($_GET['course_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $year = filter_var($_GET['year'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(50, max(5, (int) ($_GET['per_page'] ?? 10)));
    $where = ["s.role = 'student'"]; $types = ''; $values = [];
    if ($search !== '') { $where[] = '(s.name LIKE ? OR s.email LIKE ?)'; $like = "%{$search}%"; $types .= 'ss'; array_push($values, $like, $like); }
    if ($course) { $where[] = 's.course_id = ?'; $types .= 'i'; $values[] = $course; }
    if ($year >= 1 && $year <= 4) { $where[] = 's.year = ?'; $types .= 'i'; $values[] = $year; }
    $whereSql = implode(' AND ', $where);
    $count = $conn->prepare("SELECT COUNT(*) FROM students s WHERE {$whereSql}");
    if ($types) $count->bind_param($types, ...$values);
    $count->execute(); $total = (int) $count->get_result()->fetch_row()[0]; $count->close();
    $offset = ($page - 1) * $perPage;
    $query = $conn->prepare("SELECT s.id,s.name,s.email,s.mobile,s.course_id,s.year,s.gender,c.course_name FROM students s JOIN courses c ON c.course_id=s.course_id WHERE {$whereSql} ORDER BY s.id DESC LIMIT ? OFFSET ?");
    $listTypes = $types . 'ii'; $listValues = [...$values, $perPage, $offset];
    $query->bind_param($listTypes, ...$listValues); $query->execute();
    $rows = $query->get_result()->fetch_all(MYSQLI_ASSOC); $query->close();
    practicalJson(true, 'Students loaded.', ['students' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage]);
}
if (!in_array($method, ['POST', 'PUT', 'DELETE'], true)) practicalJson(false, 'Method not allowed.', null, 405);
$data = practicalInput();
if (!verifyStudentHubCsrfToken('student_api', $data['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null))) practicalJson(false, 'Invalid request token.', null, 403);
$id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
if ($method === 'DELETE') {
    if (!$id) practicalJson(false, 'Invalid student ID.', null, 422);
    $conn->begin_transaction();
    try {
        $check = $conn->prepare('SELECT id FROM students WHERE id=? AND role="student"'); $check->bind_param('i', $id); $check->execute();
        if (!$check->get_result()->fetch_assoc()) throw new RuntimeException('Student not found.'); $check->close();
        $registered = $conn->prepare('SELECT 1 FROM registrations WHERE student_id=? LIMIT 1'); $registered->bind_param('i', $id); $registered->execute();
        if ($registered->get_result()->fetch_assoc()) throw new RuntimeException('This student has event registrations and cannot be deleted.'); $registered->close();
        $delete = $conn->prepare('DELETE FROM students WHERE id=?'); $delete->bind_param('i', $id); if (!$delete->execute()) throw new RuntimeException('Could not delete student.'); $delete->close();
        if (!practicalAudit($conn, 'delete', 'student', $id, 'Student deleted')) throw new RuntimeException('Audit log could not be saved.');
        $conn->commit(); practicalJson(true, 'Student deleted.');
    } catch (Throwable $e) { $conn->rollback(); practicalJson(false, $e->getMessage(), null, 422); }
}
$name = trim((string) ($data['name'] ?? '')); $email = trim((string) ($data['email'] ?? '')); $mobile = trim((string) ($data['mobile'] ?? ''));
$courseId = filter_var($data['course_id'] ?? null, FILTER_VALIDATE_INT); $year = filter_var($data['year'] ?? null, FILTER_VALIDATE_INT); $gender = trim((string) ($data['gender'] ?? ''));
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[6-9][0-9]{9}$/', $mobile) || !$courseId || !$year || !in_array($gender, ['Male','Female','Other'], true)) practicalJson(false, 'Enter valid student details.', null, 422);
if ($method === 'POST') {
    $password = password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO students (name,email,mobile,role,course_id,year,gender,password) VALUES (?, ?, ?, "student", ?, ?, ?, ?)');
    $stmt->bind_param('sssiiss', $name,$email,$mobile,$courseId,$year,$gender,$password);
    if (!$stmt->execute()) practicalJson(false, $conn->errno === 1062 ? 'Email already exists.' : 'Could not create student.', null, 422);
    $newId=$conn->insert_id; $stmt->close(); if (!practicalAudit($conn,'create','student',$newId,'Student created')) practicalJson(false,'Student was created but could not be audited.',null,500);
    practicalJson(true, 'Student created.', ['id' => $newId], 201);
}
if ($method === 'PUT') {
    if (!$id) practicalJson(false,'Invalid student ID.',null,422);
    $stmt = $conn->prepare('UPDATE students SET name=?,email=?,mobile=?,course_id=?,year=?,gender=? WHERE id=? AND role="student"');
    $stmt->bind_param('sssiisi',$name,$email,$mobile,$courseId,$year,$gender,$id);
    if (!$stmt->execute()) practicalJson(false, $conn->errno === 1062 ? 'Email already exists.' : 'Could not update student.',null,422);
    if ($stmt->affected_rows < 0) practicalJson(false,'Student not found.',null,404); $stmt->close();
    if (!practicalAudit($conn,'update','student',$id,'Student updated')) practicalJson(false,'Student was updated but could not be audited.',null,500);
    practicalJson(true,'Student updated.');
}
practicalJson(false,'Method not allowed.',null,405);
