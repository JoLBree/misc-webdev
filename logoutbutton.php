<?php
?>
<div class = "login">
	<?php  printf("Hello %s, you are logged in", $username); ?>
	<form action="logout.php" method="POST">
		<input type="hidden" name="logout" value= true />
		<input type="submit" value="Log out" />
	</form>
   </div> 
 <?php
?>