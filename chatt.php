<?php
session_start();

if(!$_SESSION['INLOGGAD']) {
    header("location: index.php");
    exit;
} else {

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post2'])) {
    
        // Hämtar alla users för att veta vilket ID man skall skicka meddelande till
        $stmt = $pdo->prepare("SELECT * FROM users WHERE firstname = :firstname");
        $stmt->execute([':firstname' => $_POST['receiver']]);
        $userName = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $receiver_id = $userName['id'];      //denna raden skall bytas mot användarnamn och inte ID
        
        $post_content = trim($_POST['post_content2']);
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
    
    $posts2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    
    $userchatt = [];
    // Här fixar jag så jag har alla userIDs som har chattat och tagit bort den usern som är inloggad.
    foreach($posts2 as $post) {
    
        $sender = $post['senderID'];
    
        
            if(!in_array($sender, $userchatt)) {
                $userchatt[] = $sender;
            }
        
    }
    
      // KOD SLUTAR HÄR FÖR CHATT

}
?>