<?php

require_once '../Utils/DBConfig.php';
			
parse_str($_SERVER['QUERY_STRING']);

// Query the database to show all the tables.
$query = "Select T1.CategoryID,T1.CategoryName,T1.PhotoURL,concat('[', group_concat(concat('{\"ID\":\"',T1.ID,'\",\"Name\":\"',T1.Name,'\",\"Price\":\"',T1.Price,'\"}')),']') as SubProducts FROM (Select Categories.ID as CategoryID,Categories.Name as CategoryName,Categories.PhotoURL,Products.ID,Products.Name,Products.Price FROM products as Products inner join categories as Categories on Products.CategoryID=Categories.ID where Products.IsActive=true and Categories.IsActive=True) as T1 group by CategoryID,CategoryName,PhotoURL";
$data = array();
$result = mysqli_query($conn, $query);
// Print the results of the query.
while($row = mysqli_fetch_array($result)) {
	$data[] = $row;
}
echo json_encode($data);

?>