<?php

require_once '../Utils/DBConfig.php';
parse_str($_SERVER['QUERY_STRING']);
$sql = "Call A_GetAllCategories ($PageIndex,$PageSize)";
$data = array();
$result = mysqli_query($conn, $sql);
// Print the results of the query.
while($row = mysqli_fetch_array($result)) {
	$data[] = $row;
}
echo json_encode($data);

?>