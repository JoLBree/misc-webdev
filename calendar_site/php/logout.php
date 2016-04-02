<?php
header("Content-Type: application/json");
require 'loginPHPcheck.php'; // includes session_start() and gives the variable (bool) $loggedin

//****************************************************    
// log out //
//****************************************************

if ($loggedin){
	require 'csrfcheck.php';
    // remove token hash
	$stmt = $mysqli->prepare("UPDATE users SET crypted_token = NULL WHERE user_id = ?");
	$stmt->bind_param('s', $user_id);
	$stmt->execute();
	$stmt->close(); 
	// close the session
	unset($_SESSION['user_id']);
	unset($_SESSION['token']);
	session_destroy();
	echo json_encode(array(
		"success" => true
		));
	exit();

}else{
		echo json_encode(array(
		"success" => "done",
		"message" => "Already logged out"
		));
}


?>