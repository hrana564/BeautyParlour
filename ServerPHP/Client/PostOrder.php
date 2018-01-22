<?php

require_once '../Utils/DBConfig.php';

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$OD  = $_POST['OD'];
$OC = $_POST['OC'];

$OCObj = json_decode($OD);
$OCArray = json_decode($OC);
try {	
	// Query the database to show all the tables.
	$sql = "Select * from `trn_captcha` where `ImgToken`='$OCObj->ImgToken' and `ImgActualString`='$OCObj->ImgActualString'";
	$result = mysqli_query($conn, $sql);
	// Print the results of the sql query.
	while($row = mysqli_fetch_array($result)) {
		$sql = "delete from `trn_captcha` where `ImgToken`='$OCObj->ImgToken' and `ImgActualString`='$OCObj->ImgActualString'";
		$result = mysqli_query($conn,$sql);
		/* set autocommit to off */
		mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

		$sql = "INSERT INTO `orders`(`Name`, `Mobile`, `Email`, `DateOfService`, `TimeOfService`, `Address`, `TotalCost`, `DiscountPercentage`, `FinalCost`, `IsServicePerformed`, `Comments`, `IsActive`, `CreatedOn`, `LastUpdatedOn`) value('$OCObj->Name','$OCObj->Mobile','$OCObj->Email','$OCObj->DateOfService','$OCObj->TimeOfService','$OCObj->Address',$OCObj->TotalCost,$OCObj->DiscountPercentage,$OCObj->FinalCost,0,'',1,NOW(),NOW())";
		$result = mysqli_query($conn,$sql);
		if(! ($result === True)){
			 throw new Exception("Cannot Place Order!!!");
		}
		$sql = "SELECT MAX(`ID`) as MAXID FROM `orders`";
		$result = mysqli_query($conn, $sql);
		while($row = mysqli_fetch_array($result)) {
			$NewOrderID = $row["MAXID"];
		}

		foreach ($OCArray as $value) {
			$sql = "INSERT INTO `orderproducts`(`OrderID`, `CategoryName`, `ProductName`, `TotalCost`, `CreatedOn`, `LastUpdatedOn`) select $NewOrderID,'$value->CategoryName','$value->ProductName',(SELECT `Price` FROM `products` where `ID`='$value->ProductID'),NOW(),NOW()";
			$result = mysqli_query($conn,$sql);
			if(! ($result === True)){
				throw new Exception("Cannot Place Sub Order!!!");
			}
		}
		mysqli_commit($conn);
		echo "[{\"Result\":\"True\"}]";
		exit();
	}
	echo "[{\"Result\":\"InvalidCaptcha\"}]";
	
} catch (Exception $e) {
	/* Rollback */
	mysqli_rollback($conn);
	echo $e->getMessage();
	echo "[{\"Result\":\"False\"}]";
}

?>