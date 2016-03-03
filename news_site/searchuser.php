<!DOCTYPE html>
<html>
<head>
<title>News Site</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
    require 'database.php';
    require 'logincheck.php';
	include 'header.php';
    if ($loggedin){
        require'logoutbutton.php';
    } else{
        require 'loginbutton.php';
    }
    ?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<span>Search user: </span> <input type="text" name="search_username"/>
		<input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
		<input type="submit" value= "search">
	</form>
	<form action="main.php" method="POST">
		<input type="submit" value= "Return to Home">
	</form>
		
	<?php
    if (isset($_POST['search_username'])){
      $username = test_input($_POST['search_username']);
        // check if user exists		
        $stmt = $mysqli->prepare("SELECT COUNT(user_id) FROM users WHERE username=?");
        if(!$stmt){
            printf("Check Username Taken Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($numUsers);
        $stmt->fetch();
        $stmt->close();
        if ($numUsers != 1){
            echo "User does not exist";
            exit;
        } else{              
            // check is user has stories
            $stmt = $mysqli->prepare("SELECT
									COUNT(stories.story_Id),
									users.user_id
									FROM stories
									JOIN users on (users.user_id = stories.user_Id)
									WHERE users.username=?");
                
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($numStories, $user_id);
            $stmt->fetch();
            $stmt->close();

            if ($numStories == 0){
                printf("%s has not posted any news commentary", $username);
            } else{
				printf("News commentary by %s:", $username);
                $stmt = $mysqli->prepare("select title, story_Id from stories
                                         WHERE user_id = ?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s', $user_id);
                $stmt->execute();     
                $result = $stmt->get_result();
				$stmt->close();
                echo "<ul>\n";
                while($row = $result->fetch_assoc()){
                    printf("\t<li>%s</li>",
                    htmlspecialchars( $row["title"] ));
					?>
					<form action="viewstory.php" method="POST">
						<input type="hidden" name="story_id" value="<?php echo htmlentities($row["story_Id"]);?>"/>
						<input type="submit" value= "view">
					</form>
					<?php     
                }
				echo "</ul>\n";
            }
            
			
			// check is user has comments
            $stmt = $mysqli->prepare("SELECT
									COUNT(comments.comment_Id)
									FROM comments
									JOIN users on (users.user_id = comments.user_id)
									WHERE users.username=?");
                
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($numComments);
            $stmt->fetch();
            $stmt->close();
			
            if ($numComments == 0){
                printf("%s has not posted any comments", $username);
            } else{
				printf("Comments by %s:", $username);
                $stmt = $mysqli->prepare("SELECT
                                            stories.title,
											stories.story_Id,
                                            comments.comment
                                        FROM users
                                        JOIN comments on (comments.user_id = users.user_id)
                                        JOIN stories on (stories.story_Id=comments.story_Id)
                                        WHERE users.user_id = ?");
                if(!$stmt){
                    printf("getting comments Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
				$stmt->close();
                echo "<ul>\n";
                while($row = $result->fetch_assoc()){
                    printf("\t<li>Title: %s</br>Comment: %s</li>",
                    htmlspecialchars( $row["title"]), htmlspecialchars( $row["comment"]));
                    ?>
					<form action="viewstory.php" method="POST">
						<input type="hidden" name="story_id" value="<?php echo htmlentities($row["story_Id"]);?>"/>
						<input type="submit" value= "view">
					</form>
					<?php     
                }
				echo "</ul>\n";
            }
        }
    } else{
        Header("Location:main.php");
		exit;
    } 
?>
</body>
</html>