<?php
require 'database.php';
require 'loginPHPcheck.php'; // starts session and provides bool $loggedin
header("Content-Type: application/json");

if ($loggedin && isset($_POST['year']) && isset($_POST['month']) && isset($_POST['date']) && isset($_SESSION['user_id']) ){
	require 'csrfcheck.php';
	$date = $_POST['year']."-".$_POST['month']."-".$_POST['date']." 00:00:00";
	// $date = $_POST['month']."/".$_POST['date']."/".$_POST['year']." 00:00:00";

	$stmt = $mysqli->prepare("SELECT event_id, title, body, tag, datetime 
		from events 
		where user_id = ? and (datetime BETWEEN ? AND (DATE_ADD(?, INTERVAL 42 DAY)))
		ORDER BY datetime ASC;");
	$userid = $_SESSION['user_id'];
	$stmt->bind_param('iss', $userid, $date, $date);
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->execute();

	$result = $stmt->get_result();

	$rows = [];
	while($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}
	echo json_encode($rows);	 
	$stmt->close();
	exit;
}
else{
	$emptyArray = [];
	echo json_encode($emptyArray);
	exit;
}



?>
