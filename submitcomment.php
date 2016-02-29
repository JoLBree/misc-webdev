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
	if (isset($_POST['author_id']) && isset($_POST['story_id']) && isset($_POST['content']) && (!empty($_POST['content']))){
		// get story title, body and comments
		$author_id = test_input($_POST['author_id']);
		$content = test_input($_POST['content']);
		$story_id = test_input($_POST['story_id']);
		$stmt = $mysqli->prepare("
						 INSERT INTO comments (user_id, comment, story_Id) VALUES (?, ?, ?)
									");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('isi', $author_id, $content, $story_id);
		$stmt->execute();
		$stmt->close();  
        Header("Location:main.php");
		exit;
    } else{
		Header("Location:main.php");
		exit;
	}
    

    
    
?>