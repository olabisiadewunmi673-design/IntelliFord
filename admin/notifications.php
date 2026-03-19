<?php 
include '../includes/sidebar.php'; 
include '../config.php';

$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mx-auto" style="max-width: 900px; padding-top: 20px;">
  <h2 class="text-center mb-4">Notifications Log</h2>

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr><th>Message</th><th>Created At</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($notifications->num_rows > 0): ?>
        <?php while($notif = $notifications->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($notif['message']); ?></td>
            <td><?php echo $notif['created_at']; ?></td>
            <td>
              <button class="btn btn-sm btn-secondary">Mark as Read</button>
              <button class="btn btn-sm btn-danger">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3" class="text-center">No notifications yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>