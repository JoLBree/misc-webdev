<?php
    include 'header.php';
    require 'database.php';
    require 'logincheck.php';
    if (!$loggedin){
        Header("Location:main.php");
        exit;
    }
    require 'csrfcheck.php';
    
    if (isset($_POST['user_id']) && isset($_POST['story_id']) && isset($_POST['do'])){
        $user_id = $_POST['user_id'];
		$story_id = $_POST['story_id'];
		$do = $_POST['do'];
        
        if ($do == "un_fav"){
            $stmt = $mysqli->prepare("DELETE FROM favs WHERE (story_Id = ? AND user_id = ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('ii', $story_id, $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($do == "fav"){
            $stmt = $mysqli->prepare("INSERT INTO favs (story_Id, user_id) VALUES (?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('ii', $story_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        Header("Location:viewstory.php?story=$story_id");
        exit;
		
    }
    
?>