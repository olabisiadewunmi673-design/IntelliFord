<?php
require_once '../auth.php';
requireRole('lecturer');

include '../config.php';

$lecturer_id = $_SESSION['user_id'];

/* ==========================
   INPUTS
========================== */
$course_filter = $conn->real_escape_string($_GET['course'] ?? '');
$session_id = intval($_GET['session'] ?? 0);
$student_search = $conn->real_escape_string($_GET['student_search'] ?? '');

/* ==========================
   FETCH SESSIONS
========================== */
$sessions = $conn->query("
    SELECT s.id, s.created_at,
           c.code, c.name,
           COUNT(DISTINCT a.student_id) as total_present
    FROM attendance_sessions s
    JOIN courses c ON s.course_id = c.id
    LEFT JOIN attendance a ON a.session_id = s.id
    WHERE c.lecturer_id = $lecturer_id
    AND (c.code LIKE '%$course_filter%' OR c.name LIKE '%$course_filter%')
    GROUP BY s.id
    ORDER BY s.id DESC
");

/* ==========================
   FETCH STUDENTS
========================== */
$students = null;

if ($session_id > 0) {
    $students = $conn->query("
        SELECT u.full_name, s.reg_number, a.marked_at
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN attendance_sessions sess ON a.session_id = sess.id
        JOIN courses c ON sess.course_id = c.id
        WHERE a.session_id = $session_id
        AND c.lecturer_id = $lecturer_id
        AND u.full_name LIKE '%$student_search%'
        ORDER BY a.marked_at ASC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:#f8f9fa;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#343a40;
    color:white;
    padding:20px;
}

.sidebar a{
    color:white;
    display:block;
    padding:10px;
    text-decoration:none;
    border-radius:5px;
}

.sidebar a:hover{
    background:#495057;
}

/* CONTENT */
.main-content{
    margin-left:250px;
    padding:30px;
}
</style>

</head>

<body>

<!-- ==========================
     SIDEBAR
========================== -->
<div class="sidebar">

<h4 class="text-center mb-4">Lecturer Panel</h4>
<hr>

<a href="dashboard.php">Dashboard</a>
<a href="#">My Courses</a>
<a href="attendance_history.php" class="active">Attendance History</a>
<a href="#">Reports</a>
<a href="../logout.php" class="mt-5 text-danger">Logout</a>

</div>

<!-- ==========================
     MAIN CONTENT
========================== -->
<div class="main-content">

<h2 class="mb-4">Attendance History</h2>

<!-- SEARCH -->
<form method="GET" class="mb-4">
<div class="row">

<div class="col-md-10">
<input type="text" name="course" class="form-control"
placeholder="Search by course..."
value="<?= htmlspecialchars($course_filter) ?>">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Search</button>
</div>

</div>
</form>

<!-- SESSION TABLE -->
<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Course</th>
<th>Date</th>
<th>Present</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php if($sessions && $sessions->num_rows > 0): ?>
<?php $i=1; while($row = $sessions->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= $row['code'] ?> - <?= $row['name'] ?></td>
<td><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></td>
<td><?= $row['total_present'] ?></td>
<td>
<a href="?session=<?= $row['id'] ?>&course=<?= $course_filter ?>" 
class="btn btn-primary btn-sm">
View Students
</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center">No sessions found</td></tr>
<?php endif; ?>
</tbody>
</table>

<?php if ($session_id > 0): ?>

<hr>

<h4 class="mt-4">Students Present</h4>

<!-- STUDENT SEARCH -->
<form method="GET" class="mb-3">
<input type="hidden" name="session" value="<?= $session_id ?>">
<input type="hidden" name="course" value="<?= $course_filter ?>">

<div class="input-group">
<input type="text" name="student_search" class="form-control"
placeholder="Search student..."
value="<?= htmlspecialchars($student_search) ?>">
<button class="btn btn-secondary">Search</button>
</div>
</form>

<!-- STUDENT TABLE -->
<table class="table table-striped">
<thead class="table-dark">
<tr>
<th>Name</th>
<th>Reg Number</th>
<th>Time</th>
</tr>
</thead>

<tbody>
<?php if($students && $students->num_rows > 0): ?>
<?php while($st = $students->fetch_assoc()): ?>
<tr>
<td><?= $st['full_name'] ?></td>
<td><?= $st['reg_number'] ?></td>
<td><?= date("H:i:s", strtotime($st['marked_at'])) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="3" class="text-center">No students found</td></tr>
<?php endif; ?>
</tbody>
</table>

<?php endif; ?>

</div>

</body>
</html>