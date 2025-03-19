<?php
    require 'db_connect.php';

    session_start();

    //Kollar så användare är inloggad
    $INLOGGAD = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

    


?>
