<?php
    if ($loggedin){
        $stmt = $mysqli->prepare("SELECT COUNT(story_Id) FROM favs WHERE (story_Id = ? AND user_id = ?)");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('ii', $story_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($numFavs);
        $stmt->fetch();
        $stmt->close();
        if ($numFavs == 1){
            ?>
			<form action="favdo.php" method="POST">
				<input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>"/>
                <input type="hidden" name="user_id" value="<?php echo htmlentities($user_id);?>"/>
                <input type="hidden" name="do" value="un_fav"/>
                <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
				<input type="submit" value= "un-fav">
			</form>
			<?php    
        } else{
            ?>
			<form action="favdo.php" method="POST">
				<input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>"/>
                <input type="hidden" name="user_id" value="<?php echo htmlentities($user_id);?>"/>
                <input type="hidden" name="do" value="fav"/>
                <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>"/>
				<input type="submit" value= "fav">
			</form>
			<?php 
        }
    }
?>