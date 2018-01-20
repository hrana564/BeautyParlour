<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$ID = $_POST['ID'];
$Name  = $_POST['Name'];
$TotalCost = $_POST['TotalCost'];
$Quantity  = $_POST['Quantity'];
$PricePerKG  = $_POST['PricePerKG'];
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  != 1){
	echo "[{\"Result\":\"-1\"}]";
	exit();
}

$sql = "Update `orderproducts` set `Name`='$Name', `TotalCost`=$TotalCost, `Quantity`=$Quantity, `PricePerKG`=$PricePerKG, `LastUpdatedOn`=NOW() where `ID`=$ID";

$result = mysqli_query($conn, $sql);
if ($result===True) {
    echo "[{\"Result\":\"True\"}]";
} else {
    echo "[{\"Result\":\"False\"}]";
}

?>