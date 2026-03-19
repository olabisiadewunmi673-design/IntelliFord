<?php
// student/dashboard.php

require_once '../auth.php';
requireRole('student');

include_once '../config.php';

$student_id = $_SESSION['student_id'] ?? 0;

if (!$student_id) {
    header("Location: ../index.php?error=invalid_session");
    exit;
}

// Overall stats
$present = $conn->query("SELECT COUNT(*) as cnt FROM attendance WHERE student_id = $student_id AND status = 'present'")->fetch_assoc()['cnt'] ?? 0;
$total = $conn->query("SELECT COUNT(*) as cnt FROM attendance WHERE student_id = $student_id")->fetch_assoc()['cnt'] ?? 0;
$missed = $total - $present;
$percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

// Today's attendance
$today_present = $conn->query("SELECT COUNT(*) as cnt FROM attendance WHERE student_id = $student_id AND DATE(marked_at) = CURDATE() AND status = 'present'")->fetch_assoc()['cnt'] ?? 0;

// Course breakdown
$courses = $conn->query("
    SELECT c.code, c.name,
           COUNT(*) as total,
           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    WHERE a.student_id = $student_id
    GROUP BY c.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard - IntelliFord</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://unpkg.com/html5-qrcode"></script>

<style>
body { background:#f8f9fa; }

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

.sidebar a:hover,.sidebar a.active{
background:#495057;
}

.main-content{
margin-left:250px;
padding:30px;
}

.card{
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

#reader{
width:350px;
margin:auto;
}

.scan-btn{
font-size:20px;
padding:15px 30px;
}

</style>
</head>

<body>

<div class="sidebar">
<h4 class="text-center mb-4">Student Panel</h4>
<hr>

<a href="dashboard.php" class="active">Dashboard</a>
<a href="attendance_history.php">My Attendance</a>
<a href="#">Class Schedule</a>
<a href="#">Notifications</a>
<a href="#">Profile</a>
<a href="../logout.php" class="mt-5 text-danger">Logout</a>

</div>

<div class="main-content">

<h2 class="text-center mb-4">
Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Student'); ?>
</h2>

<!-- SCAN QR BUTTON -->

<div class="text-center mb-4">

<button class="btn btn-primary scan-btn" onclick="startScanner()">
📷 Scan QR Code For Attendance
</button>

</div>

<div id="reader"></div>

<div id="scan-result" class="text-center mt-3"></div>

<!-- Attendance Progress -->

<div class="card p-4 mb-4 text-center">

<h4>Your Overall Attendance</h4>

<div class="progress mb-3">

<div class="progress-bar 
<?php 
if ($percentage >= 75) echo 'bg-success';
elseif ($percentage >= 50) echo 'bg-warning';
else echo 'bg-danger';
?>"

style="width: <?php echo $percentage; ?>%;">

<?php echo $percentage; ?>%

</div>
</div>

</div>

<div class="alert alert-info text-center mb-4">

<strong>Today:</strong> You marked present for
<strong><?php echo $today_present; ?></strong> class(es)

</div>

<?php if ($courses->num_rows > 0): ?>

<h4 class="text-center mb-3">Attendance by Course</h4>

<table class="table table-striped">

<thead class="table-dark">

<tr>
<th>Course</th>
<th>Total Sessions</th>
<th>Present</th>
<th>Percentage</th>
</tr>

</thead>

<tbody>

<?php while ($row = $courses->fetch_assoc()): 
$perc = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 1) : 0;
?>

<tr>

<td><?php echo htmlspecialchars($row['code'].' - '.$row['name']); ?></td>

<td><?php echo $row['total']; ?></td>

<td><?php echo $row['present']; ?></td>

<td><?php echo $perc; ?>%</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php endif; ?>

</div>


<script>

function startScanner(){

document.getElementById("reader").innerHTML="";

const qr = new Html5Qrcode("reader");

qr.start(
{ facingMode: "environment" },
{
fps:10,
qrbox:250
},

function(decodedText){

document.getElementById("scan-result").innerHTML =
'<div class="alert alert-info">Processing attendance...</div>';

try {

let url = new URL(decodedText);
let token = url.searchParams.get("token");

if(!token){
    throw "Invalid QR";
}

fetch("../mark.php?token=" + encodeURIComponent(token), {

    method: "GET",
    headers: {
        "X-Requested-With": "XMLHttpRequest"
    }

})
.then(res => res.text())
.then(data => {

document.getElementById("scan-result").innerHTML = data;

qr.stop();

});

} catch(e){

document.getElementById("scan-result").innerHTML =
'<div class="alert alert-danger">Invalid QR Code</div>';

}

}

);

}

</script>

</body>
</html>