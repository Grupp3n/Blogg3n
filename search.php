<?php
session_start();

require 'db_connect.php';

if (!isset($_GET['sökning']) || empty(trim($_GET['sökning']))) {
    die("no search input.");
}

$search_query = trim($_GET['sökning']);
$search_query = "%{$search_query}%";

$sql = "SELECT id, header, textInput FROM Posts WHERE header LIKE :query OR textInput LIKE :query";
$stmt = $pdo->prepare($sql);
$stmt->execute(['query' => $search_query]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sök</title>
</head>
<body>
<header>
    
    <div class="header-button left-button">
        <a href="create_post.php" class="btn">Gör ett inlägg</a>
    </div>
    
    <div class="logo-con">
        <a href="index.php"><img src="img/transparent logo.png" alt="Nexlify"></a>
    </div>
    
    <div class="dropdown right-dropdown">
        <button class="dropbtn">Meny</button>
        <div class="dropdown-content">            
            <a href="profile.php">Profile</a>
            <a href="follow.php">Followers</a>
            <a href="logout.php">Logga ut</a>              
        </div>        
    </div>
</header>


    
    <main class="search-main">
    <div class="search-container">
    <form action="search.php" method="GET" class="search-form">
    <input type="text" name="sökning" placeholder="Search for posts..." required>
    <button type="submit">Search</button>
    </div>
</form>
        <h1>Related Posts</h1>
        
        <?php if ($posts): ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <h3><a class="post-länk" class href="post.php?id=<?php echo $post['id']; ?>">
                            <?php echo htmlspecialchars($post['header']); ?>
                        </a></h3>
                        <p><?php echo htmlspecialchars(substr($post['textInput'], 0, 100)); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No posts found for "<?php echo htmlspecialchars($_GET['sökning']); ?>".</p>
        <?php endif; ?>
    </main>
</body>
</html>