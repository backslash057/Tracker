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
    $password = isset($data["pwd"])? $data["pwd"] : "";

    if(!$email) {
        http_response_code(400); // HTTP 400: Bad request
        echo json_encode(["error" => "Invalid email adress"]);
        exit;
    }

    try {
        $user_id = check_user(
            $email, $password
        );

        error_log($user_id);

        if($user_id == null) {
            http_response_code(404); // HTTP 404: Not found
            echo json_encode([
                "error" => "Adresse email ou mot de passe incorrect"
            ]);
        }
        else {
            $token = generateToken([
                "user_id" => $user_id,
    
                // TODO: change the expiration limit and load from a global config
                "expires" => time() +(60 * 60 * 24 * 30)
            ]);
    
            setcookie("auth_token", $token, [
                "httponly" => true,  // Prevent XSS atack via Javascript
                "secure" => true,    // Send only over HTTPS
                "samesite" => "Strict", // Prevent CSRF attacks
                "expires" => time() + (60 * 60 * 24 * 30)
            ]);
            
            http_response_code(202); // HTTP 202: Accepted
            echo json_encode([
                "success" => "Connexion reussie"
            ]);
        }
    }catch(mysqli_sql_exeption $e) {
        error_log("SQL error: " . $e);

        http_response_code(500); // HTTP 500: Internal server error
        echo json_encode([
            "error" => "Une erreur est survenue. Veuillez reessayer"
        ]);
        
    }
}
else {
    require("authView.php");
}
?>

