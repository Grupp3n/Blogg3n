<?php
require 'db_connect.php';

session_start();

//Kollar så användare är inloggad
$INLOGGAD = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Fetcha från posts för main content (Stora bilden)
$sql_main = 'SELECT id, userID, textInput, header 
             FROM Posts
             ORDER BY timeCreated DESC LIMIT 1';

$stmt_main = $pdo->prepare($sql_main);
$stmt_main->execute();
$main_post = $stmt_main->fetch(PDO::FETCH_ASSOC);

// Fetcha från posts de senaste 4 inläggen för Recent Headlines
$sql_thumbnails = 'SELECT id, textInput, header
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
<!-- <div class="sticky-ad">
    <a href="ad-page.php" class="ad-link">
        <img src="ad.gif" alt="Sticky Ad" class="ad-image">
    </a>
</div> -->
<header>
    <!-- Visar create post om man är inloggad -->
    <?php if ($INLOGGAD) : ?>
        <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button>
    <?php endif; ?>

    <img src="img/transparent logo.png" alt="Nexlify" class="Logo">

    <!-- visar Login knapp om man inte är inloggad -->
    <?php if (!$INLOGGAD) : ?>
        <a href="login.php" class="Loginknapp">Log in</a>
    <?php else : ?>
        <!-- visar profile knapp om man är inloggad -->
        <button class="ProfileKnapp" onclick="window.location.href='profile.php'">Profile</button>
    <?php endif; ?>
</header>

    <main class="index-main">
        <div class="main-content">
            <?php if ($main_post): ?>
                <h2>Main Headline</h2>
                <div class="post-preview">
                    <h3><?= htmlspecialchars($main_post['header']); ?></h3>
                    <p><?= htmlspecialchars($main_post['textInput']); ?></p>
                </div>
                <p>Posted by User ID: <?= htmlspecialchars($main_post['userID']);?></p>
            <?php else: ?>
                <h2>Main Headline</h2>
                <div class="post-preview">
                    <p>No posts posted</p>
                </div>
            <?php endif; ?>
    </div>

    <aside class="index-aside">
        <h2>Recent Headlines</h2>
        <div class="post-thumbnails">
            <?php if ($thumbnail_posts): ?>
                <?php foreach ($thumbnail_posts as $post): ?>
                    <div class="thumbnail">
                        <h4><?= htmlspecialchars($post['header']); ?></h4>
                        <p><?= htmlspecialchars($post['textInput']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="thumbnail">
                    <p>No posts posted</p>
                </div>
            <?php endif; ?>
        </div>
    </aside>
</main>

<div class="div_create_post">
            <img src="./img/Swish-codes.gif" style="width: 150px; margin-right: 1rem;">
            <a href="ad-page.php">
            <img src="ad.gif" alt="Sticky Ad" class="ad-image" style="width: 500px; height: 125px; margin-top: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
            </a>
            <img src="./img/Swish-codes.gif" style="width: 150px; margin-left: 1rem;">
        </div>

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
