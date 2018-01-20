<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Name  = $_POST['Name'];
$Mobile  = $_POST['Mobile'];
$Email = $_POST['Email'];
$DateOfDelivery  = $_POST['DateOfDelivery'];
$Address = $_POST['Address'];
$TotalCost  = $_POST['TotalCost'];
$DiscountPercentage  = $_POST['DiscountPercentage'];
$FinalCost = $_POST['FinalCost'];
$IsDelivered  = $_POST['IsDelivered'];
$Comments  = $_POST['Comments'];
$IsActive  = $_POST['IsActive'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

$sql = "Update `orders` set `Name`='$Name', `Mobile`='$Mobile', `Email`='$Email', `DateOfDelivery`='$DateOfDelivery', `Address`='$Address', `TotalCost`=$TotalCost, `DiscountPercentage`=$DiscountPercentage, `FinalCost`=$FinalCost, `IsDelivered`=$IsDelivered, `Comments`='$Comments', `IsActive`=$IsActive, `LastUpdatedOn`=NOW() where `ID`=$ID";

$result = mysqli_query($conn, $sql);
if ($result===True) {
    echo "[{\"Result\":\"True\"}]";
} else {
    echo "[{\"Result\":\"False\"}]";
}

?>