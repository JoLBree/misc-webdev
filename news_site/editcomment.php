<!DOCTYPE html>
<html>
<head>
<title>News Site</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
	include 'header.php';
    require 'database.php';
    require 'logincheck.php';
	if (!$loggedin){ //non-logged in users are not allowed here
		    echo "not logged in, you dont belong here!";
            Header("Location:main.php");
            exit;
	}
	require 'logoutbutton.php';
	require 'csrfcheck.php'; // must come after login check
	
	if (isset($_POST['edit_comment_id'])){
		$comment_id = $_POST['edit_comment_id'];
        $guess_user_id = $_SESSION['user_id'];
        
        // check that story_id belongs to that user
        $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE comment_Id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $comment_id);
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
		$stmt = $mysqli->prepare("SELECT
									stories.title,
									stories.story_Id,
                                    comments.comment
                                FROM comments
								JOIN stories on (stories.story_Id=comments.story_Id)
                                WHERE
                                    comment_Id = ?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();         
        $stmt->bind_result($title, $story_id, $comment);
        $stmt->fetch();
		$stmt->close(); 
		
		?>
		<div class="t">
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
			<span><?php echo htmlspecialchars($title) ?></span> <br>
			<!--This has to be in one line. Any line breaks or whitespace between the <textarea> and </textarea> tags will
			be entered in the box, meaning our default comment entry will have a bunch of line breaks-->
		    <textarea rows="5" name="newcomment"><?php echo htmlspecialchars($comment)?></textarea><br>
			<input type="hidden" name="replace_comment_id" value="<?php echo htmlspecialchars($comment_id) ?>" />
			<input type="submit" value= "submit">
		</form>
		<br>		

		<form action="viewstory.php" method="POST">
			<input type="submit" value= "Cancel">
			<input type="hidden" name="story_id" value="<?php echo htmlspecialchars($story_id) ?>" />
		</form>
		</div>
		<?php
	}
	
	if (isset($_POST['replace_comment_id'])){
		$comment_id = $_POST['replace_comment_id'];
        $guess_user_id = $_SESSION['user_id'];
        
        // check that comment_id belongs to that user
        $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE comment_Id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
        if ($user_id !=  $guess_user_id){
            echo "comment doesn't belong to you!";
            Header("Location:main.php");
            exit;
        }
		
		// comment belongs to user
		if (isset($_POST['newcomment'])){
			$comment = test_input($_POST['newcomment']);			
			$stmt = $mysqli->prepare("UPDATE comments
									 SET comment = ?
									 WHERE comment_Id = ?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('si', $comment, $comment_id);
			$stmt->execute();
			$stmt->close();
			Header("Location:main.php");
			exit;
		}
	}
?>
</body>
</html>