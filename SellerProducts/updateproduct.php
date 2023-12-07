<?php
include('../connection.php');
require  '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

$product_id = $_POST['product_id'];
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
        $query = $mysqli->prepare('UPDATE products SET product_name = ?, price = ? WHERE product_id = ?');
        $query->bind_param('sii',$product_name,$price,$product_id);
        $query->execute();
        $query1 = $mysqli->prepare('UPDATE productdetails SET description = ? , stock_quantity = ?, release_date = ? WHERE product_id = ?');
        $query1->bind_param('sisi',$description,$stock_quantity,$release_date,$product_id);
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