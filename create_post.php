<?php
Session_start();


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
        <img src="img/transparent logo.png" alt="Nexlify" class="Logo">
        <button onclick="window.location.href='login.php'">Profile</button>
    </header>

    <main class="main_create_post">

        <form method="POST" class="form_create_post">

            <div class="div_create_post">
                <input type="text" name="blogHeader" placeholder="HEADLINE" class="headline_create_post">
            </div>

            <textarea name="blogText" class="text_create_post" rows="25" placeholder="Input blog shit here"></textarea>

        </form>

        <div class="div_create_post">
            <img src="ad.gif" alt="Sticky Ad" class="ad-image" style="width: 500px; height: 125px; margin-top: 3rem; text-align: center;">
        </div>

    </main>
</body>
</html>