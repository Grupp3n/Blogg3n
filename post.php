<?php
session_start();
require 'db_connect.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("The requested post doesnt exist.");
}

$post_id = $_GET['id'];

//hämtar vald post genom postID
$sql_post = 'SELECT p.id, p.userID, p.textInput, p.header, p.imagePath, u.username
             FROM Posts p
             LEFT JOIN Users u ON p.userID = u.id
             WHERE p.id = :post_id';
$stmt_post = $pdo->prepare($sql_post);
$stmt_post->execute(['post_id' => $post_id]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);


//hämtar vald 'BILD' genom postID
$pictureID = $post['imagePath'];

$sql_post = 'SELECT p.id, p.userID, p.textInput, p.header, p.image, u.username
             FROM Posts p
             LEFT JOIN Users u ON p.userID = u.id
             WHERE p.id = :post_id';
$stmt_post = $pdo->prepare($sql_post);
$stmt_post->execute(['post_id' => $pictureID]);
$post2 = $stmt_post->fetch(PDO::FETCH_ASSOC);

//Hämtar kommentarerna för valt inlägg
$sql_comments = 'SELECT c.textInput, c.timeCreated, u.username 
                 FROM Comments c
                 LEFT JOIN Users u ON c.userID = u.id
                 WHERE c.postID = :post_id
                 ORDER BY c.timeCreated DESC';
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute(['post_id' => $post_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kommentar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <a href="index.php">index</a>
    </header>

    <main>
    <h1 style="color: white;"><?php echo htmlspecialchars($post['header']); ?></h1>
<p style="color: white;">Posted by: <?php echo htmlspecialchars($post['username']); ?></p>
<?php if ($post['imagePath']): ?>
    
    <img src="data:image/*;base64, <?php echo $post2['image'] ?>" alt="<?php echo htmlspecialchars($post['header']); ?>" style="max-width: 100%; height: auto;">
<?php endif; ?>
<p style="color: white;"><?php echo htmlspecialchars($post['textInput']); ?></p>

<hr>

<h2 style="color: white;">Comments</h2>
<?php if ($comments): ?>
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p style="color: white;"><strong><?php echo htmlspecialchars($comment['username']); ?></strong>:</p>
            <p style="color: white;"><?php echo nl2br(htmlspecialchars($comment['textInput'])); ?></p>
            <p style="color: white;"><small><?php echo $comment['timeCreated']; ?></small></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="color: white;">No comments yet. Be the first to comment!</p>
<?php endif; ?>

<hr>

<!-- Kommentar formen för submit -->
<?php if (isset($_SESSION['user_id'])): ?>
    <form action="post_comment.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <textarea name="comment" placeholder="Write a comment!" required></textarea>
        <button type="submit">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> to comment.</p>
<?php endif; ?>
    </main>
</body>
</html>