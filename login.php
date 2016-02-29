<?php
    // just a script for logging in

    require 'database.php';
    include 'header.php';
    // Use a prepared statement
    $stmt = $mysqli->prepare("SELECT COUNT(*), user_id, crypted_password FROM users WHERE username=?");
     
    // Bind the parameter
    $stmt->bind_param('s', $user);
    $user = test_input($_POST['username']);
    $stmt->execute();
     
    // Bind the results
    $stmt->bind_result($cnt, $user_id, $pwd_hash);
    $stmt->fetch();
    $stmt->close();
    
    $pwd_guess = test_input($_POST['password']);
    // Compare the submitted password to the actual password hash
    if( $cnt == 1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash){
        // Login succeeded!
        session_start();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['token'] = substr(md5(rand()), 0, 10);
        $token_hash = crypt($_SESSION['token']);
        
        // insert hashed token       
        $stmt = $mysqli->prepare("UPDATE users SET crypted_token=? WHERE user_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
		    exit;
		}
        $stmt->bind_param('ss', $token_hash, $user_id);
		$stmt->execute();
        if (isset($_SESSION['token']) && isset($_SESSION['user_id'])){
            printf("I'm logged in and session vars set\n");}
            printf("Session status: ".session_status());
        Header("Location:main.php");
        exit();
        ?>
        
		<?php
    }else{
        // Login failed; redirect back to main page
        Header("Location:main.php");
        exit();
    }
?>
