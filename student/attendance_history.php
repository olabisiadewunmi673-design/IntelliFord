<?php
require_once '../auth.php';
requireRole('student');

include '../config.php';

$student_id = $_SESSION['student_id'];
$course_filter = $conn->real_escape_string($_GET['course'] ?? '');

/* ==========================
   FETCH ATTENDANCE
========================== */
$attendance = $conn->query("
    SELECT c.code, c.name, a.status, a.marked_at
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    WHERE a.student_id = $student_id
    AND (c.code LIKE '%$course_filter%' OR c.name LIKE '%$course_filter%')
    ORDER BY a.marked_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Attendance</title>
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

<h4 class="text-center mb-4">Student Panel</h4>
<hr>

<a href="dashboard.php">Dashboard</a>
<a href="attendance_history.php" class="active">My Attendance</a>
<a href="#">Notifications</a>
<a href="#">Profile</a>
<a href="../logout.php" class="mt-5 text-danger">Logout</a>

</div>

<!-- ==========================
     MAIN CONTENT
========================== -->
<div class="main-content">

<h2 class="mb-4">My Attendance History</h2>

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

<!-- TABLE -->
<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>Course</th>
<th>Status</th>
<th>Date</th>
<th>Time</th>
</tr>
</thead>

<tbody>

<?php if($attendance && $attendance->num_rows > 0): ?>
<?php while($row = $attendance->fetch_assoc()): ?>
<tr>

<td><?= $row['code'] ?> - <?= $row['name'] ?></td>

<td>
<?php if($row['status'] == 'present'): ?>
<span class="badge bg-success">Present</span>
<?php else: ?>
<span class="badge bg-danger">Absent</span>
<?php endif; ?>
</td>

<td><?= date("d M Y", strtotime($row['marked_at'])) ?></td>
<td><?= date("H:i:s", strtotime($row['marked_at'])) ?></td>

</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="4" class="text-center">No records found</td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>

</body>
</html>