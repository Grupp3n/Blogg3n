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

//Phillips kod POST UPDATE
if (!$post) {
    die("Post not found.");
}

# Den första If-satsen med !isset($_POST['likeButton']) är till för att få bort felmeddelande över header!
if(!isset($_POST['likeButton'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['userID']) {
        $new_header = trim($_POST['header']);
        $new_text = trim($_POST['textInput']);

        if (!empty($new_header) && !empty($new_text)) {
            $update_sql = 'UPDATE Posts SET header = :header, textInput = :textInput WHERE id = :post_id';
            $stmt_update = $pdo->prepare($update_sql);
            $stmt_update->execute([
                'header' => $new_header,
                'textInput' => $new_text,
                'post_id' => $post_id
            ]);
            header("Location: post.php?id=" . $post_id);
            exit();
        } else {
            $error_message = "Both fields must be filled.";
        }
    }
}


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


// Skriver kod om likes
if(isset($_POST['likeButton'])){
    
    $bool = false;
    $likeID = "";
    
    $sql_comments = 'SELECT * 
                     FROM Likes                 
                     WHERE userID = :id';                     
    $stmt_comments = $pdo->prepare($sql_comments);
    $stmt_comments->execute(['id' => $_SESSION['user_id']]);
    $comments2 = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);    
    
    foreach($comments2 as $comment) {
        if($comment['userID'] == $_SESSION['user_id'] && $comment['postID'] == $_GET['id']){
            $bool = true;
            $likeID = $comment['id'];
        }    
    }    

    $id = (int) $post['id']; 

    if($bool) { 
        $query = 'DELETE FROM Likes               
                  WHERE id = :id';                     
        $stmt_comments = $pdo->prepare($query);
        $stmt_comments->execute(['id' => $likeID]);
        $delete = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
        header("location: post.php?id=$id");
    } else {       
        $userID = $_SESSION['user_id'];
        
        $stmt = $pdo->prepare('INSERT INTO Likes (postID, userID) VALUES (:postID, :userID)');
        if ($stmt->execute([
            ':postID' => $_GET['id'],
            ':userID' => $_SESSION['user_id']       
        ]));    
                     
         #Till för att växla färg på LikeButton beroende på om man gillat det eller inte.
         header("location: post.php?id=$id");
    }
    
}

//Hämtar alla Post för att se om man gillat eller inte. För att sätta färg
$sql_comments = 'SELECT * 
                 FROM Likes                 
                 WHERE userID = :id';                     
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute(['id' => $_SESSION['user_id']]);
$comments3 = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);    

$color = false;
foreach($comments3 as $comment) {        
        if($comment['userID'] == $_SESSION['user_id'] && $comment['postID'] == $_GET['id']){
            $color = true;
        }
}


// Här visas Countern "räknaren" på likes
$counter = (int) 0;

$sql_comments = 'SELECT * 
                 FROM Likes                 
                 WHERE postID = :postID';                     
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute(['postID' => $post_id]);
$likes = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

foreach($likes as $like) {
    $counter += 1;
}


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
    <img class="post-image" src="data:image/*;base64,<?php echo $post2['image'] ?>" alt="<?php echo htmlspecialchars($post['header']); ?>" 
    style="max-width: 600px; width: 100%; height: auto; display: block; margin: auto;">
<?php endif; ?>
<p style="color: white;"><?php echo htmlspecialchars($post['textInput']); ?></p>

<hr>

<div>
    <form method="POST" class="commentsAndLike">
    <h2 style="color: white;">Comments</h2>  
    
        <div class="commentsAndLike">

            <p style="color: white;"><?php echo "Likes: $counter" ?></p>

            <button type="submit" name="likeButton" style="background-color: transparent;">
            
            
    
                <?php if($color): ?>
                    <img src="./img/thumbs-up-24.png" alt="" style="width:130%; background-color: green;">     
                <?php else: ?>
                    <img src="./img/thumbs-up-24.png" alt="" style="width:130%; background-color: white;">
                <?php endif ?>
            
            </button>

        </div>
    </form>
</div>
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

<hr>

<!-- Post Update form -->
<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['userID']): ?>
    <button id="toggleEditForm">Edit Post</button>

    <div id="editForm" style="display: none;">
        <h2 style="color: white;">Edit Post</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="post.php?id=<?php echo $post_id; ?>" method="post">
            <label style="color: white;">Header:</label><br>
            <input type="text" name="header" value="<?php echo htmlspecialchars($post['header']); ?>" required><br>
    
            <label style="color: white;">Text:</label><br>
            <textarea name="textInput" required><?php echo htmlspecialchars($post['textInput']); ?></textarea><br>
    
            <button type="submit">Update Post</button>
        </form>
    </div>

    <script>
        document.getElementById("toggleEditForm").addEventListener("click", function () {
            var form = document.getElementById("editForm");
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        })
    </script>
<?php endif; ?>
</main>
</body>
</html>