<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$Token = $_POST['Token'];

$IsAuthenticated = ValidateToken($Token,$conn);
if($IsAuthenticated  == 1){
	echo "[{\"Result\":\"True\"}]";
} else {
	echo "[{\"Result\":\"False\"}]";
}

?>