<?php
    session_start();
      
    require 'db_connect.php';

    $query = '  SELECT * 
                FROM Users                 
                -- WHERE userID = :id
            ';                     
        $stmt = $pdo->prepare($query);
        // $stmt->execute(['id' => $_SESSION['user_id']]);
        $stmt->execute();
        $follower = $stmt->fetchAll(PDO::FETCH_ASSOC);    
        
        $number;
        if(!empty($follower)) {
            foreach($follower as $follow) {
                // if($comment['userID'] == $_SESSION['user_id'] && $comment['postID'] == $_GET['id']){
                //     $bool = true;
                //     $likeID = $comment['id'];
                // }    
                
                echo "ID: " . $follow['id'] . " Namn: " . $follow['firstname'];
                echo "<br>";

                if($follow['id'] == 1) {
                    $number = $follow['id'];
                }
            }    
        } else {
            echo "<p style='color: red;'> NÅGOT FEL </p>";
        }


        $query = '  SELECT * 
                    FROM Follower                 
                    WHERE followedID = :id
                ';  
        $stmt = $pdo->prepare($query);
        
        $stmt->execute(['id' => 3]);  # Skall ändra till USERID
        $follower = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $counterFollower = 0;
        $counterFollowed = 0;

        # Denna skall räkna hur många det är som följer användaren
        foreach($follower as $follow) { 

            if($follow['followerID']) {
                $counterFollower += 1;
            }
            
        }

        $query = '  SELECT * 
                    FROM Follower                 
                    WHERE followerID = :id
                ';  
        $stmt = $pdo->prepare($query);
        
        $stmt->execute(['id' => 3]);  # Skall ändra till USERID
        $follower2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        # Denna skall räkna hur många användaren följer
        foreach($follower2 as $follow) {
            
            if($follow['followedID']) {
                $counterFollowed += 1;
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
        
        $visitProfile = 2;
        $userID = 3;

        if($_SERVER['REQUEST_METHOD'] == "POST") {

            if(isset($_POST['follow-button'])) {
                $query2 = "INSERT INTO Follower (followedID, followerID) VALUES (:followedID, :followerID)";                
                $stmt2 = $pdo->prepare($query2);               

                $stmt2->execute([
                    'followerID' => 3, # Här skall man implentera USERID
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
    <form method="POST">        

            <p>Alma</p>
            <?php foreach($followAll as $follow): ?>  
                
                <!-- DET FÖRSTA ÄR USERID ---------  DET ANDRA ÄR PROFILSIDAN MAN ÄR INNE PÅ -->
                <?php if($follow['followerID'] == 3 && $follow['followedID'] == 1): ?> <!-- HÄR SKALL ÄVEN EN KONTROLL AV USERS SAMT EN KONTROLL EMOT ANVÄNDARENS PROFIL. SÅ MAN INTE KAN GILLA SIN EGNA SIDA-->
                    <?php $bool = false; ?>
                    <?php $_SESSION['deleteid'] = $follow['id']; ?>
                    <?php var_dump($follow['id']); ?>
                <?php endif ?>
            <?php endforeach ?>           
                    
                    
            <?php if($visitProfile != $userID): ?> <!-- KONTROLLERA SÅ ATT INTE PROFILSIDAN MAN ÄR INNE PÅ ÄR ENS EGNA PROFIL -->
                <?php if($bool): ?>
                        <button name="follow-button">Follow</button>
                    <?php else: ?>
                        <button name="unfollow-button">Unfollow</button>
                <?php endif ?>                
            <?php endif ?>

            <p>Following: <?php echo $counterFollowed ?></p>
            <p>Follower: <?php echo $counterFollower ?></p>
        



    </form>
</body>
</html>