var app = angular.module("BeautyParlourApp", []); 

app.controller('mainController', ['$http','$scope', function($http,$scope){

	$scope.loading = true;
	$scope.productAddedSuccess = false;
	$scope.CartProducts = typeof localStorage.getItem('BeautyParloursCart') == "string" &&  localStorage.getItem('BeautyParloursCart') != "undefined" ? JSON.parse(localStorage.getItem('BeautyParloursCart')) : [];

	$scope.AllProducts = [];

	$http({
		url: window.location.origin+'/ServerPHP/Client/GetAllProducts.php',
		method: "GET",
	})
	.then(function(response) {
		document.getElementById("loadingPAGE").style.display="none";
		document.getElementById("mainPAGE").style.display="block";
		$scope.DispalyProducts = [];
		$scope.loading = false;
		$scope.BindGrid = [];
		for (var i = 0; i < response.data.length; i++) {
            if(response.data[i].PhotoURL=="" || response.data[i].PhotoURL== null || response.data[i].PhotoURL=="undefined" || response.data[i].PhotoURL==undefined){
				response.data[i].PhotoURL = "default.png";
            }
            var singleProduct = {"CategoryID":response.data[i].CategoryID, "CategoryName":response.data[i].CategoryName, "PhotoURL":response.data[i].PhotoURL, "SubProducts":JSON.parse(response.data[i].SubProducts)};
			for (var j = 0; j < singleProduct.SubProducts.length; j++) {
				if($scope.CartProducts.includes(singleProduct.SubProducts[j].ID)){
					singleProduct.SubProducts[j].AddedToCart = true;
				}	else {
					singleProduct.SubProducts[j].AddedToCart = false;
				}
			}
			$scope.AllProducts.push(singleProduct);
		}
		$scope.DispalyProducts = angular.copy($scope.AllProducts);
	});

	$scope.RefreshProducts = function () {
		$scope.DispalyProducts = [];
		for (var i = 0; i < $scope.AllProducts.length; i++) {
			var singleProduct = angular.copy($scope.AllProducts[i]);
			singleProduct.SubProducts=[];
			for (var j = 0; j < $scope.AllProducts[i].SubProducts.length; j++) {
				if($scope.AllProducts[i].SubProducts[j].Name.toLowerCase().includes($scope.filterValue.toLowerCase()) || !$scope.filterValue){
					singleProduct.SubProducts.push($scope.AllProducts[i].SubProducts[j]);
				}
			}
			if(singleProduct.SubProducts.length>0) $scope.DispalyProducts.push(singleProduct);
		}
	}

	$scope.CurrentlyActiveWindow = 0 ;
	$scope.screenWidth = screen.width;
	$scope.WindowUpDown = function (categoryID) {
		if(screen.width>479){
			return;
		}
		if($scope.CurrentlyActiveWindow == 0){
			$scope.ShowWindow(categoryID);
		} else if($scope.CurrentlyActiveWindow==categoryID){
			if(document.getElementById("container"+categoryID).style.display == "none"){
				$scope.ShowWindow(categoryID);
			} else {
				$scope.HideWindow(categoryID);
			}
		} else {
			$scope.HideWindow($scope.CurrentlyActiveWindow);
			$scope.ShowWindow(categoryID);
		}
		$scope.CurrentlyActiveWindow = categoryID;
	}

	$scope.ShowWindow = function (categoryID) {
		document.getElementById("container"+categoryID).style.display = "block";
		document.getElementById("leftDown"+categoryID).style.display = "none";
		document.getElementById("rightDown"+categoryID).style.display = "none";
		document.getElementById("leftUp"+categoryID).style.display = "block";
		document.getElementById("rightUp"+categoryID).style.display = "block";
	}

	$scope.HideWindow = function (categoryID) {
		document.getElementById("container"+categoryID).style.display = "none";
		document.getElementById("leftDown"+categoryID).style.display = "block";
		document.getElementById("rightDown"+categoryID).style.display = "block";
		document.getElementById("leftUp"+categoryID).style.display = "none";
		document.getElementById("rightUp"+categoryID).style.display = "none";
	}

	$scope.CheckUncheckProduct = function (CategID,ProdID,AddedToCart) {
		for (var i = 0; i < $scope.AllProducts.length; i++) {
			if($scope.AllProducts[i].CategoryID == CategID){
				for (var j = 0; j < $scope.AllProducts[i].SubProducts.length; j++) {
					if($scope.AllProducts[i].SubProducts[j].ID==ProdID){
						$scope.AllProducts[i].SubProducts[j].AddedToCart = AddedToCart;
					}
				}
			}
		}
	}

	$scope.AddToCart = function(){
		$scope.productAddedSuccess = false;
		for (var i = 0; i <$scope.AllProducts.length; i++) {
			for (var j = 0; j < $scope.AllProducts[i].SubProducts.length; j++) {
				if($scope.AllProducts[i].SubProducts[j].AddedToCart){
					if(!$scope.CartProducts.includes($scope.AllProducts[i].SubProducts[j].ID)){
						$scope.CartProducts.push($scope.AllProducts[i].SubProducts[j].ID);
					}
				} else {
					if($scope.CartProducts.includes($scope.AllProducts[i].SubProducts[j].ID)){
						$scope.CartProducts.splice($scope.CartProducts.indexOf($scope.AllProducts[i].SubProducts[j].ID),1)
					}
				}
			}
		}
		localStorage.setItem('BeautyParloursCart', JSON.stringify($scope.CartProducts));
		$scope.productAddedSuccess = true;
		document.body.scrollTop = document.documentElement.scrollTop = 0;
	};

}]);
