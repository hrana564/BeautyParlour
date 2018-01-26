<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

$sql = "Delete from `products` where `CategoryID` = $ID";

$result = mysqli_query($conn, $sql);
if ($result===True) {
	$sql = "Delete from `categories` where `ID`=$ID";
	$result = mysqli_query($conn, $sql);
	if ($result===True) {
    	echo "[{\"Result\":\"True\"}]";
    } else {
    	echo "[{\"Result\":\"False\"}]";
    }
} else {
    echo "[{\"Result\":\"False\"}]";
}
$conn->close();

?>