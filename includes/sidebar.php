<?php 
// includes/sidebar.php
require_once '../auth.php'; 
requireRole('admin'); 
?>
<div class="d-flex">
  <!-- Fixed left sidebar -->
  <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">IntelliFord Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item"><a href="dashboard.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>">Dashboard</a></li>
      <li class="nav-item"><a href="users.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='users.php'?'active':''; ?>">Manage Users</a></li>
      <li class="nav-item"><a href="students.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='students.php'?'active':''; ?>">Manage Students</a></li>
      <li class="nav-item"><a href="assign_course.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='assign_course.php'?'active':''; ?>">Assign Course</a></li>
      <li class="nav-item"><a href="courses.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='courses.php'?'active':''; ?>">Manage Courses</a></li>
      <li class="nav-item"><a href="reports.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='reports.php'?'active':''; ?>">Attendance Reports</a></li>
      <li class="nav-item"><a href="notifications.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='notifications.php'?'active':''; ?>">Notifications</a></li>
      <li class="nav-item"><a href="issues.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='issues.php'?'active':''; ?>">Issue Tracking</a></li>
      <li class="nav-item"><a href="settings.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF'])=='settings.php'?'active':''; ?>">Settings</a></li>
      <li class="nav-item mt-auto"><a href="../logout.php" class="nav-link text-danger">Logout</a></li>
    </ul>
  </div>

  <!-- Main content wrapper (with left padding for sidebar) -->
  <div class="flex-grow-1" style="margin-left: 280px; padding: 20px;">