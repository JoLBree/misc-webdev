<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = filter_var($data, FILTER_SANITIZE_STRING);
	//$data = htmlspecialchars($data); // htmlspecialchars is for php stuff being output to html, not input from html to php
	return $data;
}
	?>
