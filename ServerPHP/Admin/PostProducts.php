<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Name  = $_POST['Name'];
$Price = $_POST['Price'];
$CategoryID = $_POST['CategoryID'];
$IsActive  = $_POST['IsActive'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

if($ID ==""){
	$sql = "INSERT INTO `products`(`Name`, `Price`, `CategoryID`, `IsActive`, `CreatedOn`, `LastUpdatedOn`) Select '$Name', $Price, $CategoryID,$IsActive,NOW(),NOW()";
} else{
	$sql = "Update `products` set `Name`='$Name', `Price`=$Price, `IsActive`=$IsActive, `LastUpdatedOn`=NOW() where `ID`=$ID";
}

$result = mysqli_query($conn, $sql);
if ($result===True) {
    echo "[{\"Result\":\"True\"}]";
} else {
    echo "[{\"Result\":\"False\"}]";
}
$conn->close();

?>