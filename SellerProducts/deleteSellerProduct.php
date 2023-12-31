<?php
include('../connection.php');
require  '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

$product_id = $_POST["product_id"];

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
        $query = $mysqli->prepare('delete products, productdetails from products JOIN productdetails ON products.product_id = productdetails.product_id where products.product_id = ? and seller_id=?');
        $query->bind_param('ii',$product_id,$seller_id);
        $query->execute();
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