<?php
session_start();

// Test-användare:
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);

    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
    if ($stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':id'       => $user_id
    ])) {
        $message = "Profil uppdaterad!";
    } else {
        $message = "Fel vid uppdatering av profil.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $post_header  = trim($_POST['post_header']);
    $post_content = trim($_POST['post_content']);
    
    if (!empty($post_header) && !empty($post_content)) {
        $stmt = $pdo->prepare("INSERT INTO posts (userID, header, textInput, timeCreated) VALUES (:userID, :header, :textInput, NOW())");
        if ($stmt->execute([
            ':userID'    => $user_id,
            ':header'    => $post_header,
            ':textInput' => $post_content
        ])) {
            $message = "Inlägg publicerat!";
        } else {
            $message = "Fel vid publicering av inlägg.";
        }
    } else {
        $message = "Både rubrik och innehåll måste fyllas i.";
    }
}

// Hämta aktuell användardata från databasen
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php");
    exit;
}

// Hämta inlägg från DB för den inloggade användaren
$stmt = $pdo->prepare("SELECT header, textInput, timeCreated FROM posts WHERE userID = :userID ORDER BY timeCreated DESC");
$stmt->execute([':userID' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <!-- "Gör ett inlägg" knappen längst till vänster -->
    <div class="header-button left-button">
        <a href="create_post.php" class="btn">Gör ett inlägg</a>
    </div>
    <!-- Logotypen centrerad -->
    <div class="logo-con">
        <a href="index.php"><img src="img/transparent logo.png" alt="Nexlify"></a>
    </div>
    <!-- Dropdown-menyn "Meny" längst till höger -->
    <div class="dropdown right-dropdown">
        <button class="dropbtn">Meny</button>
        <div class="dropdown-content">            
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logga ut</a>            
        </div>        
    </div>
</header>

<main>
    <!-- Profilsektionen -->
    <div class="profile-info">
        <div class="profile-info-box">
            <img src="img/transparent logo.png" alt="Profilbild">
        </div>
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    </div>
    
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <!-- Uppdatera profil -->
    <form class="update-profile-form" method="post" action="">
        <div class="form-group">
            <label for="username">Användarnamn:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        <div class="form-group">
            <label for="email">E-post:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <button type="submit" name="update_profile">Uppdatera profil</button>
    </form>

    <!-- Skapa inlägg -->
    <form class="create-post-form" method="post" action="">
        <div class="form-group">
            <label for="post_header">Skapa ett inlägg:</label>
            <input type="text" name="post_header" id="post_header" placeholder="Ange rubrik">
        </div>
        <div class="form-group">
            
            <textarea name="post_content" id="post_content" placeholder="Vad vill du dela?"></textarea>
        </div>
        <button type="submit" name="create_post">Publicera</button>
    </form>
    
    <!-- Inlägg och notifieringar sida vid sida -->
    <div class="content-columns">
        <div class="posts">
            <h3>Senaste inlägg</h3>
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <h4><?php echo nl2br(htmlspecialchars($post['header'])); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($post['textInput'])); ?></p>
                        <small>Postat: <?php echo htmlspecialchars($post['timeCreated']); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Inga inlägg ännu.</p>
            <?php endif; ?>
        </div>
        <div class="notifications">
            <h3>Nya kommentarer</h3>
        </div>
    </div>
</main>
</body>
</html>
