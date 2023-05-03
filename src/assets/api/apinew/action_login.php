<?php
require('db.php');

$data = json_decode(file_get_contents("php://input"));

$name = mysqli_real_escape_string($con, $data->name);
$email = mysqli_real_escape_string($con, $data->email);

$query = mysqli_query($con, "insert into students (student_name, student_email) values ('$name', '$email')") or die ('Unable to execute query. '. mysqli_error($con));
