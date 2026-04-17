<?php
session_start();

// Change 'swarnavahini' to your folder name if it's different
$directory = "swarnavahini"; 

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . $directory . "/"; 
define('BASE_URL', $base_url);

// Database Connection
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "swarna_scheduler";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>