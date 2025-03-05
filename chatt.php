<?php
session_start();

if(!$_SESSION['INLOGGAD']) {
    header("location: index.php");
    exit;
} else {




}
?>