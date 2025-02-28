<?php
require 'db_connect.php';

session_start();

//Kommentera "true" om Post knappen ska visas, false om den ska döljas/samma för om det ska stå login eller profile
$_SESSION['INLOGGAD'] = false; 

// Fetcha från posts för main content (Stora bilden)
$sql_main = 'SELECT id, userID, textInput 
             FROM Posts
             ORDER BY timeCreated DESC LIMIT 1';

$stmt_main = $pdo->prepare($sql_main);
$stmt_main->execute();
$main_post = $stmt_main->fetch(PDO::FETCH_ASSOC);

// Fetcha från posts de senaste 4 inläggen för Recent Headlines
$sql_thumbnails = 'SELECT id, textInput
                   FROM Posts
                   ORDER BY timeCreated DESC LIMIT 4';
$stmt_thumbnails = $pdo->prepare($sql_thumbnails);
$stmt_thumbnails->execute();
$thumbnail_posts = $stmt_thumbnails->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Nexlify</title>
</head>
<body>
<div class="sticky-ad">
    <a href="ad-page.php" class="ad-link">
        <img src="ad.gif" alt="Sticky Ad" class="ad-image">
    </a>
</div>
    <header>
        <?php if (isset($_SESSION['INLOGGAD']) && $_SESSION['INLOGGAD'] === true) : ?>
            <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button>
        <?php else: ?>
                <div></div>
        <?php endif; ?>
        
        <!-- Log in "button" if SESSION inloggad är false -->
        <img src="img/transparent logo.png" alt="Nexlify" class="Logo">
        <?php if (isset($_SESSION['INLOGGAD']) && $_SESSION['INLOGGAD'] === false) : ?>
            <a href="#adPopup" class="Loginknapp">Log in</a>
        <?php else : ?>
        <?php endif; ?>

        <!-- Profie button if SESSION inloggad är true -->
        <?php if (isset($_SESSION['INLOGGAD']) && $_SESSION['INLOGGAD'] === true) : ?>
            <button class="ProfileKnapp" onclick="window.location.href='profile.php'">Profile</button>
        <?php else : ?>
        <?php endif; ?>
    </header>

    <main class="index-main">
    <div class="main-content">
        <h2>Main Headline</h2>
        <div class="post-preview">
            <p>No posts posted</p>
        </div>
    </div>

    <aside class="index-aside">
        <h2>Recent Headlines</h2>
        <div class="post-thumbnails">
            <div class="thumbnail">
                <p>No posts posted</p>
            </div>
        </div>
    </aside>
</main>
<div id="adPopup" class="popup">
    <div class="popup-content">
    <a href="ad-page.php" class="ad-link">
        <img src="ad.gif" alt="Sticky Ad" class="popup-ad-image">
    </a>
        <a href="login.php" class="popup-login-link">Proceed to Login</a>
        <a href="#" class="popup-close-link">Close</a> 
    </div>
</div>
</body>
</html>
