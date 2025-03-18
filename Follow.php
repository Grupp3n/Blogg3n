<?php
session_start();
    
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
      
    require 'db_connect.php';

        $query = '  SELECT Follower.*, U.*
                    FROM Follower  
                    LEFT JOIN Users as U on followerID = U.id               
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

        $query = '  SELECT Follower.*, U.*
                    FROM Follower  
                    LEFT JOIN Users as U on followerID = U.id                     
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
        $userID = 3;

        if($_SERVER['REQUEST_METHOD'] == "POST") {

            if(isset($_POST['follow-button'])) {
                $query2 = "INSERT INTO Follower (followedID, followerID) VALUES (:followedID, :followerID)";                
                $stmt2 = $pdo->prepare($query2);               

                $stmt2->execute([
                    'followerID' => $_SESSION['user_id'], # Här skall man implentera USERID
                    'followedID' => 2  # Här lägger man till den användaren man är inne på
                ]);   
                header("location: follow.php");  # skall ändras så man resetar den sidan man är på (profilen)             
            }

            if(isset($_POST['unfollow-button'])) {
                
                $query = "DELETE FROM Follower WHERE id = :id";                
                $stmt = $pdo->prepare($query);     
                
                $stmt->execute([
                    'id' => $_SESSION['deleteid'], # Här skall man implentera USERID                    
                ]);    
                $delete = $stmt->fetchAll(PDO::FETCH_ASSOC); 
                header("location: follow.php");  # skall ändras så man resetar den sidan man är på (profilen)
            }
            
        }
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body class="body_follow">
    <header>    
                
        <div class="logo-con">
            <a href="index.php"><img src="img/transparent logo.png" alt="Nexlify"></a>
        </div>
            
        <div class="dropdown">
            <button class="dropbtn">Meny</button>
            <div class="dropdown-content">
                <?php if (!$INLOGGAD) : ?>
                    <a href="login.php">Log in</a>
                <?php else : ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logga ut</a>
                <?php endif; ?>
            </div>
        </div>
    </header> 
    <form method="POST">        
        <div class="body_follow__div">

            
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
            
        <div>
            <p>Following: <?php echo $counterFollowed ?></p>   
                 
           <?php foreach($follower as $follow) { 
                echo $follow['firstname'] . " " . $follow['lastname'];
            } ?>
        </div>
        
        <div>
            <p>Follower: <?php echo $counterFollower ?></p>
            <?php foreach($follower2 as $follow) { 
                echo $follow['firstname'] . " " . $follow['lastname'];
            } ?>
        </div>


    </div>
    </form>
</body>
</html>