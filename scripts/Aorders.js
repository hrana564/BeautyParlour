Date.prototype.addDays = function(days) {
    this.setDate(this.getDate() + parseInt(days));
    return this;
};

var app = angular.module("RanaSweetsApp", []); 

app.controller('orderController', ['$http','$scope','UtilityObject', function($http,$scope,Utility){

    $scope.getQueryString = function ( field, url ) {
        var href = url ? url : window.location.href;
        var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
        var string = reg.exec(href);
        return string ? string[1] : null;
    };
    $scope.loading = false;
    $scope.FilterMode = $scope.getQueryString("Mode");
    $scope.FilterMode = $scope.FilterMode ? $scope.FilterMode : 1;
    $scope.BeautyParloursAT = typeof localStorage.getItem('BeautyParloursAT') == "string" &&  localStorage.getItem('BeautyParloursAT') != "undefined" ? localStorage.getItem('BeautyParloursAT') : "";

    if($scope.BeautyParloursAT.length == 100){
        $http({
                url: window.location.origin+'/ServerPHP/Admin/ValidateAuthToken.php',
                method: "POST",
                data: { 'Token':$scope.BeautyParloursAT}
            })
      .then(function(response) {
                // success
                if(response.data[0].Result=="True"){

                } else {
                    console.log(response);
                    localStorage.setItem('BeautyParloursAT','');
                    window.location = window.location.origin+'/Admin/index.html';
                }
            }).catch(function(response) { 
                // failure
                console.log(response);
                localStorage.setItem('BeautyParloursAT','');
                window.location = window.location.origin+'/Admin/index.html';
            });
    }else {
        localStorage.setItem('BeautyParloursAT','');
        window.location = window.location.origin+'/Admin/index.html';
    }
 //declaring the variable
 $scope.AngularGrid = new RSOrder();
 $scope.BindGrid = [];
 $scope.Utility = Utility;
 $scope.PageSize = 10;
 $scope.currentPage = 1;
 $scope.PagingMessage = "";
 $scope.AJAXTotalRequests = 0;
 $scope.AJAXCompletedRequests = 0;

 var currentDate = new Date();
 currentDate.addDays(-30);
 $scope.DeliveryDates = [];
 for (var i = 0; i < 60; i++) {
    var tmpDate = currentDate.addDays(1);
    $scope.DeliveryDates.push({"text":tmpDate.toDateString(),"value":tmpDate.getFullYear()+"-"+(tmpDate.getMonth()+1)+"-"+tmpDate.getDate()});
}

$scope.AlterProduct = new RSOrder();
$scope.productsToDelete = [];

function Error(Message) {
    alert(Message);
}

    //Function to bind Angular Grid
    $scope.loadGrid = function (Index) {
        $scope.loading = true;
        $http({
            url: window.location.origin+'/ServerPHP/Admin/GetAllOrders.php?PageIndex='+Index+'&PageSize='+$scope.PageSize+'&Mode='+$scope.FilterMode,
            method: "GET",
        })
        .then(function(response) {
            $scope.loading = false;
            $scope.BindGrid = [];
            for (var i = 0; i < response.data.length; i++) {
                $scope.BindGrid.push({"ID":response.data[i].ID ,
                    "Name":response.data[i].Name, 
                    "Mobile":response.data[i].Mobile, 
                    "Email":response.data[i].Email,
                    "DateOfService":response.data[i].DateOfService,
                    "TimeOfService":response.data[i].TimeOfService,
                    "Address":response.data[i].Address,
                    "TotalCost":response.data[i].TotalCost,
                    "DiscountPercentage":response.data[i].DiscountPercentage,
                    "FinalCost":response.data[i].FinalCost,
                    "FinalCartProducts":JSON.parse(response.data[i].SubOrderProducts),
                    "OrderProductCount":(JSON.parse(response.data[i].SubOrderProducts)).length,
                    "IsServicePerformed":response.data[i].IsServicePerformed,
                    "Comments":response.data[i].Comments,
                    "IsActive":response.data[i].IsActive,
                    "CreatedOn":response.data[i].CreatedOn,
                    "LastUpdatedOn":response.data[i].LastUpdatedOn});
            }
            $scope.VirtualItemCount = response.data[0]?response.data[0].VirtualItemCount:0;
            $scope.PagingMessage = $scope.Utility.Paging($scope.VirtualItemCount, $scope.PageSize, Index);
            $scope.currentPage = Index;
        });
    }
    $scope.loadGrid(1);
    $scope.FilterChange = function () {
        $scope.loadGrid(1);
    }

    $scope.sortBy = function (propertyName) {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
        $scope.propertyName = propertyName;
    };

    $scope.prevPage = function () {
        $scope.loadGrid($scope.Utility.prevPage($scope.currentPage));
    };

    $scope.nextPage = function () {
        $scope.loadGrid($scope.Utility.nextPage($scope.currentPage, $scope.VirtualItemCount, $scope.PageSize, 10));
    }

    $scope.setPage = function () {
        $scope.currentPage = this.n;
    };

    $scope.DeleteOrder = function (orderID) {
        $scope.BeautyParloursAT = typeof localStorage.getItem('BeautyParloursAT') == "string" &&  localStorage.getItem('BeautyParloursAT') != "undefined" ? localStorage.getItem('BeautyParloursAT') : "";
        if(confirm('Are you sure you want to delete this Order?')){
            $http({
                url: window.location.origin+'/ServerPHP/Admin/DeleteOrders.php',
                method: "POST",
                headers: {
                  'Content-Type': 'multipart/form-data'
              },
              data:{"ID":orderID,'Token':$scope.BeautyParloursAT}
          })
            .then(function(response) {
                if(response.data[0].Result=="-1"){
                    localStorage.setItem('BeautyParloursAT','');
                    window.location = window.location.origin+'/Admin/index.html';
                }
                if(response.data[0].Result=="True"){
                    alert('Order Deleted Successfully!');
                    $scope.loadGrid(1);
                } else {
                    alert('Order Deletion Failed!');
                }
            });
        }
    };

    $scope.DeliveryTime = [];
    for (var i = 0; i < 24; i++) {
        if(i<12){
            $scope.DeliveryTime.push({"text":(i<10?"0"+i.toString():i)+" am","value":(i<10?"0"+i.toString():i)+" am"});
        } else {
            $scope.DeliveryTime.push({"text":((i==12?12:i-12)<10?"0"+(i==12?12:i-12).toString():(i==12?12:i-12))+" pm","value":((i==12?12:i-12)<10?"0"+(i==12?12:i-12).toString():(i==12?12:i-12))+" pm"});
        }
    }

    $scope.InitEditNewProduct =function (currentProduct) {
        $scope.AlterProduct = angular.copy(currentProduct);
        // Get the modal
        var modal = document.getElementById('myModal');
        // Get the button that opens the modal
        var btn = document.getElementById("myBtn");
        modal.style.display = "block";
    };

$scope.UpdateOrder = function () {
    $scope.BeautyParloursAT = typeof localStorage.getItem('BeautyParloursAT') == "string" &&  localStorage.getItem('BeautyParloursAT') != "undefined" ? localStorage.getItem('BeautyParloursAT') : "";
    $scope.AlterProduct.Token=$scope.BeautyParloursAT;
    $scope.AJAXTotalRequests = $scope.AlterProduct.FinalCartProducts.length;
    $scope.AJAXCompletedRequests = 0;
    $scope.loading = true;
    $http({
        url: window.location.origin+'/ServerPHP/Admin/UpdateOrders.php',
        method: "POST",
        headers: {
          'Content-Type': 'multipart/form-data'
      },
      data:$scope.AlterProduct
    })
    .then(function(response) {
        if(response.data[0].Result=="-1"){
            localStorage.setItem('BeautyParloursAT','');
            window.location = window.location.origin+'/Admin/index.html';
        }
        if(response.data[0].Result=="True"){
            for (var i = 0; i < $scope.AlterProduct.FinalCartProducts.length; i++) {
                $scope.AlterProduct.FinalCartProducts[i].Token=$scope.BeautyParloursAT;
                $http({
                    url: window.location.origin+'/ServerPHP/Admin/UpdateSubOrders.php',
                    method: "POST",
                    headers: {
                      'Content-Type': 'multipart/form-data'
                  },
                  data:$scope.AlterProduct.FinalCartProducts[i]
              }).then(function (response) {
                if(response.data[0].Result=="-1"){
                    localStorage.setItem('BeautyParloursAT','');
                    window.location = window.location.origin+'/Admin/index.html';
                }
                $scope.AJAXCompletedRequests++;
                if($scope.AJAXTotalRequests == $scope.AJAXCompletedRequests){
                    if($scope.productsToDelete.length>0){
                        $http({
                        url: window.location.origin+'/ServerPHP/Admin/DeleteSubOrders.php',
                        method: "POST",
                        headers: {
                          'Content-Type': 'multipart/form-data'
                      },
                          data:{"ID":$scope.AlterProduct.ID,"ProductIDToDelete":$scope.productsToDelete.join(','),"Token":$scope.BeautyParloursAT}
                        }).then(function(response) {
                             if(response.data[0].Result=="True"){
                                $scope.loadGrid(1);
                            } else {
                                alert('Error occoured while deleting Sub-Order! Contact Admin immediately.');
                                $scope.loadGrid(1);
                            }
                        });
                    } else {
                        $scope.loadGrid(1);
                    }
                }
                if(response.data[0].Result=="True"){

                } else {
                    alert('Order-SubProduct Updation Failed! Contact Admin immediately.');
                }
            });
          }
          alert('Data Updated Successfully!');
          document.getElementById('myModal').style.display = "none";
      } else {
        alert('Data Updation Failed!');
    }
});
};

$scope.UpdateSubProductsCosts = function () {
    var TotalCost = 0;
    for (var i = 0; i < $scope.AlterProduct.FinalCartProducts.length; i++) {
        TotalCost += Number($scope.AlterProduct.FinalCartProducts[i].TotalCost);
    }
    $scope.AlterProduct.TotalCost = Math.round(TotalCost);
    $scope.TotalCostChange();
};

$scope.DeleteSubProduct = function (ID) {
    $scope.productsToDelete.push(ID);
    for (var i = 0; i < $scope.AlterProduct.FinalCartProducts.length; i++) {
        if ($scope.AlterProduct.FinalCartProducts[i].ID == ID){
            $scope.AlterProduct.FinalCartProducts.splice(i,1);
            break;
        }
    }
    $scope.UpdateSubProductsCosts();
}

$scope.TotalCostChange = function () {
    $scope.AlterProduct.FinalCost = Math.round($scope.AlterProduct.TotalCost * (1-($scope.AlterProduct.DiscountPercentage / 100)));
};

$scope.DiscountPercentChange = function () {
 $scope.AlterProduct.FinalCost = Math.round($scope.AlterProduct.TotalCost * (1-($scope.AlterProduct.DiscountPercentage / 100)));
};

$scope.FinalCostChange = function () {
    $scope.AlterProduct.DiscountPercentage = Math.round(100 - (($scope.AlterProduct.FinalCost / $scope.AlterProduct.TotalCost) * 100));
};

$scope.Logout = function () {
    $http({
            url: window.location.origin+'/ServerPHP/Admin/Logout.php',
            method: "POST",
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            data:{'Token':$scope.BeautyParloursAT}
        })
        .then(function(response) {
            if(response.data[0].Result=="True"){
                localStorage.setItem('BeautyParloursAT','');
                window.location = window.location.origin+'/Admin/index.html';
            } else {
                console.log(response);
                alert('Not able to Logout! Contact Sysadmin ASAP.');
            }
        });
}

}]);
app.service("UtilityObject", Utility);