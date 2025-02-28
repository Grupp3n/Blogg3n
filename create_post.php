<?php
    Session_start();
    require 'db_connect.php';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $blogHeader = $_POST['blogHeader'];
        $blogText = $_POST['blogText'];

        if(isset($_POST['post_submit_button'])) {

            $stmt = $pdo->prepare("INSERT INTO posts (textInput, header, userID, timeCreated) 
                                            VALUES (:textInput, :header)");

            $stmt->bindParam(':textInput', $blogText);
            $stmt->bindParam(':header', $blogHeader);

            // Här kollas hela tabellen Users för att kontrollera jämförelse med användarnamn emot inloggade användarnamnet genom Session
            foreach ($users as $user) {

                if($user['username'] == $_SESSION['username']) {
                    $stmt->bindParam(':userID', $user['id']);   
                }
            }

            $time = date_create();
            $getTime = date_format($time, "Y-m-d H:i:s");

            $stmt->bindParam(':timeCreated', $getTime);

            if ($stmt->execute()) {
                echo "<div class='success'>Registrering lyckades!</div>";
            } 
            else 
            {
                echo "<div class='error'>Något gick fel!</div>";
            }
        }
    }
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body class="body_create_post">
    <header>    
        <!-- <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button> -->
         <div></div>
        <img src="img/transparent logo.png" alt="Nexlify" class="Logo">
        <button onclick="window.location.href='login.php'">Profile</button>
    </header>

    <main class="main_create_post">

        <form method="POST" class="form_create_post">

            <div class="div_create_post">
                <input type="text" name="blogHeader" placeholder="HEADLINE" class="headline_create_post" require>
            </div>

            <textarea name="blogText" class="text_create_post" rows="25" placeholder="Input blog shit here" require></textarea>
            
            <div class="div_create_post">
                <button name="post_submit_button" class="post_submit_button">Post</button>
            </div>
        </form>

        <div class="div_create_post">
            <img src="ad.gif" alt="Sticky Ad" class="ad-image" style="width: 500px; height: 125px; margin-top: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
        </div>

    </main>

    <div class="footer">        
        <p>&copy; Alla rättigheter förbehållna. Grupp 3 </p>
    </div>
</body>
</html>