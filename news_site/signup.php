<!DOCTYPE html>
<html>
<head>
<title> Signup </title>
</head>
<body>

Create an account

<br/>
<br/>

<!-- Send username to self to run script below -->
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
	<label>Enter a user Name: <input type="text" name="newuser" /></label>
	<br/>
	<label>Enter a password: <input type="password" name="newpw" /></label>
	<input type="hidden" name="token" value=substr(md5(rand()), 0, 10)/> <!-- generate random token in case user creation successful-->
	<input type="submit" value="Create account" />
</form>

<!-- Script to create user -->
<?php
    require 'database.php';
	require 'logincheck.php';
	if ($loggedin){
		Header("Location:main.php");
        exit;
	}
    // check if create user button has been pressed
    // check that username is valid
    if (isset($_POST['newuser']) && isset($_POST['newpw'])){
        $username = $_POST['newuser'];
		$pw = $_POST['newpw'];
        if( !preg_match('/^[\w_\.\-]+$/', $username) ){
            echo "Invalid username";
	        exit;
        } else if(!preg_match('/^[\w_\.\-]+$/', $pw)){
			echo "Invalid password";
	        exit;
		} else{
			// check if username already taken
			$stmt = $mysqli->prepare("SELECT COUNT(user_id) FROM users WHERE username=?");
			
			if(!$stmt){
				printf("Check Username Taken Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->bind_result($numDuplicatedUsers);
			$stmt->fetch();
			if ($numDuplicatedUsers != 0){
				echo "Username already taken";
				exit;
			}
			$stmt->close();
		}        
		
		// generate token, hash created password and token		
		$token = substr(md5(rand()), 0, 10);
		$pwd_hash = crypt($pw);
		$token_hash = crypt($token);
		
		// add created username, password hash and session token hash to database table
		$stmt = $mysqli->prepare("insert into users (username, crypted_password, crypted_token) values (?, ?, ?)");
		//$stmt = $mysqli->prepare("insert into employees (first_name, last_name, department) values (?, ?, ?)");
		if(!$stmt){
			printf("Insert User Query Prep Failed: %s\n", $mysqli->error);
			//printf("username: %s\n pwhash: %s\n tokenHash: %s\n", $username, $pwd_hash, $token_hash);
		    exit;
		}
		$stmt->bind_param('sss', $username, $pwd_hash, $token_hash);
		$stmt->execute();
		
		// retrieve generated (auto_incremented) user_id
		$stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM users WHERE username=?");
		if(!$stmt){
			printf("Get User ID Query Prep Failed: %s\n", $mysqli->error);
		    exit;
		}
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($cnt, $user_id);
		$stmt->fetch();
		$stmt->close();

		// start session with token
		session_start();
		$_SESSION['token']=$token;
		$_SESSION['user_id']=$user_id;
		Header("Location:main.php");
		exit;
    }
?>
</br></br>
Already have an account?
</br>
<form action="login.php" method="POST">
	<label>Userame: <input type="text" name="username" /></label>
	<br/>
	<label>Password: <input type="password" name="password" /></label>
	<input type="submit" value="Log in" />
</form>

<form action="main.php" method="POST">
	<input type="submit" value= "Return to Home">
</form>

</body>
</html>
