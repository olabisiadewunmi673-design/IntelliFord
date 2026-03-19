<?php
session_start();

/* ==========================
   INCLUDE FILES (FIXED PATHS)
========================== */
include '../includes/sidebar.php';
include '../config.php';

/* ==========================
   ADMIN AUTH CHECK (OPTIONAL)
========================== */
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: ../index.php");
//     exit;
// }

/* ==========================
   ASSIGN COURSE
========================== */
if (isset($_POST['assign'])) {

    $student_id = intval($_POST['student_id']);
    $course_id  = intval($_POST['course_id']);

    // Prevent duplicate assignment
    $check = $conn->query("
        SELECT 1 FROM student_courses 
        WHERE student_id = $student_id 
        AND course_id = $course_id
    ");

    if ($check && $check->num_rows == 0) {

        $insert = $conn->query("
            INSERT INTO student_courses (student_id, course_id)
            VALUES ($student_id, $course_id)
        ");

        $msg = $insert ? "Course assigned successfully!" : "Error assigning course.";

    } else {
        $msg = "Student already assigned to this course.";
    }
}

/* ==========================
   REMOVE COURSE
========================== */
if (isset($_GET['remove'])) {

    $id = intval($_GET['remove']);

    $conn->query("DELETE FROM student_courses WHERE id = $id");

    header("Location: assign_course.php");
    exit;
}

/* ==========================
   FETCH STUDENTS
========================== */
$students = $conn->query("
    SELECT s.id, u.full_name 
    FROM students s
    JOIN users u ON s.user_id = u.id
");

/* ==========================
   FETCH COURSES
========================== */
$courses = $conn->query("
    SELECT id, code, name FROM courses
");

/* ==========================
   FETCH ASSIGNED COURSES
========================== */
$assigned = $conn->query("
    SELECT sc.id, u.full_name, c.code, c.name
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON sc.course_id = c.id
    ORDER BY sc.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Assign Course to Student</h2>

<?php if(isset($msg)): ?>
    <div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<!-- ==========================
     ASSIGN FORM
========================== -->
<form method="POST" class="mt-4">

    <div class="mb-3">
        <label>Select Student</label>
        <select name="student_id" class="form-control" required>
            <option value="">-- Select Student --</option>
            <?php while($row = $students->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= $row['full_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Select Course</label>
        <select name="course_id" class="form-control" required>
            <option value="">-- Select Course --</option>
            <?php while($row = $courses->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= $row['code'] ?> - <?= $row['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <button type="submit" name="assign" class="btn btn-primary">
        Assign Course
    </button>

</form>

<hr>

<!-- ==========================
     ASSIGNED COURSES TABLE
========================== -->
<h3 class="mt-5">Assigned Courses</h3>

<table class="table table-bordered mt-3">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Student</th>
            <th>Course</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if($assigned && $assigned->num_rows > 0): ?>
            <?php $i = 1; while($row = $assigned->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['full_name'] ?></td>
                    <td><?= $row['code'] ?> - <?= $row['name'] ?></td>
                    <td>
                        <a href="?remove=<?= $row['id'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Remove this course?')">
                           Remove
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No assignments found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>