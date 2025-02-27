<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Nexlify</title>
</head>
<body>
    <header>
        
        
        <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button>
        <img src="img/transparent logo.png" alt="Nexlify" class="Logo">
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