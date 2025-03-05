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
    
    // Hämtar alla users för att veta vilket ID man skall skicka meddelande till
    $stmt = $pdo->prepare("SELECT * FROM users WHERE firstname = :firstname");
    $stmt->execute([':firstname' => $_POST['receiver']]);
    $userName = $stmt->fetch(PDO::FETCH_ASSOC);

    $receiver_id = $userName['id'];      //denna raden skall bytas mot användarnamn och inte ID
    
    $post_content = trim($_POST['post_content']);
    if (!empty($post_content)) {
        $stmt = $pdo->prepare("INSERT INTO chatt (text, senderID, receiverID, timeCreated) VALUES (:text, :senderID, :receiverID, NOW())");
        if ($stmt->execute([
            ':senderID'    => $user_id,
            ':receiverID'    => $receiver_id,
            ':text' => $post_content
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
$stmt = $pdo->prepare("SELECT text, senderID, receiverID, text, timeCreated
                                FROM chatt 
                                WHERE senderID = :senderID OR receiverID = :receiverID
                                ORDER BY timeCreated DESC");

$stmt->execute([
    ':senderID' => $user_id,
    ':receiverID' => $user_id
]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);



$userchatt = [];

// Här fixar jag så jag har alla userIDs som har chattat och tagit bort den usern som är inloggad.
foreach($posts as $post) {

    $sender = $post['senderID'];

    if($user_id != $sender) {
        if(!in_array($sender, $userchatt)) {
            $userchatt[] = $sender;
        }
    }
}

print_r($userchatt);

foreach($posts as $post) {

}

// Bygga en till foreach-loop där man går igenom varje användare och sparar texten?

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
            background-color: grey;
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
            background: darkgray;
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
            background: darkgray;
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
            background: darkgray;
            margin-top: 1rem;
            padding: 1.5rem;
            border-radius: 4px;
        }
        .posts h3 {
            margin-top: 0;
        }
        .post {
            /* display: none; */
            background: lightgray;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
        }        
        .post p {
            margin: 0 0 0.5rem;
        }

        /* Tar bort Pilen i details */
       .no_arrow {
        list-style: none;
       }       
       .no_arrow_hidden {
        list-style: none;
        display: none;
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
                <input type="text" name="receiver" placeholder="Vem vill du skicka till?">
                <label for="post_content">Ny chatt:</label><br>
                <textarea name="post_content" id="post_content" placeholder="Vad vill du dela idag?"></textarea><br>
                <button type="submit" name="create_post">Publicera</button>
            </form>
            
            <!-- LISTA INLÄGG FRÅN DATABAS -->
            <div class="posts">
                <h3>Senaste Chatt historiken</h3>

               
                <?php if (!empty($posts)): ?>
                    
                    <?php foreach ($userchatt as $index => $userID): ?>

                     <!-- Lägga en array i denna forloopen som sparar inloggade användaren och sedan kollar igenom chatt historiken med den den har chattat med-->
                        <?php foreach ($posts as $post): ?>

                            <?php if($userID == $post['senderID']): ?>
                                <!-- DENNA CONTAINERN SKALL LÄGGAS I EN TILL CONTAINER 
                                Så man specar upp det på användare och trycker man på den användaren så kommer bara den chatthistoriken upp -->

                                <div>
                                    <details>
                                    
                                        <?php $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :id");
                                        $stmt->execute([':id' => $post['senderID']]);
                                        $user2 = $stmt->fetch(PDO::FETCH_ASSOC); ?>
                
                                       
                                            <summary class="no_arrow"><h3 style="color: Green;" tabindex="0" class="master"><?php echo nl2br(htmlspecialchars($user2['username'])); ?></h3></summary>  
                                       
                                        <div class="post">

                                            <?php if($post['senderID'] != $user_id):                               

                                                    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :id");
                                                    $stmt->execute([':id' => $post['senderID']]);
                                                    $user2 = $stmt->fetch(PDO::FETCH_ASSOC); ?>

                                            <h3 style="color: red;"><?php echo nl2br(htmlspecialchars($user2['username'])); ?></h3>
                                            <p style="color: red;"><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>
                                            <small style="color: red;">Postat: <?php echo htmlspecialchars($post['timeCreated']); ?></small>

                                                <?php else: ?>
                                                    

                                                        <h3><?php echo nl2br(htmlspecialchars($chatt['username'])); ?></h3>
                                                        <p><?php echo nl2br(htmlspecialchars($chatt['text'])); ?></p>
                                                        <small>Postat: <?php echo htmlspecialchars($chatt['timeCreated']); ?></small>

                                                    
                                                <?php endif ?>

                                        </div>

                                    </details>

                                </div>

                            <?php endif ?>

                        <?php endforeach; ?>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>Ingen chatthistorik ännu</p>

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
