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
        <img src="ad.gif" alt="Sticky Ad" class="ad-image">
    </div>

    <header class="placeholder_header">
        <img src="logo.jpeg" alt="Blog Logo" class="logo">
        <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button>
        <input type="text" id="title" placeholder="TITLE" class="title-input">
        <button onclick="window.location.href='login.php'">Log In</button>
    </header>    

    <main>
        <div class="main-content">
            <?php
                require '';
                $sql = 'SELECT id, title, content FROM posts';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $post = $stmt->fetch(PDO::FETCH_ASSOC);

            ?>
        </div>
    </main>
</body>
</html>