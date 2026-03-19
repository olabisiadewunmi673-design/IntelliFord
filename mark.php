<?php
include 'config.php';

// OPTIONAL: disable email for now
// include 'includes/send_email.php';

/* ==========================
   AJAX DETECTION
========================== */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/* ==========================
   AUTH CHECK
========================== */
if (!isset($_SESSION['student_id'])) {
    if ($isAjax) {
        echo "<div class='alert alert-danger'>Session expired. Please login again.</div>";
    } else {
        $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
        header("Location: index.php");
    }
    exit;
}

/* ==========================
   TOKEN CHECK
========================== */
if (isset($_GET['token'])) {

    $token = $conn->real_escape_string($_GET['token']);
    $student_id = intval($_SESSION['student_id']);

    $query = $conn->query("
        SELECT * FROM attendance_sessions 
        WHERE token='$token' AND expiry > NOW()
    ");

    if ($query && $query->num_rows > 0) {

        $session = $query->fetch_assoc();

        /* COURSE CHECK */
        $course_check = $conn->query("
            SELECT 1 FROM student_courses
            WHERE student_id = $student_id
            AND course_id = {$session['course_id']}
        ");

        if (!$course_check || $course_check->num_rows == 0) {
            echo "<div class='alert alert-danger'>You are not registered for this course.</div>";
            exit;
        }

        /* DUPLICATE CHECK */
        $check = $conn->query("
            SELECT 1 FROM attendance 
            WHERE student_id=$student_id 
            AND session_id={$session['id']}
        ");

        if ($check && $check->num_rows == 0) {

            $insert = $conn->query("
                INSERT INTO attendance (student_id, course_id, session_id, status)
                VALUES ($student_id, {$session['course_id']}, {$session['id']}, 'present')
            ");

            if ($insert) {

                $conn->query("
                    INSERT INTO notifications (message)
                    VALUES ('Attendance marked for student $student_id')
                ");

                echo "<div class='alert alert-success'>Attendance marked successfully ✓</div>";

            } else {
                echo "<div class='alert alert-danger'>Error recording attendance</div>";
            }

        } else {
            echo "<div class='alert alert-warning'>Already marked</div>";
        }

    } else {
        echo "<div class='alert alert-danger'>Invalid or expired QR code</div>";
    }

} else {
    echo "<div class='alert alert-warning'>No token provided</div>";
}
?>