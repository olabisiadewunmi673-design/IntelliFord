<?php
include '../config.php';
include '../includes/sidebar.php';

/* ADD USER */

if(isset($_POST['add_user'])){

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$name = $_POST['name'];
$role = $_POST['role'];
$gender = $_POST['gender'] ?? 'Prefer not to say';
$phone = $_POST['phone'];
$email = $_POST['email'];

$conn->query("INSERT INTO users (username,password,role,full_name,gender,phone,email)
VALUES ('$username','$password','$role','$name','$gender','$phone','$email')");
}

/* DELETE USER */

if(isset($_GET['delete'])){
$id = $_GET['delete'];
$conn->query("DELETE FROM users WHERE id=$id");
}

/* UPDATE USER */

if(isset($_POST['update_user'])){

$id = $_POST['id'];
$username = $_POST['username'];
$name = $_POST['name'];
$role = $_POST['role'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$email = $_POST['email'];

$conn->query("UPDATE users SET
username='$username',
name='$name',
role='$role',
gender='$gender',
phone='$phone',
email='$email'
WHERE id=$id");
}

$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Users</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.card{
border:none;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

</style>

</head>

<body>

<div class="container mt-4">

<h3>Manage Users</h3>

<!-- ADD USER FORM -->

<div class="card p-4 mb-4">

<form method="POST">

<div class="row">

<div class="col-md-2">
<label>Username</label>
<input type="text" name="username" class="form-control" placeholder="Enter username" required>
</div>

<div class="col-md-2">
<label>Password</label>
<input type="password" name="password" class="form-control" placeholder="Enter password" required>
</div>

<div class="col-md-2">
<label>Full Name</label>
<input type="text" name="name" class="form-control" placeholder="Enter full name">
</div>

<div class="col-md-2">
<label>Gender</label>

<select name="gender" class="form-control">

<option value="" disabled selected>Choose an option</option>

<option value="Male">Male</option>

<option value="Female">Female</option>

<option value="Prefer not to say">Prefer not to say</option>

</select>

</div>

<div class="col-md-2">
<label>Role</label>
<select name="role" class="form-control">

<option value="admin">Admin</option>
<option value="lecturer">Lecturer</option>
<option value="student">Student</option>

</select>
</div>

<div class="col-md-2">
<label>Email</label>
<input type="email" name="email" class="form-control" placeholder="example@email.com">
</div>

<div class="col-md-2 mt-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control" placeholder="Enter phone number">
</div>

<div class="col-md-2 mt-4">
<button class="btn btn-primary" name="add_user">Add User</button>
</div>

</div>

</form>

</div>

<!-- USERS TABLE -->

<div class="card p-3">

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>
<th>Username</th>
<th>Role</th>
<th>Name</th>
<th>Gender</th>
<th>Phone</th>
<th>Email</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($row = $users->fetch_assoc()){ ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= $row['username'] ?></td>

<td><?= ucfirst($row['role']) ?></td>

<td><?= $row['full_name'] ?></td>

<td><?= $row['gender'] ?: 'Prefer not to say' ?></td>

<td><?= $row['phone'] ?></td>

<td><?= $row['email'] ?></td>

<td>

<!-- EDIT BUTTON -->

<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">
Edit
</button>

<a href="?delete=<?= $row['id'] ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')">

Delete

</a>

</td>

</tr>

<!-- EDIT MODAL -->

<div class="modal fade" id="edit<?= $row['id'] ?>">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5>Edit User</h5>

</div>

<div class="modal-body">

<form method="POST">

<input type="hidden" name="id" value="<?= $row['id'] ?>">

<div class="mb-2">

<label>Username</label>

<input type="text" name="username" class="form-control"
value="<?= $row['username'] ?>">

</div>

<div class="mb-2">

<label>Full Name</label>

<input type="text" name="full_name" class="form-control"
value="<?= $row['full_name'] ?>">

</div>

<div class="mb-2">

<label>Gender</label>

<select name="gender" class="form-control">

<option value="">Choose an option</option>

<option value="Male" <?= $row['gender']=="Male"?'selected':'' ?>>Male</option>

<option value="Female" <?= $row['gender']=="Female"?'selected':'' ?>>Female</option>

<option value="Prefer not to say" <?= $row['gender']=="Prefer not to say"?'selected':'' ?>>Prefer not to say</option>

</select>

</div>

<div class="mb-2">

<label>Role</label>

<select name="role" class="form-control">

<option value="admin" <?= $row['role']=="admin"?'selected':'' ?>>Admin</option>

<option value="lecturer" <?= $row['role']=="lecturer"?'selected':'' ?>>Lecturer</option>

<option value="student" <?= $row['role']=="student"?'selected':'' ?>>Student</option>

</select>

</div>

<div class="mb-2">

<label>Email</label>

<input type="email" name="email" class="form-control"
value="<?= $row['email'] ?>">

</div>

<div class="mb-2">

<label>Phone</label>

<input type="text" name="phone" class="form-control"
value="<?= $row['phone'] ?>">

</div>

<button class="btn btn-success" name="update_user">

Update User

</button>

</form>

</div>

</div>

</div>

</div>

<?php } ?>

</tbody>

</table>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>