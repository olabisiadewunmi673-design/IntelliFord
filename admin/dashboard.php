```php
<?php
include '../includes/sidebar.php';
include '../config.php';

/* =========================
   DASHBOARD STATISTICS
========================= */

$res_students=$conn->query("SELECT COUNT(*) AS count FROM students");
$total_students=$res_students->fetch_assoc()['count'] ?? 0;

$res_lecturers=$conn->query("SELECT COUNT(*) AS count FROM users WHERE role='lecturer'");
$total_lecturers=$res_lecturers->fetch_assoc()['count'] ?? 0;

$res_today=$conn->query("SELECT COUNT(*) AS count FROM attendance WHERE DATE(marked_at)=CURDATE()");
$today_attendance=$res_today->fetch_assoc()['count'] ?? 0;

$absent_today=max($total_students-$today_attendance,0);
$attendance_rate=$total_students>0?round(($today_attendance/$total_students)*100):0;

/* =========================
   TOTAL USERS
========================= */

$res_users = $conn->query("SELECT COUNT(*) AS count FROM users");
$row_users = $res_users ? $res_users->fetch_assoc() : null;
$total_users = $row_users['count'] ?? 0;

/* =========================
   CHART DATA
========================= */

$dates=[];
$present_count=[];

for($i=6;$i>=0;$i--){

$date=date('Y-m-d',strtotime("-$i days"));
$dates[]=date('M d',strtotime($date));

$res_chart=$conn->query("
SELECT COUNT(*) AS c
FROM attendance
WHERE DATE(marked_at)='$date'
AND status='present'
");

$present_count[]=$res_chart->fetch_assoc()['c'] ?? 0;

}

/* =========================
   RECENT ACTIVITY
========================= */

$recent_activity=$conn->query("
SELECT u.full_name,s.reg_number,c.code,a.status,a.marked_at
FROM attendance a
JOIN students s ON a.student_id=s.id
JOIN users u ON s.user_id=u.id
JOIN courses c ON a.course_id=c.id
ORDER BY a.marked_at DESC
LIMIT 5
");

/* =========================
   STUDENT PERFORMANCE
========================= */

$performance=$conn->query("
SELECT s.reg_number,u.full_name,
ROUND(SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END)/COUNT(*)*100,1) AS percentage
FROM attendance a
JOIN students s ON a.student_id=s.id
JOIN users u ON s.user_id=u.id
GROUP BY a.student_id
ORDER BY percentage ASC
LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{background:#e9ecef;}

.card{
border:none;
box-shadow:0 4px 10px rgba(0,0,0,0.08);
background:white;
}

.quick-btn{
width:100%;
margin-bottom:10px;
}
.stat-card{
display:flex;
align-items:center;
justify-content:space-between;
padding:18px 20px;
border-radius:10px;
}

.stat-left{
display:flex;
align-items:center;
gap:10px;
}

.stat-left i{
font-size:22px;
color:#0d6efd;
}

.stat-number{
font-size:22px;
font-weight:600;
}

</style>

</head>

<body>

<div class="container" style="max-width:1200px;padding-top:20px">

<!-- HEADER -->

<div class="d-flex justify-content-between align-items-center mb-4">

<h4 class="mb-0">
Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>
</h4>

<div class="d-flex align-items-center gap-3">

<input type="text"
class="form-control"
placeholder="Search..."
style="width:250px">

<span class="text-muted">
admin / dashboard
</span>

</div>

</div>

<!-- STAT CARDS -->

<div class="row mb-4 text-center">

<div class="col-md-2">
<div class="card p-3">
<i class="fas fa-user-graduate text-primary mb-2"></i>
<h6 class="text-muted">Students</h6>
<h4><?php echo $total_students; ?></h4>
</div>
</div>

<div class="col-md-2">
<div class="card p-3">
<i class="fas fa-chalkboard-teacher text-primary mb-2"></i>
<h6 class="text-muted">Lecturers</h6>
<h4><?php echo $total_lecturers; ?></h4>
</div>
</div>

<div class="col-md-2">
<div class="card p-3">
<i class="fas fa-users text-primary mb-2"></i>
<h6 class="text-muted">Users</h6>
<h4><?php echo $total_users; ?></h4>
</div>
</div>

<div class="col-md-2">
<div class="card p-3">
<i class="fas fa-bug text-primary mb-2"></i>
<h6 class="text-muted">Issues</h6>
<h4><?php echo $total_issues ?? 0; ?></h4>
</div>
</div>

<div class="col-md-2">
<div class="card p-3">
<i class="fas fa-calendar-check text-primary mb-2"></i>
<h6 class="text-muted">Today</h6>
<h4><?php echo $today_attendance; ?></h4>
</div>
</div>

</div>

<div class="row">

<!-- LEFT COLUMN -->

<div class="col-lg-7">

<div class="card p-4 mb-4">
<h5 class="text-center">Attendance Trend (Last 7 Days)</h5>
<canvas id="lineChart" height="120"></canvas>
</div>

<div class="card p-4 mb-4">

<h6 class="mb-3">Attendance Health</h6>

<div class="row text-center">

<div class="col-4">
<i class="fas fa-check-circle text-success"></i>
<p class="small text-muted mb-0">Present</p>
<strong><?php echo $today_attendance;?></strong>
</div>

<div class="col-4">
<i class="fas fa-times-circle text-danger"></i>
<p class="small text-muted mb-0">Absent</p>
<strong><?php echo $absent_today;?></strong>
</div>

<div class="col-4">
<i class="fas fa-chart-line text-primary"></i>
<p class="small text-muted mb-0">Rate</p>
<strong><?php echo $attendance_rate;?>%</strong>
</div>

</div>

</div>

<!-- RECENT ACTIVITY -->

<div class="card p-3 mb-4">

<h6>Recent Attendance Activity</h6>

<table class="table table-sm">

<thead>
<tr>
<th>Student</th>
<th>Course</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php while($row=$recent_activity->fetch_assoc()): ?>

<tr>

<td><?php echo htmlspecialchars($row['full_name']);?></td>

<td><?php echo htmlspecialchars($row['code']);?></td>

<td>

<?php if($row['status']=="present"):?>

<span class="badge bg-success">Present</span>

<?php else:?>

<span class="badge bg-danger">Absent</span>

<?php endif;?>

</td>

</tr>

<?php endwhile;?>

</tbody>

</table>

</div>

<!-- PERFORMANCE TABLE -->

<div class="card p-3 mb-4">

<div class="d-flex justify-content-between mb-3">

<h6>Student Attendance Performance</h6>

<select class="form-select w-auto" id="performanceFilter">
<option value="all">All</option>
<option value="high">High (≥75%)</option>
<option value="low">Low (&lt;75%)</option>
</select>

</div>

<table class="table table-sm">

<thead>
<tr>
<th>Student</th>
<th>Attendance</th>
</tr>
</thead>

<tbody id="performanceTable">

<?php while($row=$performance->fetch_assoc()): ?>

<tr data-percentage="<?php echo $row['percentage']; ?>">

<td><?php echo htmlspecialchars($row['full_name']);?></td>

<td><?php echo $row['percentage'];?>%</td>

</tr>

<?php endwhile;?>

</tbody>

</table>

</div>

</div>

<!-- RIGHT COLUMN -->

<div class="col-lg-5">

<div class="card p-4 mb-4">

<h5 class="text-center">Present vs Absent</h5>

<canvas id="pieChart" height="200"></canvas>

</div>

<div class="card p-4">

<h6 class="mb-3">Quick Actions</h6>

<button class="btn btn-primary quick-btn">
<i class="fas fa-user-plus"></i> Add Student
</button>

<button class="btn btn-success quick-btn">
<i class="fas fa-chalkboard-teacher"></i> Add Lecturer
</button>

<button class="btn btn-warning quick-btn">
<i class="fas fa-book"></i> Create Course
</button>

<button class="btn btn-dark quick-btn">
<i class="fas fa-file-export"></i> Export Report
</button>

</div>

</div>

</div>

</div>

<script>

const lineChart=new Chart(
document.getElementById('lineChart'),
{
type:'line',
data:{
labels: <?php echo json_encode($dates);?>,
datasets:[{
label:'Present',
data: <?php echo json_encode($present_count);?>,
borderColor:'#0d6efd',
tension:0.3
}]
}
}
);

const pieChart=new Chart(
document.getElementById('pieChart'),
{
type:'doughnut',
data:{
labels:['Present','Absent'],
datasets:[{
data:[<?php echo $today_attendance;?>,<?php echo $absent_today;?>],
backgroundColor:['#198754','#dc3545']
}]
}
}
);

/* FILTER PERFORMANCE TABLE */

document.getElementById("performanceFilter").addEventListener("change",function(){

let filter=this.value;
let rows=document.querySelectorAll("#performanceTable tr");

rows.forEach(row=>{

let perc=parseFloat(row.dataset.percentage);

if(filter==="high" && perc<75){
row.style.display="none";
}
else if(filter==="low" && perc>=75){
row.style.display="none";
}
else{
row.style.display="";
}

});

});

</script>

</body>
</html>
```
