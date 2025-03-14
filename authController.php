<?php

require 'config.php';


function base64_url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64_url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}


function generateToken($payload) {
    global $secret_key;
    $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
    $headerEncoded = base64_url_encode($header);
    $payloadEncoded = base64_url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret_key, true);
    $signatureEncoded = base64_url_encode($signature);

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

function decodeToken($token) {
    global $secret_key;
    list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $token);

    $header = json_decode(base64_url_decode($headerEncoded), true);
    $payload = json_decode(base64_url_decode($payloadEncoded), true);

    $signature = base64_url_decode($signatureEncoded);
    $checkSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret_key, true);

    if ($signature === $checkSignature && $payload["expires"] > time()) {
        return $payload;
    }

    return null;
}

/*
Verifies the token, verify the user in database
@return $user the user fetched from the database
*/
function try_authentification() {
    if(isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $payload = decodeToken($token);

        if($payload != null && isset($payload["expires"]) && $payload['expires']>time()) {
            if(isset($payload["user_id"])) {
                try {
                    $user = find_user($payload["user_id"]);
                    return $user;
                } catch(mysqli_exception $e) {
                    error_log("[authController.php] SQL error when checking user from database: " + e->getMessage());
                }
            }
        }
    }

    return null;
}


function save_user($email, $password){
    // TODO: REMOVE THIS LINE FOR PRODUCTION
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // TODO: export database credentials to a more secure way
    $db = new mysqli("localhost", "root", "", "tracker");

    $stmt = $db->prepare("INSERT INTO TUser (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);
    
    $stmt->execute();
    
    $stmt->close();
    $insertion_id = $db->insert_id;
    $db->close();

    return $insertion_id;
}

function check_user($email, $password) {
    // TODO: REMOVE THIS LINE FOR PRODUCTION
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $db = new mysqli("localhost", "root", "", "tracker");

    $stmt = $db->prepare("SELECT user_id, password FROM TUser WHERE email=?");
    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $db->close();

    if ($user && password_verify($password, $user['password'])) {
        return $user['user_id'];
    }

    return null;
}

function find_user($user_id) {
    // TODO: REMOVE THIS LINE FOR PRODUCTION
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $db = new mysqli("localhost", "root", "", "tracker");

    $stmt = $db->prepare("
        SELECT email
        FROM TUser
        WHERE user_id=?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();
    $db->close();

    return $user;
}
?>
