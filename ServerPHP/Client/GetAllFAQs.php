<?php

require_once '../Utils/DBConfig.php';
			
parse_str($_SERVER['QUERY_STRING']);

// Query the database to show all the tables.
$query = "SELECT `ID`, `Question`, `Answer` FROM `faq` WHERE `IsActive` = 1 ORDER by `Priority`,`LastUpdatedOn`";
$data = array();
$result = mysqli_query($conn, $query);
// Print the results of the query.
while($row = mysqli_fetch_array($result)) {
	$data[] = $row;
}
echo json_encode($data);

?>