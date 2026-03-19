<?php include 'config.php'; 
if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $res = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];
            
            if ($row['role'] == 'student') {
                $st = $conn->query("SELECT id FROM students WHERE user_id = " . $row['id']);
                $_SESSION['student_id'] = $st->fetch_assoc()['id'] ?? null;
                header("Location: student/dashboard.php");
            } elseif ($row['role'] == 'lecturer') {
                header("Location: lecturer/dashboard.php");
            } else {
                header("Location: admin/dashboard.php");
            }
            exit;
        }
    }
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title><?php echo $sitename; ?> - Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

body{
height:100vh;
margin:0;
display:flex;
align-items:center;
justify-content:center;
font-family:Segoe UI;
overflow:hidden;
color:white;
}

/* BLURRED BACKGROUND */

.bg-blur{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:url("assets/if_logo.png") center/500px no-repeat;
filter:blur(60px) brightness(0.6);
transform:scale(2);
z-index:-1;
background-color:#0d3a6a;
}

/* LOGIN CARD */

.login-card{
background:rgba(0,0,0,0.35);
backdrop-filter:blur(10px);
padding:40px;
border-radius:12px;
width:350px;
text-align:center;
box-shadow:0 10px 40px rgba(0,0,0,0.5);
}

/* LOGO */

.logo{
width:120px;
margin-bottom:10px;
}

/* TITLE */

.title{
font-size:30px;
font-weight:600;
margin-bottom:0;
}

.subtitle{
letter-spacing:2px;
font-size:12px;
opacity:0.8;
margin-bottom:25px;
}

/* INPUT ICONS */

.input-group-text{
background:transparent;
border-right:0;
color:#aaa;
}

.form-control{
border-left:0;
background:rgba(255,255,255,0.1);
color:white;
}

/* LOGIN BUTTON */

.btn-login{
background:linear-gradient(90deg,#1c8cff,#0052cc);
border:none;
font-size:18px;
padding:10px;
margin-top:10px;
}

</style>

</head>

<body>

<div class="bg-blur"></div>

<div class="login-card">

<img src="assets/if_logo.png" class="logo">

<div class="title">IntelliFord</div>
<div class="subtitle">ATTENDANCE SYSTEM</div>

<h5 class="mb-4">Login</h5>

<?php if(isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>

<form method="post">

<div class="input-group mb-3">

<span class="input-group-text">
<i class="fa fa-user"></i>
</span>

<input type="text" name="username" class="form-control" placeholder="Username" required>

</div>

<div class="input-group mb-3">

<span class="input-group-text">
<i class="fa fa-lock"></i>
</span>

<input type="password" name="password" class="form-control" placeholder="Password" required>

</div>

<button type="submit" name="submit" class="btn btn-login w-100">
Login
</button>

</form>

</div>

</body>
</html>

