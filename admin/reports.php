<?php 
include '../includes/sidebar.php'; 
include '../config.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter == 'low') {
  $query = "SELECT u.full_name, s.reg_number, c.code, c.name AS course_name, 
            COUNT(*) AS total, SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present,
            ROUND(SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) AS percentage
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON a.course_id = c.id
            GROUP BY a.student_id, a.course_id
            HAVING percentage < 75
            ORDER BY percentage ASC";
} else {
  $query = "SELECT u.full_name, s.reg_number, c.code, c.name AS course_name, 
            COUNT(*) AS total, SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present,
            ROUND(SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) AS percentage
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON a.course_id = c.id
            GROUP BY a.student_id, a.course_id
            ORDER BY percentage DESC";
}
$reports = $conn->query($query);

// Export handling (CSV and PDF)
if (isset($_GET['export'])) {
  if ($_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student Name', 'Reg Number', 'Course Code', 'Course Name', 'Total Sessions', 'Present', 'Percentage']);
    while ($row = $reports->fetch_assoc()) {
      fputcsv($output, [$row['full_name'], $row['reg_number'], $row['code'], $row['course_name'], $row['total'], $row['present'], $row['percentage']]);
    }
    fclose($output);
    exit;
  } elseif ($_GET['export'] == 'pdf') {
    require_once '../lib/tcpdf/tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
    $html = '<table border="1"><tr><th>Student</th><th>Reg No</th><th>Course Code</th><th>Course</th><th>Total</th><th>Present</th><th>%</th></tr>';
    while ($row = $reports->fetch_assoc()) {
      $html .= "<tr><td>{$row['full_name']}</td><td>{$row['reg_number']}</td><td>{$row['code']}</td><td>{$row['course_name']}</td><td>{$row['total']}</td><td>{$row['present']}</td><td>{$row['percentage']}</td></tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('attendance_report.pdf', 'D');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Reports - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mx-auto" style="max-width: 900px; padding-top: 20px;">
  <h2 class="text-center mb-4">Attendance Reports</h2>

  <div class="mb-3 text-center">
    <a href="?filter=<?php echo $filter; ?>&export=csv" class="btn btn-success me-2">Export CSV</a>
    <a href="?filter=<?php echo $filter; ?>&export=pdf" class="btn btn-danger">Export PDF</a>
  </div>

  <ul class="nav nav-tabs mb-4 justify-content-center">
    <li class="nav-item"><a class="nav-link <?php echo $filter == 'all' ? 'active' : ''; ?>" href="?filter=all">All</a></li>
    <li class="nav-item"><a class="nav-link <?php echo $filter == 'low' ? 'active' : ''; ?>" href="?filter=low">Low Attendance (<75%)</a></li>
  </ul>

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr><th>Student Name</th><th>Reg Number</th><th>Course Code</th><th>Course Name</th><th>Total Sessions</th><th>Present</th><th>Percentage</th></tr>
    </thead>
    <tbody>
      <?php if ($reports->num_rows > 0): ?>
        <?php while($report = $reports->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($report['full_name']); ?></td>
            <td><?php echo htmlspecialchars($report['reg_number']); ?></td>
            <td><?php echo htmlspecialchars($report['code']); ?></td>
            <td><?php echo htmlspecialchars($report['course_name']); ?></td>
            <td><?php echo $report['total']; ?></td>
            <td><?php echo $report['present']; ?></td>
            <td class="<?php echo $report['percentage'] < 75 ? 'text-danger' : 'text-success'; ?>"><?php echo $report['percentage']; ?>%</td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center">No attendance records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>