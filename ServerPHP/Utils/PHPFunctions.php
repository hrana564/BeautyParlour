<?php

function generateRandomString($length = 100) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function AuthenticateUser($UserName, $Password,$conn){
	$sql = "select * from mstusers where UserName='$UserName' and UserPassword='$Password' and IsActive=True";
	$result = mysqli_query($conn, $sql);
    // output data of each row
		while($row = mysqli_fetch_array($result)) {
			$NewAuthToken = generateRandomString();
			$sql = "update mstusers set AuthToken='$NewAuthToken' where UserName='$UserName'";
			$result = mysqli_query($conn, $sql);
			if ($result===True) {
			    return $NewAuthToken;
			} else {
			    return "-1";
			}
		}
}

function ValidateToken($Token,$conn) {
	if(strlen($Token)==100){
		$sql = "select * from mstusers where AuthToken='$Token'";
		$result = mysqli_query($conn, $sql);
		while($row = mysqli_fetch_array($result)) {
			return "1";
		}
		return "-1";
	} else {
		
	}
	$sql = "update mstusers set AuthToken='' where AuthToken='$Token'";
	$result = mysqli_query($conn, $sql);
	return "-1";
}

?>