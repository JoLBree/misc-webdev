<?php
header("Content-Type: application/json");
require 'database.php';
$loggedin = false;
ini_set("session.cookie_httponly", 1);
session_start();
if (isset($_SESSION['token']) && isset($_SESSION['user_id'])){
	$user_id = $_SESSION['user_id'];
	$token_guess = $_SESSION['token'];
	$stmt = $mysqli->prepare("SELECT COUNT(*), crypted_token FROM users WHERE user_id=?");
        // Bind the parameter
	$stmt->bind_param('s', $user_id);
	$stmt->execute();        
        // Bind the results
	$stmt->bind_result($cnt, $correct_token);
	$stmt->fetch();
	$stmt->close(); 
        // Compare the submitted token to the actual token hash
	if( $cnt == 1 && crypt($token_guess, $correct_token)==$correct_token){
		$stmt = $mysqli->prepare("SELECT
			username
			FROM users
			WHERE
			user_id = ?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('i', $user_id);
		$stmt->execute();         
		$stmt->bind_result($username);
		$stmt->fetch();
		$stmt->close(); 
            // printf("Hello %s, you are logged in", $username);
		$loggedin = true;
		echo json_encode(array(
			"loggedin" => true,
			"username" => $username,
			"token" => $correct_token
			));
		exit();

	} else{
			// someone not logged in who somehow has a session. close session and redirect to main!
		unset($_SESSION['user_id']);
		unset($_SESSION['token']);
		session_destroy();
		echo json_encode(array(
			"loggedin" => false
			));
		exit();
	}
}

// not logged in user without a session
echo json_encode(array(
	"loggedin" => "hey"
	));
exit();
?>