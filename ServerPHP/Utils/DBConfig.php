<?php

$dbhost     = 'localhost';
$dbname   = 'beautyparlour';
//$dbuser     = 'Ketan28';
//$dbpass     = 'Admin@123';

$dbuser     = 'himanshu';
$dbpass     = 'admin123';


$dbport      = '3306';

// Create connection
$conn = @mysqli_connect($dbhost,$dbuser,$dbpass,$dbname,$dbport);
// Check connection
if (mysqli_connect_errno()) {
	die ("Failed to connect to MySQL using the PHP mysqli extension: " . mysqli_connect_error());
}

?>