<?php
      session_start();
   

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

                echo "Databasen $dbname skapades Framgångsrikt <p style='color: red;'>✔</p><br>";

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
                
                echo "Tabellen för Users skapades Framgångsrikt <p style='color: red;'>✔</p><br>";

                $conn = null;
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
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
                    <button name="createUserTableButton">Skapa Users tabke</button>
                </form>
            </div>
        </div>
   
</body>
</html>