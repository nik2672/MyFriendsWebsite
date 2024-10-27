<?php
// all errors enabled for when testing website
error_reporting(E_ALL);
ini_set('display_errors', 1);

// host, user, pswd and dbnm for db connection
$host = "feenix-mariadb.swin.edu.au";
$user = "s104549772"; 
$pswd = "Shrilaxmi"; 
$dbnm = "s104549772_db";

// assigned credential to $conn variable 
$conn = new mysqli($host, $user, $pswd, $dbnm);
// check if the connection fails
if ($conn->connect_error) {
    die("failed to connect to db: " . $conn->connect_error);

}
?>
