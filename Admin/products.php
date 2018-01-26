<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Admin Panel</title>
  <!-- Bootstrap core CSS -->
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom styles for this template -->
    <link href="/css/shop-homepage.css" rel="stylesheet">
  <style type="text/css">
   .sortorder:after {
    content: '\25b2';
  }

  .sortorder.reverse:after {
    content: '\25bc';
  }

  ul.pagination {
    display: inline-block;
    padding: 0;
    margin: 0;
  }

  ul.pagination li {
    display: inline;
  }

  ul.pagination li a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
    transition: background-color .3s;
  }

  ul.pagination li a.active {
    background-color: #337AC6;
    color: white;
  }

  ul.pagination li a:hover:not(.active) {
    background-color: #ddd;
  }
  /* The Modal (background) */
  .modal {
    padding-top: 56px;
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
  }

  /* Modal Content/Box */
  .modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
  }

  /* The Close Button */
  .close {
    color: #000;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.5/angular.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.5/angular-touch.min.js"></script>

<script type="text/javascript" src="../scripts/Product_Model.js"></script>
<script type="text/javascript" src="../scripts/AngularGrid_Utility.js"></script>
<script type="text/javascript" src="../scripts/Aproducts.js"></script>

</head>

<body ng-app="RanaSweetsApp" ng-controller="productController">
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <div style="overflow:auto; width: 80%">
        <div style="float:left;">
          <a class="navbar-brand" href="./index.html">Test Beauty Parlour</a>
        </div>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="dropdown">
              <a href="#" class="nav-link" data-toggle="dropdown" style="width: 120%">Manage &#8615;</a>
              <ul class="dropdown-menu" style="background-color: #343a40!important">
                <li ><a class="nav-link" href="./categories.html">Products</a></li>
                <li ><a class="nav-link" href="./faqs.html">FAQs</a></li>
              </ul>
            </li>
            <li class="dropdown nav-item active"> 
              <a href="#" class="nav-link" data-toggle="dropdown" style="width: 120%">Orders &#8615;</a>
              <ul class="dropdown-menu" style="background-color: #343a40!important">
                <li ><a class="nav-link" href="./orders.html?Mode=1">All</a></li>
                <li ><a class="nav-link" href="./orders.html?Mode=2">Un Serviced</a></li>
                <li ><a class="nav-link" href="./orders.html?Mode=3">Danger</a></li>
                <li ><a class="nav-link" href="./orders.html?Mode=4">Serviced</a></li>
              </ul>
            </li>
            <li class="nav-item" style="cursor: pointer;">
              <a class="nav-link" ng-click="Logout()">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Page Content -->
    <br />
    <div class="container" >
      <div class="row">
      <?php
          require_once '../ServerPHP/Utils/DBConfig.php';
          require_once '../ServerPHP/Utils/PHPFunctions.php';
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Token = $_POST['Token'];

              $IsAuthenticated = ValidateToken($Token,$conn);
              if($IsAuthenticated  != 1){
                echo "<script type=\"text/javascript\">window.location = window.location.origin+'/Admin/index.html';</script>";
                exit();
              }
          }

          parse_str($_SERVER['QUERY_STRING']);

          $target_dir = $_SERVER['DOCUMENT_ROOT']."images/Categories/";
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
            $sql = "update `categories` set `PhotoURL`='$NewFileName' where `ID`=$CategoryID";
            $result = mysqli_query($conn, $sql);
            if ($result===True) {
              echo "<div class=\"col-md-12\"><div class=\"alert alert-success\"><strong>Success!</strong> Image updated successfully.</div></div>";
            } else {
              $errors[] = 'Error occured while registering image to database';
                print_r($errors);
            }
          }

          $sql = "Select`Name` ,`PhotoURL` FROM `categories` where `ID`=$CategoryID";
          $result = mysqli_query($conn, $sql);
          while($row = mysqli_fetch_array($result)) {
            $PhotoURL = $row["PhotoURL"];
            $CategoryName = $row["Name"];
          }

          if($PhotoURL == "" || $PhotoURL == NULL){
            $PhotoURL = "default.png";
          }
          echo "<div class=\"col-md-12\"><h1 style=\"text-align:center;\">$CategoryName</h1></div>";
          echo "<div class=\"col-md-4\" ><div style=\"width: 250px; height: 150px;\"><img src=\"/images/Categories/$PhotoURL\" style=\"width: 100%; height: 100%\"></div></div>" ;

          ?>

             <form action="#" method="POST" enctype="multipart/form-data">
                <input type="file" name="image" />
                <input type="hidden" name="Token" value="" id="Token">
                <input type="submit"/>
             </form>
        <script type="text/javascript">
          if((typeof localStorage.getItem('BeautyParloursAT') == "string" &&  localStorage.getItem('BeautyParloursAT') != "undefined" ? localStorage.getItem('BeautyParloursAT') : "").length !=100){
            window.location = window.location.origin+'/Admin/index.html';
          }
          document.getElementById("Token").value = typeof localStorage.getItem('BeautyParloursAT') == "string" &&  localStorage.getItem('BeautyParloursAT') != "undefined" ? localStorage.getItem('BeautyParloursAT') : "";
        </script>
      </div>
      <div class="row">
        <div class="col-lg-6">
          Show <select ng-model="PageSize" ng-change="loadGrid(1)">
          <option ng-selected="true" ng-value="10">10</option>
          <option ng-value="20">20</option>
          <option ng-value="50">50</option>
        </select> entries
      </div>
      <div class="col-lg-6" style="text-align: right;">
      <button id="myBtn" class="btn btn-success btn-md" ng-click="InitAddNewProduct()">+ Add New Product</button>
        <input type="text" placeholder="Search" ng-model="search">
      </div>
    </div>
    <div class="row" ng-show="loading">
      <div class="col-lg-12">
      <div style="text-align: center;">
        <IMG SRC="../images/loading.gif" ALT="loading..." style="width: 50px; height: 50px;margin-top: 100px" >
      </div>
      </div>
    </div>
    <div class="row" ng-show="!loading">
      <div class="col-lg-12">
        <table class="table table-responsive table-bordered">
          <thead>
            <tr>
              <th>
                <a style=" cursor: pointer" ng-click="sortBy('Name')">Name</a>
                <span class="sortorder" ng-show="propertyName === 'Name'" ng-class="{reverse: reverse}"></span>
              </th>
              <th>
                <a style=" cursor: pointer" ng-click="sortBy('Price')">Price</a>
                <span class="sortorder" ng-show="propertyName === 'Price'" ng-class="{reverse: reverse}"></span>
              </th>
              <th>
                <a style=" cursor: pointer" ng-click="sortBy('IsActive')">IsActive</a>
                <span class="sortorder" ng-show="propertyName === 'IsActive'" ng-class="{reverse: reverse}"></span>
              </th>
              <th>
                <a style=" cursor: pointer" ng-click="sortBy('CreatedOn')">Created On</a>
                <span class="sortorder" ng-show="propertyName === 'CreatedOn'" ng-class="{reverse: reverse}"></span>
              </th>
              <th>
                <a style=" cursor: pointer" ng-click="sortBy('LastUpdatedOn')">Last Updated On</a>
                <span class="sortorder" ng-show="propertyName === 'LastUpdatedOn'" ng-class="{reverse: reverse}"></span>
              </th>
              <th>
                Edit
              </th>
              <th>
                Delete
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="singleProduct in BindGrid | orderBy:propertyName:reverse | filter : search">
              <td>{{singleProduct.Name}}</td>
              <td>{{singleProduct.Price}}</td>
              <td>{{singleProduct.IsActive==1?'YES':'NO'}}</td>
              <td>{{singleProduct.CreatedOn}}</td>
              <td>{{singleProduct.LastUpdatedOn}}</td>
              <td><button class="btn btn-primary" ng-click='InitEditNewProduct(singleProduct)'>  &#9998;</button></td>
              <td><button class="btn btn-danger" ng-click='DeleteProduct(singleProduct.Name,singleProduct.ID)'> X </button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-lg-3" style="margin-top:30px">
        <div class="form-group">
          {{PagingMessage}}

        </div>
      </div>
      <div class="pagination pagination-sm no-margin pull-right">
        <ul class="pagination disabled">
          <li>
            <a ng-click="prevPage()">« Prev</a>
          </li>
        </ul>
        <ul class="pagination  " ng-repeat="n in Utility.range(1,VirtualItemCount ,PageSize,1)">
          <li><a id="P{{n}}" ng-class="{active: n == currentPage}" ng-click="loadGrid(n)">{{n}}</a></li>
        </ul>
        <ul class="pagination">
          <li>
            <a ng-click="nextPage()">Next »</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <div style="text-align: right;">
          <span class="close">&times;</span>
        </div>
        <div class="container">
          <div class="row">
            <div class="col-md-6">
              Name : 
              <span ng-if="AlterProduct.ID!=''">
                {{AlterProduct.Name}}
              </span>
              <span ng-if="AlterProduct.ID=='' || AlterProduct.ID==null || AlterProduct.ID=='undefined'">
                <input type="text" ng-model="AlterProduct.Name">
              </span>
            </div>
            <div class="col-md-6">
              Price : <input type="text" ng-model="AlterProduct.Price">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              In Active:
              <input type="radio" ng-model="AlterProduct.IsActive" value="1">YES
              <input type="radio" ng-model="AlterProduct.IsActive" value="0">NO
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <button class="btn btn-danger btn-sm" ng-click="ModalSave()">Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap core JavaScript -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
      $('.row').on('click',function(e){
        if($(".navbar-collapse").attr("class").includes("show")){
          e.preventDefault();  $(".navbar-toggler").click();
        }
      });
    </script>
<script type="text/javascript">
  // Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
</body>
</html>