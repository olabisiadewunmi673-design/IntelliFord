<?php 
include '../includes/sidebar.php'; 
include '../config.php';

if (isset($_POST['add_issue'])) {
  $title = $conn->real_escape_string($_POST['title']);
  $description = $conn->real_escape_string($_POST['description']);
  $stmt = $conn->prepare("INSERT INTO issue_tracking (title, description) VALUES (?, ?)");
  $stmt->bind_param("ss", $title, $description);
  $stmt->execute();
}

$issues = $conn->query("SELECT * FROM issue_tracking ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Issue Tracking - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mx-auto" style="max-width: 900px; padding-top: 20px;">
  <h2 class="text-center mb-4">Issue Tracking</h2>

  <form method="post" class="card p-4 mb-4">
    <div class="mb-3">
      <label class="form-label">Issue Title</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" name="add_issue" class="btn btn-primary w-100">Add Issue</button>
  </form>

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr><th>Title</th><th>Description</th><th>Status</th><th>Created At</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($issues->num_rows > 0): ?>
        <?php while($issue = $issues->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($issue['title']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($issue['description'])); ?></td>
            <td><span class="badge bg-warning"><?php echo htmlspecialchars($issue['status']); ?></span></td>
            <td><?php echo $issue['created_at']; ?></td>
            <td>
              <button class="btn btn-sm btn-warning">Edit</button>
              <button class="btn btn-sm btn-danger">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">No issues reported yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>