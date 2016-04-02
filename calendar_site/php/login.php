<?php

// login.php
require 'database.php';

header("Content-Type: application/json");
if (isset($_POST['username']) && isset($_POST['password'])){
$username = $_POST['username'];
$pwd_guess = $_POST['password'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid username"
		));
	exit;
}
if( !preg_match('/^[A-Za-z\d$@$!%*#?&]+$/', $pwd_guess) ){
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid password"
		));
	exit;
}

// Check to see if the username and password are valid.
$stmt = $mysqli->prepare("SELECT COUNT(*), user_id, crypted_password FROM users WHERE username=?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($cnt, $user_id, $pwd_hash);
$stmt->fetch();
$stmt->close();

    // Compare the submitted password to the actual password hash
if( $cnt == 1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash){
    // Login succeeded!
	ini_set("session.cookie_httponly", 1);
	session_start();
	$_SESSION['user_id'] = $user_id;
	$_SESSION['token'] = substr(md5(rand()), 0, 10);
	$token_hash = crypt($_SESSION['token']);

    // insert hashed token       
	$stmt = $mysqli->prepare("UPDATE users SET crypted_token=? WHERE user_id=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('ss', $token_hash, $user_id);
	$stmt->execute();
	if (isset($_SESSION['token']) && isset($_SESSION['user_id'])){
		echo json_encode(array(
			"success" => true,
			"token" => $token_hash
			));
		exit();
	}
}else{
    // Login failed
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
		));
	exit();
}
}
exit();
?>