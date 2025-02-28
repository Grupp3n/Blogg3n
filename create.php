<?php     

    $servername = "localhost";
    $dbname = "nexlify";
    $username = "root";
    $password = "";
    

    try {
        // HÄR SKAPAS DATABASEN   GLÖM INTE ATT HA IGÅNG MySQL I XAMPP!!!
        if(isset($_POST['createDatabasButton'])) {
            $conn = new PDO("mysql:host=$servername", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "CREATE DATABASE nexlify";

            $conn->exec($query);

            echo "<p style='color:white;'>Databasen $dbname skapades Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        
        }


        // HÄR SKAPAS TABELLEN FÖR USERS   
        if(isset($_POST['createUserTableButton'])) {           

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "CREATE TABLE Users (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        firstname NVARCHAR(20) NOT NULL,
                        lastname NVARCHAR(50) NOT NULL,
                        email NVARCHAR(25) NOT NULL UNIQUE,
                        username NVARCHAR (25) NOT NULL UNIQUE,
                        password NVARCHAR(50) NOT NULL 
            )";

            $conn->exec($query);
            
            echo "<p style='color:white;'>Tabellen för Users skapades Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        }

        // HÄR SKAPAS TABELLEN FÖR Inlägg   
        if(isset($_POST['createPostsTableButton'])) {           

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "CREATE TABLE Posts (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        userID int UNSIGNED NOT NULL,
                        textInput NVARCHAR(250) NOT NULL,
                        image MEDIUMBLOB,
                        timeCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
            )";

            $conn->exec($query);
            
            echo "<p style='color:white;'>Tabellen för Posts skapades Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        }

        // HÄR SKAPAS TABELLEN FÖR Kommentrarer   
        if(isset($_POST['createCommentsTableButton'])) {           

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "CREATE TABLE Comments (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        userID int UNSIGNED NOT NULL,
                        postID int UNSIGNED NOT NULL,
                        textInput NVARCHAR(250) NOT NULL,
                        timeCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE,
                        FOREIGN KEY (postID) REFERENCES Posts(id) ON DELETE CASCADE
            )";

            $conn->exec($query);
            
            echo "<p style='color:white;'>Tabellen för Comments skapades Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        }

        if(isset($_POST['createLikesTableButton'])) {           

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "CREATE TABLE Likes (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        userID int UNSIGNED NOT NULL,
                        postID int UNSIGNED NOT NULL,
                        count int UNSIGNED,
                        FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE,
                        FOREIGN KEY (postID) REFERENCES Posts(id) ON DELETE CASCADE
            )";

            $conn->exec($query);
            
            echo "<p style='color:white;'>Tabellen för Likes skapades Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        }

        if(isset($_POST['dropsTableButton'])) {   
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

             $query = "DROP TABLE Comments";
             $query2 = "DROP TABLE Posts";            

            $conn->exec($query);
            $conn->exec($query2);
            
            echo "<p style='color:white;'>Tabellerna har tagits bort Framgångsrikt</p> <p style='color: green;'>✔</p><br>";

            $conn = null;
        }


    } catch (PDOException $e) {
        echo "<p style='color:white;'>" . $e->getMessage() . "</p>";
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>CREATE</title>
</head>
<body>
    
        <header class="create__header">
            <a href="index.php">TILL INDEX\HEM</a>        
        </header>

        <div class="container">
            <div class="container_inside">
                <form method="POST">
                    <button name="createDatabasButton">Skapa Databas</button>                    
                    <button name="createUserTableButton">Skapa Users table</button>
                    <button name="createPostsTableButton">Skapa Posts table</button>
                    <button name="createCommentsTableButton">Skapa Comments table</button>
                    <button name="createLikesTableButton">Skapa Likes table</button>
                    <button name="dropsTableButton">Drop Tables</button>
                </form>
            </div>
        </div>
   
</body>
</html>