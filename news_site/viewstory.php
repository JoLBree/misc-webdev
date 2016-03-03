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
    if ($loggedin){
        require 'logoutbutton.php';
    }else{
        require 'loginbutton.php';
    }
    ?>
	<form action="searchuser.php" method="POST">
		<span>Search user: </span> <input type="text" name="search_username"/>
		<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
		<input type="submit" value= "submit">
	</form>
   <!--  <div class= "cmtf">
    <form action="main.php" method="POST">
		<input type="submit" value= "Return to Home">
	</form>
    </div> -->
    <?php 
    if (isset($_POST['story_id'])||isset($_GET['story'])){
            // get story title, body and comments
		if (isset($_GET['story'])){
			$story_id = $_GET['story'];	
		}else {
			$story_id = $_POST['story_id'];	
		}
		
		// check if story exists
		$stmt = $mysqli->prepare("SELECT COUNT(story_id) FROM stories WHERE story_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $story_id);
		$stmt->execute();
		$stmt->bind_result($numStories);
		$stmt->fetch();
		if ($numStories != 1){
		    Header("Location:main.php");
			exit;
		}
		$stmt->close();
		
		// get story info
        $stmt = $mysqli->prepare("SELECT
            users.username,
			stories.user_Id,
			stories.story_Id,
            stories.title,
            stories.body,
            stories.link,
            stories.tag
            FROM stories
            JOIN users on (users.user_id=stories.user_id)
            WHERE
            story_Id = ?;
            ");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->bind_result($sqlusername, $story_user_id, $story_id, $sqltitle, $sqlbody, $sqllink, $sqltag);
        echo "<h2>Story:</h2>";
        while($stmt->fetch()){
            printf("\t<div id=\"story\">%s posted: <br><h3>%s</h3><br><p>%s</p><br>Tag: %s\n",
                htmlspecialchars($sqlusername),
                htmlspecialchars($sqltitle),
                htmlspecialchars($sqlbody),
                htmlspecialchars($sqltag)
                );
			?>
			<a href="<?php printf("http://%s",$sqllink);?>"><?php echo htmlspecialchars($sqllink);?></a>
			<?php

			if ($loggedin && ($user_id == $story_user_id)){
				?><br>
				<form action="editstory.php" method="POST">
					<input type="hidden" name="edit_story_id" value="<?php echo htmlentities($story_id);?>"/>
					<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
					<input type="submit" value= "edit">
				</form>
				<form action="deletestory.php" method="POST">
					<input type="hidden" name="del_story_id" value="<?php echo htmlentities($story_id);?>"/>
					<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
					<input type="submit" value= "delete">
				</form>
				<?php
			} 
        }
        $stmt->close();
        require 'favcheckbutton.php';
        echo "</div>";
    //  display the the comments of this story
        $stmt = $mysqli->prepare("SELECT
            users.username,
			users.user_id,
            comments.comment,
			comments.comment_Id
            FROM comments
            JOIN users on (users.user_id=comments.user_id)
            WHERE
            story_Id = ?;
            ");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->bind_result($cmusername, $comment_user_id ,$comment, $comment_id);
        // echo "<ul>\n";
        echo "<h2>Commets:</h2> ";
        while($stmt->fetch()){
			printf("<div class=\"story\">%s <br>", $comment);
            if ($loggedin && ($user_id == $comment_user_id)){
				?>
				<form action="editcomment.php" method="POST">
					<input type="hidden" name="edit_comment_id" value="<?php echo htmlentities($comment_id);?>"/>
					<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
					<input type="submit" value= "edit">
				</form>
				<form action="deletecomment.php" method="POST">
					<input type="hidden" name="del_comment_id" value="<?php echo htmlentities($comment_id);?>"/>
					<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
					<input type="submit" value= "delete">
				</form>

				<?php
               
			}
        echo "</div>";}
        // echo "</ul>\n";

        $stmt->close();  
    } 

    if ($loggedin){
     ?>  
     <div class="t">
     <form action="submitcomment.php" method="POST">
		<!--There has to be no whitespace between the textarea tags.
		Any line breaks or whitespace between the <textarea> and </textarea> tags will
		be entered in the box, meaning our default comment entry will have a bunch of line breaks-->
        <h2>Comment</h2> <br><textarea rows="5" name="content" ></textarea><br>
        <input type="hidden" name="story_id" value = "<?php echo $story_id;?>"/> <br>
        <input type="hidden" name="author_id" value = "<?php echo $user_id;?>"/>
        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
        <input type="submit" value= "submit">
    </form>
    </div>
    <?php
}
?>
 <div class= "cmtf">
    <form action="main.php" method="POST">
        <input type="submit" value= "Return to Home">
    </form>
    </div>
</body>
</html>