<?php
    if (!isset($_POST['token'])){
        printf("no token");
        //die("Request forgery detected");
    }
    if($_SESSION['token'] != $_POST['token']){
        printf("%s\n", $_SESSION['token']);
        printf($_POST['token']);
        //die("Request forgery detected");
    }
?>