<?php
// header('Content-type: application/json');
require 'database.php';

class Event{
	var $flyer;
	var $dateAndTime;

	function __construct($flyr, $dT){
		$this->flyer = $flyr;
		$this->dateAndTime = $dT;
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	$file_contents = urldecode(file_get_contents('php://input'));
	for ($i = 0; $i <= 31; ++$i) { 
		$file_contents = str_replace(chr($i), "", $file_contents); 
	}
	$file_contents = str_replace(chr(127), "", $file_contents);

// This is the most common part
// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
// here we detect it and we remove it, basically it's the first 3 characters 
	if (0 === strpos(bin2hex($file_contents), 'efbbbf')) {
		$file_contents = substr($file_contents, 3);
	}
	stripslashes($file_contents);


	// echo var_dump($file_contents);
	$json = json_decode(substr($file_contents,5));
	// echo var_dump($json);



	// echo "JSON last erros: ".json_last_error();
	$date = $json->{'year'}."-".$json->{'month'}."-".$json->{'date'}." 00:00:00";


	$stmt = $mysqli->prepare("
		SELECT event_id, dateAndTime FROM events WHERE dateAndTime BETWEEN ? AND (DATE_ADD(?, INTERVAL 1 WEEK)) ORDER BY dateAndTime ASC;");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('ss', $date, $date);
	$stmt->execute();

	$data = array();
	$i = 0;

	$stmt->bind_result($flyer, $dateAndTime);
	while($stmt->fetch()){
		$tempEvent = new Event("http://ec2-52-89-88-79.us-west-2.compute.amazonaws.com/~Josephine/corkboard/flyers/".$flyer.".jpg", $dateAndTime);
	// echo json_encode($tempEvent, JSON_FORCE_OBJECT, JSON_PRETTY_PRINT);
		$data[$i] = $tempEvent;
		$i++;

	}
	$stmt->close();  
	echo json_encode($data, JSON_FORCE_OBJECT, JSON_PRETTY_PRINT);

}
?>