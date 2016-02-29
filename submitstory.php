<?php
    include 'header.php';
    require 'database.php';
    require 'logincheck.php';
	require 'csrfcheck.php';
	require 'logoutbutton.php';
    if (!$loggedin){
        Header("Location:main.php");
		exit;
    }
     
	if (isset($_POST['title'])&&isset($_POST['content'])&&isset($_POST['tag'])&&isset($_POST['link'])){
		// get story title, body and comments
		$title = test_input($_POST['title']);
		$content = test_input($_POST['content']);
		$tag = test_input($_POST['tag']);
		$link = filter_var($_POST['link'], FILTER_SANITIZE_URL);
		$stmt = $mysqli->prepare("
						 INSERT INTO stories (title, body, user_id, tag, link) VALUES (?, ?, ?, ?, ?)
									");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ssiss', $title, $content, $user_id, $tag, $link);
		$stmt->execute();
		$stmt->close();    
		Header("Location:main.php");
		exit;
	}else{
		Header("Location:main.php");
		exit;
	}
    
    

    
    
?>