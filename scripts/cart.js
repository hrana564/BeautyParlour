Date.prototype.addDays = function(days) {
    this.setDate(this.getDate() + parseInt(days));
    return this;
};

var app = angular.module("BeautyParlourApp", []); 

app.controller('cartController', ['$http','$scope', function($http,$scope){

	$scope.CartProducts = typeof localStorage.getItem('BeautyParloursCart') == "string" &&  localStorage.getItem('BeautyParloursCart') != "undefined" ? JSON.parse(localStorage.getItem('BeautyParloursCart')) : [];
	$scope.FinalCartProducts = [];
	$scope.AllProducts = [];
	$scope.TempCartProducts = [];
	$scope.loading = true;
	$scope.DiscountPercentage = 5;
	$scope.ImgSrc = "";
	$scope.PlaceOrder={
		"Name":"",
		"Mobile":"",
		"Email":"",
		"DateOfService":"",
		"TimeOfService":"",
		"Address":"",
		"TotalCost":0,
		"DiscountPercentage":$scope.DiscountPercentage,
		"FinalCost":0,
		"ImgToken" : "",
		"ImgActualString" : ""
	}

	$http({
		url: window.location.origin+'/ServerPHP/Client/GetAllProducts.php',
		method: "GET",
	})
	.then(function(response) {
		document.getElementById("loadingPAGE").style.display="none";
		document.getElementById("mainPAGE").style.display="block";
		$scope.loading = false;
		for (var i = 0; i < response.data.length; i++) {
			$scope.AllProducts.push({"CategoryID":response.data[i].CategoryID, "CategoryName":response.data[i].CategoryName, "PhotoURL":response.data[i].PhotoURL, "SubProducts":JSON.parse(response.data[i].SubProducts)});
		}
		for (var i = 0; i < $scope.CartProducts.length; i++) {
			for (var j= 0; j < $scope.AllProducts.length; j++) {
				for (var k = 0; k < $scope.AllProducts[j].SubProducts.length; k++) {
					if($scope.AllProducts[j].SubProducts[k].ID == $scope.CartProducts[i]){
						$scope.FinalCartProducts.push({"ProductID":$scope.CartProducts[i],"ProductName":$scope.AllProducts[j].SubProducts[k].Name,"CategoryName":$scope.AllProducts[j].CategoryName,"Price":$scope.AllProducts[j].SubProducts[k].Price});
						$scope.TempCartProducts.push($scope.CartProducts[i]);
						break;
					}
				}
			}
		}
		if($scope.TempCartProducts.length!=$scope.CartProducts.length){
			alert('Cart was updated due to unavailiblity of some products!');
			$scope.CartProducts = angular.copy($scope.TempCartProducts);
			localStorage.setItem('BeautyParloursCart', JSON.stringify($scope.CartProducts));
		}
	});

	$scope.add = function (a, b) {
	    return Number(b.Price) + a;
	}

	$scope.UpdateCartProduct = function () {
		$scope.CartProducts = [];
		for (var i = 0; i < $scope.FinalCartProducts.length; i++) {
			$scope.CartProducts.push($scope.FinalCartProducts[i].ProductID);
		}
		localStorage.setItem('BeautyParloursCart', JSON.stringify($scope.CartProducts));
	}

	var currentDate = new Date();
	$scope.DeliveryDates = [];
	for (var i = 0; i < 30; i++) {
		var tmpDate = currentDate.addDays(1);
		$scope.DeliveryDates.push({"text":tmpDate.toDateString(),"value":tmpDate.getFullYear()+"-"+(tmpDate.getMonth()+1)+"-"+tmpDate.getDate()});
		if(i==0) $scope.PlaceOrder.DateOfDelivery = tmpDate.getFullYear()+"-"+(tmpDate.getMonth()+1)+"-"+tmpDate.getDate();
	}

	$scope.DeliveryTime = [];
	for (var i = 0; i < 24; i++) {
		if(i<12){
			$scope.DeliveryTime.push({"text":(i<10?"0"+i.toString():i)+" am","value":(i<10?"0"+i.toString():i)+" am"});
		} else {
			$scope.DeliveryTime.push({"text":((i==12?12:i-12)<10?"0"+(i==12?12:i-12).toString():(i==12?12:i-12))+" pm","value":((i==12?12:i-12)<10?"0"+(i==12?12:i-12).toString():(i==12?12:i-12))+" pm"});
		}
	}
	$scope.PlaceOrder.TimeOfDelivery = "09 am"
	$scope.LoadFreshCaptcha = function () {
		$http({
			url: window.location.origin+'/ServerPHP/Client/GetCaptchaImage.php',
			method: "GET",
		})
		.then(function(response) {
			$scope.ImgSrc = response.data.ImgSrc;
			$scope.PlaceOrder.ImgToken = response.data.ImgToken;
		});
		$scope.PlaceOrder.ImgToken = "";
		$scope.PlaceOrder.ImgActualString = "";
	}
	$scope.LoadFreshCaptcha();

	$scope.PlaceOrderFinal = function () {
		if($scope.FinalCartProducts.length==0){
			alert('Your Cart is Empty. Please add services to your cart!')
			return;
		}
		if($scope.PlaceOrder.Name==""){
			alert('Name is Mandatory. Please Enter your name!');
			return;
		}
		if($scope.PlaceOrder.Name.length<3){
			alert('Your name entered is invalid. Name should me more that 2 characters!');
			return;
		}
		if($scope.PlaceOrder.Mobile==""){
			alert('Mobile Number is Mandatory. Please Enter your Mobile Number!');
			return;
		}
		if($scope.PlaceOrder.Mobile.length != 10 || isNaN($scope.PlaceOrder.Mobile) == true){
			alert('Invalid Mobile number detected. Mobile Number should be 10 digits only!');
			return;
		}
		if($scope.PlaceOrder.Address==""){
			alert('Delivery Address is Mandatory. Please Enter your Delivery Address!');
			return;
		}
		if($scope.PlaceOrder.Address.length<10){
			alert('Delivery Address Should be atleast 10 character long!');
			return;
		}
		$scope.PlaceOrder.TotalCost = $scope.FinalCartProducts.reduce($scope.add, 0) ;
		$scope.PlaceOrder.FinalCost = $scope.FinalCartProducts.reduce($scope.add, 0) * (1-($scope.PlaceOrder.DiscountPercentage/100)) ;

		$http({
		url: window.location.origin+'/ServerPHP/Client/PostOrder.php',
		method: "POST",
		headers: {
			'Content-Type': 'multipart/form-data'
		},
		data:{"OD":JSON.stringify($scope.PlaceOrder), "OC":JSON.stringify($scope.FinalCartProducts)}
		})
		.then(function(response) {
			$scope.LoadFreshCaptcha();
			if(response.data[0].Result=="InvalidCaptcha"){
				$scope.LoadFreshCaptcha();
				alert('Invalid Captcha Entered! Please Enter Correct Captcha.');
				return;
			}
		    else if(response.data[0].Result=="True"){
		        alert('Thankyou! Order Placed Successfully. We will get in touch with you soon :)');
		        $scope.PlaceOrder={
					"Name":"",
					"Mobile":"",
					"Email":"",
					"DateOfDelivery":"",
					"TimeOfDelivery":"",
					"Address":"",
					"TotalCost":0,
					"DiscountPercentage":$scope.DiscountPercentage,
					"FinalCost":0,
					"ImgToken" : "",
					"ImgActualString" : ""
				}
				$scope.FinalCartProducts = [];
				$scope.UpdateCartProduct();
		    } else {
		        alert('Error occoured while placing order! Call 9823625630 for support.');
		        return;
		    }
		});
	}

 }]);