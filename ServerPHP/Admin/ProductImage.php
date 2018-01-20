<!DOCTYPE html>
<html>
<body>
	<?php

	require_once '../Utils/DBConfig.php';
	require_once '../Utils/PHPFunctions.php';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$Token = $_POST['Token'];

			$IsAuthenticated = ValidateToken($Token,$conn);
			if($IsAuthenticated  != 1){
				echo "<script type=\"text/javascript\">window.location = window.location.origin+'/Admin/index.html';</script>";
				exit();
			}
	}

	parse_str($_SERVER['QUERY_STRING']);

	$target_dir = $_SERVER['DOCUMENT_ROOT']."/images/Products/";
	if(isset($_FILES['image'])){
		$errors= array();
		$file_name = $_FILES['image']['name'];
		$file_size =$_FILES['image']['size'];
		$file_tmp =$_FILES['image']['tmp_name'];
		$file_type=$_FILES['image']['type'];
		$tmp = explode('.', $file_name);
		$file_ext=strtolower(end($tmp));
		$NewFileName =generateRandomString(26) . ".".$file_ext;

		$expensions= array("jpeg","jpg","png");

		if(in_array($file_ext,$expensions)=== false){
			$errors[]="extension not allowed, please choose a JPEG or PNG file.";
		}

		if($file_size > 1097152){
			$errors[]='File size must be less than 1 MB';
		}

		if(empty($errors)==true){
			move_uploaded_file($file_tmp,$target_dir.$NewFileName);
		}else{
			$errors[]='Internal error occured while moving image';
		}
		$sql = "update `products` set `PhotoURL`='$NewFileName' where `ID`=$OrderID";
		$result = mysqli_query($conn, $sql);
		if ($result===True) {
			echo "Success";
		} else {
			$errors[] = 'Error occured while registering image to database';
		    print_r($errors);
		}
	}

	$sql = "Select `PhotoURL` FROM `products` where `ID`=$OrderID";
	$result = mysqli_query($conn, $sql);
	while($row = mysqli_fetch_array($result)) {
		$PhotoURL = $row["PhotoURL"];
	}

	if($PhotoURL == "" || $PhotoURL == NULL){
		$PhotoURL = "default.png";
	}
	echo "<div style=\"width: 250px; height: 150px;\"><img src=\"/images/Products/$PhotoURL\" style=\"width: 100%; height: 100%\"></div>" ;

	?>

	   <form action="#" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" />
         <input type="hidden" name="Token" value="" id="Token">
         <input type="submit"/>
      </form>
<script type="text/javascript">
	if((typeof localStorage.getItem('RanaSweetsAT') == "string" &&  localStorage.getItem('RanaSweetsAT') != "undefined" ? localStorage.getItem('RanaSweetsAT') : "").length !=100){
		window.location = window.location.origin+'/Admin/index.html';
	}
	document.getElementById("Token").value = typeof localStorage.getItem('RanaSweetsAT') == "string" &&  localStorage.getItem('RanaSweetsAT') != "undefined" ? localStorage.getItem('RanaSweetsAT') : "";
</script>
</body>
</html>