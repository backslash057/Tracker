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
    <link rel="stylesheet" type="text/css" href="static/css/index.css">
</head>
<body>
    <h1>Page d'acceuil</h1>

<?php
    if($user != null){
?>
    <!-- fetch his transactions and display them -->

    <span>Vos transactions</span>
    <ul class="transList"></ul>
<?php       
    }
    else {
?>
    <a href="login.php">Se connecter</a><br>
    <a href="signup.php">S'inscrire</a>    
<?php
    }
?>
    <script src="/static/js/index.js"></script>
</body>
</html>
