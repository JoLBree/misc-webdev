<?php
	$loggedin = false;
	session_start();
    if (isset($_SESSION['token']) && isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
        $token_guess = $_SESSION['token'];
        $stmt = $mysqli->prepare("SELECT COUNT(*), crypted_token FROM users WHERE user_id=?");
        // Bind the parameter
        $stmt->bind_param('s', $user_id);
        $stmt->execute();        
        // Bind the results
        $stmt->bind_result($cnt, $correct_token);
        $stmt->fetch();
        $stmt->close(); 
        // Compare the submitted token to the actual token hash
        if( $cnt == 1 && crypt($token_guess, $correct_token)==$correct_token){
			$stmt = $mysqli->prepare("SELECT
										username
									FROM users
									WHERE
										user_id = ?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('i', $user_id);
			$stmt->execute();         
			$stmt->bind_result($username);
			$stmt->fetch();
			$stmt->close(); 
            // printf("Hello %s, you are logged in", $username);
			$loggedin = true;
            
        } else{
			// someone not logged in who somehow has a session. close session and redirect to main!
            unset($_SESSION['user_id']);
            unset($_SESSION['token']);
            session_destroy();
			Header("Location:main.php");
			exit();
		}
    }
	// normal user
    
    
    
    
?>