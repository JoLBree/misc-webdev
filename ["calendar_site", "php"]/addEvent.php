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
 // warning !!!  date-time format NOT done!!! 
	else if( !preg_match('/^\d{2}\/\d{2}\/\d{4} \d{1,2}:\d{1,2} [AP]M$/', $date) ){
		echo json_encode(array(
			"success" => false,
			"message" => "Invalid date. Should be in this format:hah, but your input is ".$date
			));
		exit;
	} 
	else {
		// check if event tilte already taken
		$stmt = $mysqli->prepare("SELECT COUNT(event_id) FROM events WHERE title=?");

		if(!$stmt){
			printf("Check Event Title Taken Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $title);
		$stmt->execute();
		$stmt->bind_result($numDuplicatedEvents);
		$stmt->fetch();
		if ($numDuplicatedEvents != 0){
			echo json_encode(array(
				"success" => false,
				"message" => "Event already taken"
				));
			exit;
		}
		$stmt->close();

		$pattern = "/^(\d{2})\/(\d{2})\/(\d{4}) (\d{1,2}):(\d{1,2}) ([AP])M$/"; 
		preg_match_all($pattern, $date, $groups);
		$time = intval($groups[4][0]);
		if($groups[6][0]=='P'){
			$time += 12;
		}

		$date = $groups[3][0].'-'.$groups[1][0].'-'.$groups[2][0].' '.$time.':'.$groups[5][0].':00';
		// add created event to database table
		$stmt = $mysqli->prepare("insert into events (title, datetime, body, user_id, tag) values (?, ?, ?, ?, ?)");
		if(!$stmt){
			printf("Insert Event Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('sssss', $title, $date, $descrption, $user_id, $color);
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