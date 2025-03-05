<?php
session_start();
require 'db_connect.php';



$sql_comments = 'SELECT id, userID, postID, textInput 
                FROM comments
                ORDER BY timeCreated DESC';
$sql_comments = $pdo->prepare($sql_comments);
$sql_comments->execute();
$comments_sql = $sql_comments->fetchAll(PDO::FETCH_ASSOC);





?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>