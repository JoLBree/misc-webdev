<?php

if (!isset($_POST['token'])){
		echo json_encode(array(
		"success" => "nope, no token"
		));
        die("Request forgery detected");
}
// if($_SESSION['token'] != $_POST['token']){
// crypt($pwd_guess, $pwd_hash)==$pwd_hash
if(crypt($_SESSION['token'],$_POST['token']) != $_POST['token']){
			echo json_encode(array(
		"success" => "tokens dont match"
		));
        die("Request forgery detected session token is ".crypt($_SESSION['token'])." post token is ".$_POST['token']);
}


?>