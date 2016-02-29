<?php
?> 
<div class = "login">
	<form action="login.php" method="POST">
		<label>Userame: <input type="text" name="username" /></label>
		<label>Password: <input type="password" name="password" /></label>
		<input type="submit" value="Log in" />
	</form>
	
	<form action="signup.php" method="POST">
 		<input type="submit" value= "Sign Up">
 	</form>
</div>
    <?php
?>