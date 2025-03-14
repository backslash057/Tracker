<?php

require_once "authController.php";


// Try, load and verify the user datas from his cookies
$user = try_authentification();

// Redirect the user to logout if he is already connected
if($user != null) {
    header("Location: /logout.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(!$data) {
        http_response_code(400); // HTTP 400: Bad request
        echo json_encode(["error" => "Invalid data format. JSON expected"]);
        exit;
    }

    $email = filter_var(trim($data["email"]), FILTER_VALIDATE_EMAIL);
    $password = password_hash($data["pwd"], PASSWORD_BCRYPT);


    if(!$email) {
        http_response_code(400); // HTTP 400: Bad request
        echo json_encode(["error" => "Invalid email address"]);
        exit;
    }


    try {
        $insert_id = save_user($email, $password);

        $token = generateToken([
            "user_id" => $insert_id,

            // TODO: change the expiration limit and load from a global config
            "expires" => time() +(60 * 60 * 24 * 30)
        ]);

        setcookie("auth_token", $token, [
            "httponly" => true,  // Prevent XSS atack via Javascript
            "secure" => true,    // Send only over HTTPS
            "samesite" => "Strict", // Prevent CSRF attacks
            "expires" => time() + (60 * 60 * 24 * 30)
        ]);
        
        http_response_code(201); // HTTP 201: Created
        echo json_encode([
            "success" => "Authentification reussie"
        ]);

    } catch(mysqli_sql_exception $e) {
        error_log("SQL error: " . $e);

        if($e->getCode() == 1062) { // Mysqli duplicate entry error
            http_response_code(409); // HTTP 409: Conflict
            echo json_encode([
                "error" => "Un compte existe deja avec cette adresse email"
            ]);
        }
        else {
            http_response_code(500); // HTTP 500: Internal server error
            echo json_encode([
                "error" => "Une erreur est survenue. Veuillez reessayer"
            ]);
        }
    }

}
else if($_SERVER["REQUEST_METHOD"] == "GET") {
    $submitText = "S'inscrire";
    $altLabel = "Deja un compte? ";
    $altText = "Se connecter";
    
    require("authView.php"); // Common template between auth(login and signup) pages
}
?>