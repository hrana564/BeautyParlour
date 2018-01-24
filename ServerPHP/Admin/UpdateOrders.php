<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Name  = $_POST['Name'];
$Mobile  = $_POST['Mobile'];
$Email = $_POST['Email'];
$DateOfService  = $_POST['DateOfService'];
$TimeOfService  = $_POST['TimeOfService'];
$Address = $_POST['Address'];
$TotalCost  = $_POST['TotalCost'];
$DiscountPercentage  = $_POST['DiscountPercentage'];
$FinalCost = $_POST['FinalCost'];
$IsServicePerformed  = $_POST['IsServicePerformed'];
$Comments  = $_POST['Comments'];
$IsActive  = $_POST['IsActive'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

$sql = "Update `orders` set `Name`='$Name', `Mobile`='$Mobile', `Email`='$Email', `DateOfService`='$DateOfService', `TimeOfService`='$TimeOfService', `Address`='$Address', `TotalCost`=$TotalCost, `DiscountPercentage`=$DiscountPercentage, `FinalCost`=$FinalCost, `IsServicePerformed`=$IsServicePerformed, `Comments`='$Comments', `IsActive`=$IsActive, `LastUpdatedOn`=NOW() where `ID`=$ID";

$result = mysqli_query($conn, $sql);
if ($result===True) {
    echo "[{\"Result\":\"True\"}]";
} else {
    echo "[{\"Result\":\"False\"}]";
}

?>