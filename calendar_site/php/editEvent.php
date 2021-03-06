<?php
require 'database.php';
require 'loginPHPcheck.php'; // does session_start() and gives the variable (bool) $loggedin
// no need csrf check cos not priviledged access
header("Content-Type: application/json");
if (!$loggedin){
	echo json_encode(array(
			"success" => false,
			"message" => "Please Log in "
			));
	exit();
}
else if (isset($_POST['new_title']) && isset($_POST['new_date'])&& isset($_POST['new_description'])&& isset($_POST['new_color'])){
	require 'csrfcheck.php';
	$event_id = $_POST['event_id'];
	$title = $_POST['new_title'];
	$date = $_POST['new_date'];
	$descrption = $_POST['new_description'];
	$color = $_POST['new_color'];
	if( !preg_match('/^[\w_\- ]+$/', $title) ){
		echo json_encode(array(
			"success" => false,
			"message" => "Invalid title"
			));
		exit;
	}
	else if( !preg_match('/^\d{2}\/\d{2}\/\d{4} \d{1,2}:\d{1,2} [AP]M$/', $date) ){
		echo json_encode(array(
			"success" => false,
			"message" => "Invalid date. Should be in this format:hah, but your input is ".$date
			));
		exit;
	}  
	else {
		$pattern = "/^(\d{2})\/(\d{2})\/(\d{4}) (\d{1,2}):(\d{1,2}) ([AP])M$/"; 
		preg_match_all($pattern, $date, $groups);
		$time = intval($groups[4][0]);
		if($groups[6][0]=='P'){
			$time += 12;
		}
		$date = $groups[3][0].'-'.$groups[1][0].'-'.$groups[2][0].' '.$time.':'.$groups[5][0].':00';	
		$stmt = $mysqli->prepare("UPDATE events SET title = ?, datetime = ?, body = ?, user_id = ? , tag = ? where event_id = ?");
		if(!$stmt){
			printf("Insert Event Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('sssssi', $title, $date, $descrption, $user_id, $color, $event_id);
		$stmt->execute();
		$stmt->close();
		echo json_encode(array(
				"success" => true,
				"message" => "Success!"
				));
		
		exit();
	}
}
exit();
?>