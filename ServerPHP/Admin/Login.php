<?php

require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$userName = $_POST['userName'];
$password  = $_POST['password'];

$UserToken = AuthenticateUser($userName,$password,$conn);
echo "[{\"AuthToken\":\"$UserToken\"}]";

?>