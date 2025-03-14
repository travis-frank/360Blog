<?php
$host = "localhost";
$username = "root";
$password = "";
$databse = "360BlogDB";

$conn = new mysqli($host, $username, $password, $database);
if($conn -> connect_error){
    die("Connection failed: " . $conn -> connect_error);
}

?>