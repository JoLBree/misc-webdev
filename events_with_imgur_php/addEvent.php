<?php
// http://www.phpro.org/tutorials/Storing-Images-in-MySQL-with-PHP.html
// bad practices, but syntax: http://www.php-mysql-tutorial.com/wikis/mysql-tutorials/uploading-files-to-mysql-database.aspx
// http://www.tutorialspoint.com/android/android_php_mysql.htm
require 'database.php';
	// if (isset($_POST['author_id']) && isset($_POST['story_id']) && isset($_POST['content']) && (!empty($_POST['content']))){

// $_FILE

$stmt = $mysqli->prepare("INSERT INTO events (?, ?, ?) VALUES (1, 'My Flyer', '2016-03-14 18:00:00');");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
	$stmt->bind_param('iss', $author_id, $content, $story_id);
$stmt->execute();

$stmt->bind_result($flyer, $dateAndTime);
while($stmt->fetch()){
	echo htmlentities($dateAndTime);
}
$stmt->close();  


?>