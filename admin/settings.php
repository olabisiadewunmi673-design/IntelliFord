<?php 
include '../includes/sidebar.php'; 
include '../config.php';

// Sample settings data (you can load from DB or config)
$site_name = $sitename; // From config.php
$twilio_sid = ''; // Placeholder - load from DB if needed
$twilio_token = '';
$twilio_number = '';

if (isset($_POST['update_settings'])) {
  // Update logic (e.g., save to DB or config file)
  // For now, placeholder
  $site_name = $conn->real_escape_string($_POST['site_name']);
  // Save to site_settings table or file
  echo "<div class='alert alert-success'>Settings updated successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Settings - IntelliFord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mx-auto" style="max-width: 900px; padding-top: 20px;">
  <h2 class="text-center mb-4">System Settings</h2>

  <form method="post" class="card p-4">
    <div class="mb-3">
      <label class="form-label">Site Name</label>
      <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($site_name); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Twilio SID (for SMS)</label>
      <input type="text" name="twilio_sid" class="form-control" value="<?php echo htmlspecialchars($twilio_sid); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Twilio Auth Token</label>
      <input type="text" name="twilio_token" class="form-control" value="<?php echo htmlspecialchars($twilio_token); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Twilio Phone Number</label>
      <input type="text" name="twilio_number" class="form-control" value="<?php echo htmlspecialchars($twilio_number); ?>" placeholder="+1234567890">
    </div>
    <div class="mb-3">
      <label class="form-label">Upload Logo</label>
      <input type="file" name="logo" class="form-control">
    </div>
    <button type="submit" name="update_settings" class="btn btn-primary w-100">Update Settings</button>
  </form>
</div>

</body>
</html>