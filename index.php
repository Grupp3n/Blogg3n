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
    <div class="sticky-ad">
        <img src="ad.gif" alt="Sticky Ad" class="ad-image">
    </div>
        
        <button onclick="window.location.href='create_post.php'">Gör ett inlägg</button>
        <img src="img/transparent logo.png" alt="Nexlify" class="Logo">
        <button onclick="window.location.href='login.php'">Log In</button>
    </header>

    <main>
        <div class="main-content">
            <?php
                require '';
                $sql = 'SELECT id, title, content, image_path FROM posts
                        ORDER BY created_at DESC LIMIT 1';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $post = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($post) {
                    echo '<div class="post-preview" onclick="window.location.href=\'post.php?id=' . $post['id'] . '\'">';
                    if ($post['image_path']) {
                        echo '<img src="' . htmlspecialchars($post['image_path']) . '" alt="' . htmlspecialchars($post['title']) . '">';
                    }
                    echo '</div>';
                    echo '<h1>' . htmlspecialchars($post['title']) . '</h1>';
                    echo '<p>' . htmlspecialchars(substr($post['content'], 0, 200)) . '...</p>';
                } else {
                    echo '<div class="post-preview"';
                    echo '<p>No posts posted</p>';
                    echo '</div>';
                }
            ?>
        </div>
    </main>
</body>
</html>