<?php include 'header.php';?>

<!DOCTYPE html>
    <!-- main.php
    contains:
    titles of stories, that link to a story (post the story ID to the next page?)
    link to signup for a user
    login and pw field and button
    -->
    
<html>
<head>
<title> Our News Site </title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php 
    require 'database.php';
	require 'logincheck.php';
    
	if ($loggedin){
		require 'logoutbutton.php';
	} else{
		require 'loginbutton.php';
	}
	?> 
<div class="search">
	<form action="searchuser.php" method="POST">
		<span>Search user: </span> <input type="text" name="search_username"/>
 		<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
 		<input type="submit" value= "submit">
 	</form>
</div>
	<div class = "t"><h1>THE NEWS SITE</h1></div>

	<div class="tag">
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" value= "All news">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="tag" value= "Tech">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="tag" value= "Sports">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="tag" value= "News">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="tag" value= "Entertainment">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="tag" value= "Others">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="submit" name="fav" value= "My Favs">
	</form>
<br>
<br>
	</div>
	<hr>
<br>
	<?php
	if(isset($_POST['tag'])){
		$tag = $_POST['tag'];
		// View stories with that tag
		$stmt = $mysqli->prepare("select story_Id, title from stories WHERE tag = ?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $tag);
		$stmt->execute();     
		$result = $stmt->get_result();
		if (mysqli_num_rows($result)>0){
			// echo "<ul>\n";
			while($row = $result->fetch_assoc()){
			printf("\t<div class = story><h2>%s</h2>",
				htmlspecialchars( $row["title"] ));
				$story_id = $row["story_Id"];
				?>
				<form action="viewstory.php" method="POST">
					<input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>"/>
					<input type="submit" value= "view">
				</form>
				<?php  
				echo "</div>";    
			}
			// echo "</ul>\n";
		} else{
			printf("No news found. Submit some!<br>");
		}		
		$stmt->close();  
		
	} elseif(isset($_POST['fav'])){
			$stmt = $mysqli->prepare("select favs.story_Id, stories.title
									 FROM favs
									 JOIN stories ON (stories.story_Id = favs.story_Id)
									 WHERE favs.user_Id = ?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('i', $user_id);
			$stmt->execute();     
			$result = $stmt->get_result();
			if (mysqli_num_rows($result)>0){
				// echo "<ul>\n";
				while($row = $result->fetch_assoc()){
				printf("\t<div class = story><h2>%s</h2>",
				htmlspecialchars( $row["title"] ));
				$story_id = $row["story_Id"];
					?>
					<form action="viewstory.php" method="POST">
						<input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>"/>
						<input type="submit" value= "view">
					</form>
					<?php  
					echo "</div>";  
				}
				// echo "</ul>\n";
			} else{
				echo "<div class = t><p>";
				if ($loggedin){
					printf("No news found. Favorite some!<br>");
				} else{
					printf("No news found. Log in and favorite some!<br>");
				}				
				echo "</p></div>";  
			}		
			$stmt->close(); 	
	}else{
		// View all stories
		$stmt = $mysqli->prepare("select story_Id, title from stories");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}     
		$stmt->execute();     
		$result = $stmt->get_result();     
		// echo "<div class=\"storyr\">\n";
		while($row = $result->fetch_assoc()){
			printf("\t<div class = story><h2>%s</h2>",
				htmlspecialchars( $row["title"] ));
				$story_id = $row["story_Id"];
				?>
				<form action="viewstory.php" method="POST">
					<input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>"/>
					<input type="submit" value= "view">
				</form>
				<?php  
				echo "</div>";
   
		}
		// echo "</div>\n";
		$stmt->close();       
	}	
	
	if ($loggedin){  
	?>		
	<div class = "cmtf">
	<h3>Submit news commentary: </h3>
		<br>
		<form action="submitstory.php" method="POST">
			   <span>Title</span> <input type="text" name="title" /> <br>
			   <!--There has to be no whitespace between the textarea tags.
				Any line breaks or whitespace between the <textarea> and </textarea> tags will
				be entered in the box, meaning our default comment entry will have a bunch of line breaks-->
			   <span>Content</span> <textarea rows="5" name="content"></textarea><br>
				<span>Link</span> <input type="text" name="link" /> <br>
				<input type="radio" name="tag"  value="Tech"> Tech
				<input type="radio" name="tag"  value="Sports"> Sports
				<input type="radio" name="tag"  value="News"> News
				<input type="radio" name="tag"  value="Entertainment"> Entertainment
				<input type="radio" name="tag"  value="Others"> others
				<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
				<input type="submit" value= "submit">
		</form>
		</div>
		<?php
	}
	?>
<br>
</body>
</html>
