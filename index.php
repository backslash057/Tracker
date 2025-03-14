<?php

require_once "authController.php";

// Try, load and verify the user datas from his cookies
$user = try_authentification();


?>

<!DOCTYPE html>
<html lang="fr>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceuil - Tracker</title>
</head>
<body>
    <h1>Page d'acceuil</h1>

<?php
    if($user != null){
        echo "Bienvenue {$user['email']}";
        echo "<br>";
        echo "<a href='/logout.php'>Logout</a>";
    }
    else {
?>
    <a href="login.php">Se connecter</a><br>
    <a href="signup.php">S'inscrire</a>    
<?php
    }
?>
    <!-- <script src="/static/js/debug.js"></script> -->
</body>
</html>
