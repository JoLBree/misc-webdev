<?php
    require 'database.php';
    // check if user has been logged in
	require 'logincheck.php';

//****************************************************    
// log out //
//****************************************************
	session_start();
    if (isset($_POST['logout'])){
        if ($_POST['logout'] == true){
            printf("post set");
             if ($loggedin){

                    // remove token hash
                    $stmt = $mysqli->prepare("UPDATE users SET crypted_token = NULL WHERE user_id = ?");
                    // Bind the parameter
                    $stmt->bind_param('s', $user_id);
                    $stmt->execute();
                    $stmt->close(); 
                    // close session
                    unset($_SESSION['user_id']);
                    unset($_SESSION['token']);
                    session_destroy();
                    printf("Successful log out");
                    // then redirect to main
                            Header("Location:main.php");
                            exit();
                
             }
        }
    }
?>