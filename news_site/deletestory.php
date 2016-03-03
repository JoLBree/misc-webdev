<?php
    require 'database.php';
    require 'logincheck.php';
    if (!$loggedin){ //non-logged in users are not allowed here
		    echo "not logged in, you dont belong here!";
            Header("Location:main.php");
            exit;
	}
	require 'csrfcheck.php'; // must come after login check
    if (isset($_POST['del_story_id'])){
        $story_id = $_POST['del_story_id'];
        $guess_user_id = $_SESSION['user_id'];
        
        // check that story_id belongs to that user
        $stmt = $mysqli->prepare("SELECT user_id FROM stories WHERE story_Id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
        if ($user_id !=  $guess_user_id){
            echo "story doesn't belong to you!";
            Header("Location:main.php");
            exit;
        }
        
        // story belongs to user
        // delete comments first, since they depend on stories
        $stmt = $mysqli->prepare("DELETE FROM comments WHERE story_Id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->close();
        // delete story
        $stmt = $mysqli->prepare("DELETE FROM stories WHERE story_Id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }         
        $stmt->bind_param('i', $story_id);         
        $stmt->execute();         
        $stmt->close();
        
        Header("Location:main.php");
		exit();
    }
    
    
?>