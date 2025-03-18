<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db_connect.php';

$user_id = $_SESSION['GuestID'];
$message = '';


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



 # Här kommer logiken ifrån Follow

    $query = '  SELECT * 
                FROM Follower                 
                WHERE followedID = :id
            ';  
    $stmt = $pdo->prepare($query);
    
    $stmt->execute(['id' => $_SESSION['user_id']]);  # Skall ändra till USERID
    $follower = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $counterFollower = 0;
    $counterFollowed = 0;

    # Denna skall räkna hur många det är som följer användaren
    foreach($follower as $follow) { 

        if($follow['followerID']) {
            $counterFollowed += 1;
        }
        
    }

    $query = '  SELECT * 
                FROM Follower                 
                WHERE followerID = :id
            ';  
    $stmt = $pdo->prepare($query);
    
    $stmt->execute(['id' => $_SESSION['user_id']]);  # Skall ändra till USERID
    $follower2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # Denna skall räkna hur många användaren följer
    foreach($follower2 as $follow) {
        
        if($follow['followedID']) {
            $counterFollower += 1;
        }
    }

    $query = '  SELECT * 
                FROM Follower    
            ';  
    $stmt = $pdo->prepare($query);
    
    $stmt->execute(); 
    $followAll = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $bool = true;  
    $deleteID = 0;
    
    $visitProfile = $_SESSION['GuestID'];
    

    if($_SERVER['REQUEST_METHOD'] == "POST") {

        if(isset($_POST['follow-button'])) {
            $query2 = "INSERT INTO Follower (followedID, followerID) VALUES (:followedID, :followerID)";                
            $stmt2 = $pdo->prepare($query2);               

            $stmt2->execute([
                'followerID' => $_SESSION['user_id'], # Här skall man implentera USERID
                'followedID' => $_SESSION['GuestID']  # Här lägger man till den användaren man är inne på
            ]);   
            header("location: guest_profile.php");  # skall ändras så man resetar den sidan man är på (profilen)             
        }

        if(isset($_POST['unfollow-button'])) {
            
            $query = "DELETE FROM Follower WHERE id = :id";                
            $stmt = $pdo->prepare($query);     
            
            $stmt->execute([
                'id' => $_SESSION['deleteid'], # Här skall man implentera USERID                    
            ]);    
            $delete = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            header("location: guest_profile.php");  # skall ändras så man resetar den sidan man är på (profilen)
        }
        
    }


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
        <h2 style="color: white;"><?php echo htmlspecialchars($user['username']); ?></h2>
    </div>
    
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <!-- Visa profil -->
    <form class="update-profile-form" method="POST" action="">
        <div class="form-group">
            <label for="username">Användarnamn:</label>
            <p class="guest_profile__p"><?php echo htmlspecialchars($user['username']); ?></p>
        </div>
        <div class="form-group">
            <label for="email">E-post:</label>
            <p class="guest_profile__p"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <?php foreach($followAll as $follow): ?>  
                
                <!-- DET FÖRSTA ÄR USERID ---------  DET ANDRA ÄR PROFILSIDAN MAN ÄR INNE PÅ -->
                <?php if($follow['followerID'] == $_SESSION['user_id'] && $follow['followedID'] == $_SESSION['GuestID']): ?> <!-- HÄR SKALL ÄVEN EN KONTROLL AV USERS SAMT EN KONTROLL EMOT ANVÄNDARENS PROFIL. SÅ MAN INTE KAN GILLA SIN EGNA SIDA-->
                    <?php $bool = false; ?>
                    <?php $_SESSION['deleteid'] = $follow['id']; ?>
                <?php endif ?>
            <?php endforeach ?>           
                    
        <!-- HÄR SKALL ÄNDRAS TILL DEN sidans ID man är inne på-->
            <?php if($visitProfile != $_SESSION['user_id']): ?> <!-- KONTROLLERA SÅ ATT INTE PROFILSIDAN MAN ÄR INNE PÅ ÄR ENS EGNA PROFIL -->
                <?php if($bool): ?>
                        <button name="follow-button">Follow</button>
                    <?php else: ?>
                        <button name="unfollow-button">Unfollow</button>
                <?php endif ?>                
            <?php endif ?>
    </form>

    <!-- Skicka DM -->
    <?php if($_SESSION['GuestID'] == $_SESSION['user_id']): ?>
        <?php header("Location: profile.php") ?>
    <?php else: ?>
        <form class="create-post-form" method="POST">
            <div class="form-group">
                
                    <button class="DM_funktion" id="toggleEditForm">Skriv ett Meddelande</button>

                    <div id="editForm2" class="edit-form" style="display: none;">
                        <h2>Chatt</h2>
                        <?php if (!empty($error_message)): ?>
                            <p style="color: red;"> <?php echo $error_message; ?></p>
                        <?php endif; ?>

                        <form action="guest_profile.php?id=<?php echo $_SESSION['GuestID']; ?>" method="post">                    

                            <label for="textInput">Text:</label>
                            <textarea id="textInput" name="textInput" rows="20" required placeholder="Inputs chatt message here..."></textarea>

                            <button type="submit" style="margin-top: 20px;">Skicka Meddelande</button>
                        </form>
                    </div>
                    <script>
                        document.getElementById('toggleEditForm').addEventListener('click', function () {
                            var editForm = document.getElementById('editForm2');
                            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
                        });
                    </script>
            </div>
            
        </form>
    <?php endif ?>
    
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
