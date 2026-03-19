<?php 
include '../includes/sidebar.php'; 
include '../config.php';

if (isset($_POST['add_course'])) {
  $code = $conn->real_escape_string($_POST['code']);
  $name = $conn->real_escape_string($_POST['name']);
  $lecturer_id = (int)$_POST['lecturer_id'];
  $stmt = $conn->prepare("INSERT INTO courses (code, name, lecturer_id) VALUES (?, ?, ?)");
  $stmt->bind_param("ssi", $code, $name, $lecturer_id);
  $stmt->execute();
}

$courses = $conn->query("SELECT c.*, u.full_name AS lecturer_name FROM courses c LEFT JOIN users u ON c.lecturer_id = u.id");
$lecturers = $conn->query("SELECT id, full_name FROM users WHERE role = 'lecturer'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mx-auto" style="max-width: 900px; padding-top: 20px;">
  <h2 class="text-center mb-4">Manage Courses</h2>

  <form method="post" class="card p-4 mb-4">
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" name="code" class="form-control" placeholder="Course Code (e.g., CSC101)" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="Course Name" required>
      </div>
      <div class="col-md-4">
        <select name="lecturer_id" class="form-select" required>
          <option value="">Assign Lecturer</option>
          <?php while($l = $lecturers->fetch_assoc()): ?>
            <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['full_name']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-12"><button type="submit" name="add_course" class="btn btn-success w-100">Add Course</button></div>
    </div>
  </form>

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr><th>Code</th><th>Name</th><th>Lecturer</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($courses->num_rows > 0): ?>
        <?php while($course = $courses->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($course['code']); ?></td>
            <td><?php echo htmlspecialchars($course['name']); ?></td>
            <td><?php echo htmlspecialchars($course['lecturer_name'] ?? 'Unassigned'); ?></td>
            <td>
              <button class="btn btn-sm btn-warning">Edit</button>
              <button class="btn btn-sm btn-danger">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">No courses added yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>