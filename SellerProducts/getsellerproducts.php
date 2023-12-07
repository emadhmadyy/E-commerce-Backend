<?php
include('../connection.php');
require  '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

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
        $query = $mysqli->prepare('SELECT products.product_id, products.product_name, products.price, productdetails.description, productdetails.stock_quantity, productdetails.release_date
        from products inner join productdetails on products.product_id = productdetails.product_id WHERE seller_id = ? ;');
        $query->bind_param('i',$decoded->user_id);
        $query->execute();
        $array = $query->get_result();
        $response = [];
        $response["permissions"] = true;
        while ($product = $array->fetch_assoc()) {
            $response[] = $product;
        }
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