<?php

require_once "authController.php";


// Try, load and verify the user datas from his cookies
$user = try_authentification();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($user == null) {
        echo json_encode(["error" => "Vous etes deja deconnecté"]);
        exit;
    }
    else {
        setcookie("auth_token", "", [
            "expires" => time() - 3600,
            "path" => "/",
            "secure" => true, 
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        echo json_encode(["success" => "Vous etes maintenant deconnecté du site"]);
    }
}
else if($_SERVER["REQUEST_METHOD"] == "GET") {
?>
    <!DOCTYPE html>
    <html lang="fr>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Deconnexion - Tracker</title>
        <meta charset="UTF-8">
    </head>
    <body>
    <?php
        if($user == null) {
    ?>
            <span>Vous etes deja deconnecté</span>
            <br>
            <a href='/'>Acceuil</a>
    <?php
        }
        else {
    ?>
        <div class="output">
            <?php
                echo "Vous etes connecté en tant que {$user['email']}";
            ?>
                
        </div>
        <button class="button">Me deconnecter</button>
        
        <script>
            button = document.querySelector(".button");
            output = document.querySelector(".output");
            button.addEventListener("click", event => logout());


            function logout() {
                fetch("/logout.php", {method: "POST"}
                ).then(response => response.json()
                ).then((data) => {
                    if(data.success) {
                        button.remove();
                        output.innerText = data.success
                    }
                    else if(data.error) {
                        output.innerText = data.error;
                    }
                })
                .catch(e => {
                    output.innerText = "An error occured. Try again later";
                });
            }

            
        </script>
    <?php
        }
    ?>
    </body>
    </html>
<?php
}
?>