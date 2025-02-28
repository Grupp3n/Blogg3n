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
    $post_content = trim($_POST['post_content']);
    if (!empty($post_content)) {
        $stmt = $pdo->prepare("INSERT INTO posts (userID, textInput, timeCreated) VALUES (:userID, :textInput, NOW())");
        if ($stmt->execute([
            ':userID'    => $user_id,
            ':textInput' => $post_content
        ])) {
            $message = "Inlägg publicerat!";
        } else {
            $message = "Fel vid publicering av inlägg.";
        }
    } else {
        $message = "Inlägget får inte vara tomt.";
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
$stmt = $pdo->prepare("SELECT textInput, timeCreated FROM posts WHERE userID = :userID ORDER BY timeCreated DESC");
$stmt->execute([':userID' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?php echo htmlspecialchars($user['username']); ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 1rem;
        }
        nav {
            background: #555;
            padding: 1rem;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 1rem;
        }
        .container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            padding: 2rem;
        }
        .main-content {
            max-width: 600px;
            width: 100%;
        }
        .profile-info {
            text-align: center;
            background: #fff;
            padding: 1.5rem;
            border-radius: 4px;
        }
        .profile-info img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .update-profile-form, .create-post-form {
            background: #fff;
            padding: 1.5rem;
            margin-top: 1rem;
            border-radius: 4px;
        }
        .update-profile-form label,
        .create-post-form label {
            font-weight: bold;
        }
        .update-profile-form input,
        .create-post-form textarea {
            width: 100%;
            margin: 0.5rem 0 1rem;
            padding: 0.5rem;
            box-sizing: border-box;
        }
        .update-profile-form button,
        .create-post-form button {
            padding: 0.5rem 1rem;
            cursor: pointer;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        .posts {
            background: #fff;
            margin-top: 1rem;
            padding: 1.5rem;
            border-radius: 4px;
        }
        .posts h3 {
            margin-top: 0;
        }
        .post {
            background: #f9f9f9;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
        }
        .post p {
            margin: 0 0 0.5rem;
        }
        .notifications {
            width: 200px;
            margin-left: 2rem;
        }
        .notifications h4 {
            margin-top: 0;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
            .notifications {
                width: 100%;
                margin-left: 0;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Nexlify</h1>
    </header>
    <nav>
        <a href="#">Start</a>
        <a href="#">Start</a>
        <a href="#">Start</a>
        <a href="#">Start</a>
        <!-- Fler länkar kan läggas till -->
    </nav>
    <div class="container">
        <div class="main-content">
            <div class="profile-info">
                <img src="profile.jpg" alt="Profilbild">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            </div>
            <?php if ($message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            
            <!-- UPPDATERA PROFIL -->
            <form class="update-profile-form" method="post" action="">
                <label for="username">Användarnamn:</label><br>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>"><br>
                <label for="email">E-post:</label><br>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br>
                <button type="submit" name="update_profile">Uppdatera profil</button>
            </form>

            <!-- SKAPA INLÄGG -->
            <form class="create-post-form" method="post" action="">
                <label for="post_content">Nytt inlägg:</label><br>
                <textarea name="post_content" id="post_content" placeholder="Vad vill du dela idag?"></textarea><br>
                <button type="submit" name="create_post">Publicera</button>
            </form>
            
            <!-- LISTA INLÄGG FRÅN DATABAS -->
            <div class="posts">
                <h3>Senaste inlägg</h3>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <p><?php echo nl2br(htmlspecialchars($post['textInput'])); ?></p>
                            <small>Postat: <?php echo htmlspecialchars($post['timeCreated']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Inga inlägg ännu.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="notifications">
            <h4>Notifieringar</h4>
            <p>Nya kommentarer på dina blogginlägg</p>            
        </div>
    </div>
</body>
</html>
