<?php

require 'database.php';
require 'loginPHPcheck.php'; // does session_start() and gives the variable (bool) $loggedin
// no need csrf check cos not priviledged access
header("Content-Type: application/json");

if ($loggedin){
	echo json_encode(array(
			"success" => false,
			"message" => "Already loggedin"
			));
	exit;
}
else if (isset($_POST['new_username']) && isset($_POST['new_password'])){

	$username = $_POST['new_username'];
	$password = $_POST['new_password'];
	if( !preg_match('/^[\w_\-]+$/', $username) ){
		echo json_encode(array(
			"success" => false,
			"message" => "Invalid username"
			));
		exit;
	}

	else if( !preg_match('/^[A-Za-z\d$@$!%*#?&]{8,}$/', $password) ){
		echo json_encode(array(
			"success" => false,
			"message" => "Password must contain at least 8 letters, numbers, or special characters"
			));
		exit;
	} else {
		// check if username already taken
		$stmt = $mysqli->prepare("SELECT COUNT(user_id) FROM users WHERE username=?");

		if(!$stmt){
			printf("Check Username Taken Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($numDuplicatedUsers);
		$stmt->fetch();
		if ($numDuplicatedUsers != 0){
			echo json_encode(array(
				"success" => false,
				"message" => "Username already taken"
				));
			exit;
		}
		$stmt->close();

		// generate token, hash created password and token		
		$token = substr(md5(rand()), 0, 10);
		$pwd_hash = crypt($password);
		$token_hash = crypt($token);
		
		// add created username, password hash and session token hash to database table
		$stmt = $mysqli->prepare("insert into users (username, crypted_password, crypted_token) values (?, ?, ?)");
		if(!$stmt){
			printf("Insert User Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('sss', $username, $pwd_hash, $token_hash);
		$stmt->execute();
		
		// retrieve generated (auto_incremented) user_id
		$stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM users WHERE username=?");
		if(!$stmt){
			printf("Get User ID Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($cnt, $user_id);
		$stmt->fetch();
		$stmt->close();

		$_SESSION['token']=$token;
		$_SESSION['user_id']=$user_id;
		echo json_encode(array(
			"success" => true
			));
		exit();
	}
}
exit();
?>