<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

$parts = explode("/", $_SERVER['REQUEST_URI']);

$service = $parts[2];

$id = $parts[3] ?? null;

$services = ["reservation", "room", "customer"];

if(!in_array($service, $services)){
    http_response_code(404);
    exit;
}

$database = new Database(
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);

$user_gateway = new UserGateway($database);

$codec = new JWTCodec($_ENV['SECRET_KEY']);

$auth = new Auth($user_gateway, $codec);

if(!$auth->authenticateAccessToken()){
    exit;
}

$user_id = $auth->getUserID();

echo "successful call";

// switch($service){
//     case "reservation":
//         echo "reservation service";
        
//         break;
//     case "room":
//         echo "room management";

//         break;
//     case "customer":
//         echo "customer service";

//         break;
//     default:
        
// }



