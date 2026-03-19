<?php 
include '../includes/sidebar.php'; 
include '../config.php';

$msg = "";

/* ==========================
   ADD STUDENT
========================== */
if (isset($_POST['add_student'])) {

  $user_id = (int)$_POST['user_id'];
  $reg_number = $conn->real_escape_string($_POST['reg_number']);
  $department = $conn->real_escape_string($_POST['department']);
  $year = $conn->real_escape_string($_POST['year']);
  $parent_phone = $conn->real_escape_string($_POST['parent_phone']);

  // Prevent duplicate reg number
  $check = $conn->query("SELECT 1 FROM students WHERE reg_number = '$reg_number'");

  if ($check->num_rows == 0) {

    $insert = $conn->query("
      INSERT INTO students (user_id, reg_number, department, year, parent_phone) 
      VALUES ($user_id, '$reg_number', '$department', '$year', '$parent_phone')
    ");

    $msg = $insert ? "Student added successfully!" : "Error adding student.";

  } else {
    $msg = "Student with this Reg Number already exists!";
  }
}

/* ==========================
   DELETE STUDENT
========================== */
if (isset($_GET['delete'])) {

  $id = intval($_GET['delete']);

  $conn->query("DELETE FROM students WHERE id = $id");

  header("Location: students.php");
  exit;
}

/* ==========================
   FETCH DATA
========================== */
$students = $conn->query("
  SELECT s.*, u.full_name 
  FROM students s 
  JOIN users u ON s.user_id = u.id
");

$student_users = $conn->query("
  SELECT id, full_name 
  FROM users 
  WHERE role = 'student' 
  AND id NOT IN (SELECT user_id FROM students)
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <h2>Manage Students</h2>

  <?php if($msg): ?>
    <div class="alert alert-info"><?= $msg ?></div>
  <?php endif; ?>

  <!-- ==========================
       ADD STUDENT FORM
  ========================== -->
  <form method="post" class="mb-4">
    <div class="row g-3">
      <div class="col-md-3">
        <select name="user_id" class="form-select" required>
          <option value="">Select Student User</option>
          <?php while($u = $student_users->fetch_assoc()): ?>
            <option value="<?= $u['id']; ?>">
              <?= htmlspecialchars($u['full_name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-3">
        <input type="text" name="reg_number" class="form-control" placeholder="Reg Number" required>
      </div>

      <div class="col-md-2">
        <input type="text" name="department" class="form-control" placeholder="Department" required>
      </div>

      <div class="col-md-2">
        <input type="text" name="year" class="form-control" placeholder="Year" required>
      </div>

      <div class="col-md-2">
        <input type="text" name="parent_phone" class="form-control" placeholder="Parent Phone">
      </div>

      <div class="col-12">
        <button type="submit" name="add_student" class="btn btn-success">
          Add Student
        </button>
      </div>
    </div>
  </form>

  <!-- ==========================
       STUDENT TABLE
  ========================== -->
  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>Reg No</th>
        <th>Name</th>
        <th>Dept</th>
        <th>Year</th>
        <th>Parent Phone</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php while($s = $students->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($s['reg_number']); ?></td>
          <td><?= htmlspecialchars($s['full_name']); ?></td>
          <td><?= htmlspecialchars($s['department']); ?></td>
          <td><?= htmlspecialchars($s['year']); ?></td>
          <td><?= htmlspecialchars($s['parent_phone'] ?? '-'); ?></td>
          <td>
            <!-- DELETE BUTTON -->
            <a href="?delete=<?= $s['id'] ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this student?')">
               Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</div>

</body>
</html>