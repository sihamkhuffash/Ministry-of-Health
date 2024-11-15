<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ministry_of_health_testing";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
   die("Connection failed 222: " . $conn->connect_error);
}
else{
   /* echo "Connected successfully";*/
}
?>
