<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$Token = $_POST['Token'];

$sql = "update mstusers set AuthToken='' where AuthToken='$Token'";
$result = mysqli_query($conn, $sql);
if ($result===True) {
	echo "[{\"Result\":\"True\"}]";
} else {
	echo "[{\"Result\":\"False\"}]";
}

?>