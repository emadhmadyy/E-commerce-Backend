<?php
include('../connection.php');
require  '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;


$product_name = $_POST["product_name"];
$price = $_POST["price"];
$description = $_POST["description"];
$stock_quantity = $_POST["stock_quantity"];
$release_date = $_POST["release_date"];

$headers = getallheaders();
if (!isset($headers['Authorization']) || empty($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

$authorizationHeader = $headers['Authorization'];
$token = null;
$token = trim(str_replace("Bearer", '', $authorizationHeader));
if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}
try {
    $key = "EH";
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    if ($decoded->usertype_id == 1) {
        $query = $mysqli->prepare('INSERT INTO products (product_name, price,seller_id) VALUES (?, ?,?)');
        $query->bind_param('sii',$product_name,$price,$decoded->user_id);
        $query->execute();
        $lastProductId = $mysqli->insert_id;
        $query1 = $mysqli->prepare('INSERT INTO productdetails (product_id, description, stock_quantity, release_date) VALUES (?, ?, ?, ?)');
        $query1->bind_param('isis',$lastProductId,$description,$stock_quantity,$release_date);
        $query1->execute();
        $response = [];
        $response["permissions"] = true;
    } else {
        $response = [];
        $response["permissions"] = false;
    }
    echo json_encode($response);
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}