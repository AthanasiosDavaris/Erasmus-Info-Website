<?php
$first_name = $_POST["firstName"];
$last_name = $_POST["lastName"];
$student_id = $_POST["studentId"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];

$con = mysqli_connect("localhost", "root", "");

if(!$con) {
  echo "problem in the connection".mysqli_error();
} else {
  mysqli_select_db($con, "erasm");
  mysqli_query($con, "INSERT INTO users (first_name, last_name, student_id, phone, email, username, password) VALUES ('$first_name', '$last_name', '$student_id', '$phone', '$email', '$username', '$password')");
}
mysqli_close($con);

?>