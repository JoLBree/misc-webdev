<?php
require 'database.php';

// echo exec('whoami');
	// var_dump($_FILES);
	// var_dump($_POST);
	// echo var_dump($_FILES['uploadedfile']["tmp_name"]);
// $img=$_FILES['img'];
$user_id = 1;
$filename = $_FILES['fileToUpload']["tmp_name"];
$client_id="484caa911766c82";
$handle = fopen($filename, "r");
$data = fread($handle, filesize($filename));
$pvars   = array('image' => base64_encode($data));
$timeout = 30;
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
$out = curl_exec($curl);
curl_close ($curl);
$pms = json_decode($out,true);
$url=$pms['data']['link'];
if($url!=""){
	// echo "<h2>Uploaded Without Any Problem</h2>";
	// echo "<img src='$url'/>";

$dateAndTime = sprintf("%s-%s-%s %s:%s:00", $_POST["year"], $_POST["month"], $_POST["date"], $_POST["hour"], $_POST["minutes"]);

$stmt = $mysqli->prepare("INSERT INTO events (user_id, dateAndTime, flyer) VALUES (?, ?, ?);");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('iss', $user_id, $dateAndTime, $url);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("SELECT LAST_INSERT_ID();");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$stmt->bind_result($event_id);
while($stmt->fetch()){
		// var_dump($event_id);
		// $full_path = sprintf("/flyers/%s", $filename);
}
$stmt->close();  
 	// var_dump();
echo $event_id;
// $full_path = sprintf("corkboardFlyers/%s.jpg", $event_id);
// echo $full_path;

// if( move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $full_path) ){
// 	$success = true;
// }else{
// 	$success = false;
// }
// exit;


	// Get the filename and make sure it is valid
	// $filename = basename($_FILES['uploadedfile']['name']);
	// if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	// 	echo "Invalid filename";
	// 	exit;
	// }

	// // Get the username and make sure it is valid
	// $username = $_POST['user'];
	// if( !preg_match('/^[\w_\-]+$/', $username) ){
	// 	echo "Invalid username";
	// 	exit;
	// }
}else{
	echo "<h2>There's a Problem</h2>";
	echo $pms['data']['error'];  

}

?>








