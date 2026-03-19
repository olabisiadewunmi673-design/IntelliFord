<?php 
// lecturer/dashboard.php

require_once '../auth.php';
requireRole('lecturer');

include_once '../config.php';

$qr_url = null;
$generated_course = null;
$session_token = null;

if (isset($_POST['generate'])) {

    $course_id = (int)$_POST['course_id'];
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, lecturer_id, token, expiry) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $course_id, $_SESSION['user_id'], $token, $expiry);
    $stmt->execute();

    $session_token = $token;

    $base_url = "http://192.168.1.211/intelliford_attendance";
    $qr_url = $base_url . "/mark.php?token=" . $token;

    $generated_course = $conn->query("SELECT code, name FROM courses WHERE id = $course_id")->fetch_assoc();
}

if (isset($_POST['end_session'])) {

    $token = $_POST['token'];

    $stmt = $conn->prepare("UPDATE attendance_sessions SET expiry = NOW() WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    $session_ended = true;
}

$courses = $conn->query("SELECT id, code, name FROM courses WHERE lecturer_id = " . $_SESSION['user_id']);

$today_present = $conn->query("
    SELECT COUNT(DISTINCT a.student_id) as cnt 
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    WHERE c.lecturer_id = {$_SESSION['user_id']} 
    AND DATE(a.marked_at) = CURDATE()
")->fetch_assoc()['cnt'] ?? 0;

$low_attendance = $conn->query("
    SELECT u.full_name, s.reg_number, c.code, c.name AS course_name,
           ROUND(SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) AS percentage
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON a.course_id = c.id
    WHERE c.lecturer_id = {$_SESSION['user_id']}
    GROUP BY a.student_id, a.course_id
    HAVING percentage < 75
    ORDER BY percentage ASC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lecturer Dashboard - IntelliFord</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>

body { background:#f8f9fa; }

.sidebar{
width:250px;
height:100vh;
position:fixed;
top:0;
left:0;
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

</style>

</head>

<body>

<div class="sidebar">

<h4 class="text-center mb-4">Lecturer Panel</h4>
<hr>

<a href="dashboard.php" class="active">Dashboard</a>
<a href="#">My Courses</a>
<a href="attendance_history.php" class="nav-link text-white">Attendance History</a>
<a href="#">Reports</a>
<a href="../logout.php" class="mt-5 text-danger">Logout</a>

</div>


<div class="main-content">

<h2 class="text-center mb-4">
Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Lecturer'); ?>
</h2>


<div class="alert alert-info text-center mb-4">
<strong>Today's Attendance:</strong>
<?php echo $today_present; ?> student(s) marked present across your courses.
</div>


<div class="card p-4 mb-5 mx-auto" style="max-width:700px;">

<h4 class="text-center mb-4">Generate Attendance QR Code</h4>

<p class="text-center text-muted mb-4">
Students scan this QR to mark themselves present (valid for 10 minutes)
</p>

<form method="post">

<div class="mb-4">

<label class="form-label fw-bold">Select Course</label>

<select name="course_id" class="form-select" required>

<option value="">-- Choose a course --</option>

<?php while ($course = $courses->fetch_assoc()): ?>

<option value="<?php echo $course['id']; ?>">

<?php echo htmlspecialchars($course['code'].' - '.$course['name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<button type="submit" name="generate" class="btn btn-primary btn-lg w-100">
Generate QR Code
</button>

</form>

</div>


<?php if ($qr_url): ?>

<div class="card p-5 text-center mx-auto mb-5" style="max-width:500px;">

<h4 class="mb-4">
QR for <?php echo htmlspecialchars($generated_course['code'].' - '.$generated_course['name']); ?>
</h4>

<div id="qrcode" style="width:250px;height:250px;margin:0 auto 20px;"></div>

<p class="lead fw-bold text-danger" id="timer">Time left: 10:00</p>

<small class="text-muted">This QR code expires in 10 minutes</small>

<form method="post" class="mt-4">

<input type="hidden" name="token" value="<?php echo $session_token; ?>">

<button type="submit" name="end_session" class="btn btn-danger btn-lg">
End Session
</button>

</form>

</div>


<script>

new QRCode(document.getElementById("qrcode"), {
text:"<?php echo $qr_url; ?>",
width:250,
height:250
});

let timeLeft = 600;

const timerEl = document.getElementById("timer");

const countdown = setInterval(()=>{

timeLeft--;

let min = Math.floor(timeLeft/60);

let sec = timeLeft%60;

timerEl.textContent = `Time left: ${min}:${sec<10?'0':''}${sec}`;

if(timeLeft<=0){

clearInterval(countdown);

timerEl.textContent="QR Expired";

document.getElementById("qrcode").innerHTML="<p class='text-muted'>QR expired</p>";

}

},1000);

</script>

<?php endif; ?>


<?php if (!empty($session_ended)): ?>

<div class="alert alert-warning text-center mx-auto" style="max-width:600px;">
Session Ended. Students can no longer mark attendance.
</div>

<?php endif; ?>

</div>

</body>
</html>