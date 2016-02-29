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
	
	if (isset($_POST['edit_story_id'])){
		$story_id = $_POST['edit_story_id'];
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
		$stmt = $mysqli->prepare("SELECT
                                    stories.title,
                                    stories.body,
                                    stories.link,
                                    stories.tag
                                FROM stories
                                WHERE
                                    story_Id = ?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $story_id);
        $stmt->execute();         
        $stmt->bind_result($title, $body, $link, $tag);
        $stmt->fetch();
		$stmt->close(); 
		
		?>
		<div class="t">

		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
			<span>Title</span> <input type="text" name="newtitle" value ="<?php echo htmlspecialchars($title) ?>"/> <br>
			<!--This has to be in one line. Any line breaks or whitespace between the <textarea> and </textarea> tags will
			be entered in the box, meaning our default entry will have a bunch of line breaks-->
			<span>Content</span> <textarea rows="5" name="newbody"><?php echo htmlspecialchars($body)?></textarea><br>
			<span>Link</span> <input type="text" name="newlink" value ="<?php echo htmlspecialchars($link) ?>"/> <br>
			<input type="radio" name="newtag"  value="Tech"<?php if ($tag == "Tech"){echo "checked";}?>> Tech
			<input type="radio" name="newtag"  value="Sports"<?php if ($tag == "Sports"){echo "checked";}?>> Sports
			<input type="radio" name="newtag"  value="News"> News
			<input type="radio" name="newtag"  value="Entertainment"<?php if ($tag == "Entertainment"){echo "checked";}?>> Entertainment
			<input type="radio" name="newtag"  value="Others"<?php if ($tag == "Others"){echo "checked";}?>> others
			<input type="hidden" name="replace_story_id" value="<?php echo htmlspecialchars($story_id) ?>" />
			<input type="submit" value= "submit">
		</form>
		<br>
		<form action="main.php" method="POST">
			<input type="submit" value= "Cancel">
		</form>
	</div>
		<?php
	}
	
	if (isset($_POST['replace_story_id'])){
		$story_id = $_POST['replace_story_id'];
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
		if (isset($_POST['newtitle']) && isset($_POST['newbody']) && isset($_POST['newlink']) && isset($_POST['newtag'])){
			$title = test_input($_POST['newtitle']);
			$body = test_input($_POST['newbody']);
			$link = filter_var($_POST['newlink'], FILTER_SANITIZE_URL); // cos slashes are allowed in urls
			$tag = $_POST['newtag'];
		
			$stmt = $mysqli->prepare("UPDATE stories
									 SET title = ?,
									 body = ?,
									 link = ?,
									 tag = ?
									 WHERE story_Id = ?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('ssssi', $title, $body, $link, $tag, $story_id);
			$stmt->execute();
			$stmt->close();
			printf("end of edit block");
			Header("Location:main.php");
            exit;
		}
	}

?>
</body>
</html>