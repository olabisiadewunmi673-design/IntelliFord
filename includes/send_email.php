<?php
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;

 require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendParentEmail($to,$student,$course){

$mail = new PHPMailer(true);

try {

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'yourgmail@gmail.com';
$mail->Password = 'your_app_password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('no-reply@intelliford.com','IntelliFord Attendance');
$mail->addAddress($to);

$mail->Subject = "Attendance Alert";

$mail->Body = "
Dear Parent,

Your child $student was absent for $course today.

Regards
IntelliFord School
";

$mail->send();

} catch (Exception $e) {
}
}
?>