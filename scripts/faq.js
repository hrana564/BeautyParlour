var app = angular.module("BeautyParlourApp", []); 

app.controller('faqController', ['$http','$scope', function($http,$scope){

$scope.CartProducts = typeof localStorage.getItem('BeautyParloursCart') == "string" &&  localStorage.getItem('BeautyParloursCart') != "undefined" ? JSON.parse(localStorage.getItem('BeautyParloursCart')) : [];
	$scope.loading = true;
	$scope.FAQs = [];
	$scope.loopCount = 0;

	$http({
		url: window.location.origin+'/ServerPHP/Client/GetAllFAQs.php',
		method: "GET",
	})
	.then(function(response) {
		document.getElementById("loadingPAGE").style.display="none";
		document.getElementById("mainPAGE").style.display="block";
		$scope.loading = false;
		for (var i = 0; i < response.data.length; i++) {
			$scope.FAQs.push({"Question":response.data[i].Question,"Answer":response.data[i].Answer,"ID":i+1});
		}
	});

}]);
