<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Name  = $_POST['Name'];
$IsActive  = $_POST['IsActive'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

if($ID ==""){
	$sql = "INSERT INTO `categories`(`Name`, `IsActive`, `CreatedOn`, `LastUpdatedOn`) Select '$Name',$IsActive,NOW(),NOW()";
} else{
	$sql = "Update `categories` set `Name`='$Name', `IsActive`=$IsActive, `LastUpdatedOn`=NOW() where `ID`=$ID";
}

$result = mysqli_query($conn, $sql);
if ($result===True) {
    echo "[{\"Result\":\"True\"}]";
} else {
    echo "[{\"Result\":\"False\"}]";
}

?>