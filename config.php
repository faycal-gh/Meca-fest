<?php
$host = "localhost";
$dbname = "competition_db";
$user = "root"; 
$password = ""; 
$db = new mysqli($host, $user, $password, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>