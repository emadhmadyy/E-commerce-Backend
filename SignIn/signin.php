<?php
include('../connection.php');
require '../vendor/autoload.php';

use Firebase\JWT\JWT;

$email = $_POST['email'];
$password = $_POST["password"];

$check_user_query = $mysqli->prepare('select usertype_id from users where email = ?');
$check_user_query->bind_param('s',$email);
$check_user_query->execute();
$check_user_query->store_result();
$num_rows = $check_user_query->num_rows;
$check_user_query->bind_result($usertype_id);
$check_user_query->fetch();

$response = [];
if($num_rows == 0){
    $response["status"] = "User not found";
    echo json_encode($response);
}
else{
    if($usertype_id == 1){
        $get_seller_data_query = $mysqli->prepare("select user_id,password,business_name,business_address,business_phone_number from users where email = ?");
        $get_seller_data_query->bind_param('s',$email);
        $get_seller_data_query->execute();
        $get_seller_data_query->store_result();
        $get_seller_data_query->bind_result($user_id,$hashed_password,$business_name,$business_address,$business_phone_number);
        $get_seller_data_query->fetch();
        if(password_verify($password,$hashed_password)){
            $key = "EH";
            $payload_array = [];
            $payload_array["user_id"] = $user_id;
            $payload_array["usertype_id"] = $usertype_id;
            $payload_array["business_name"] = $business_name;
            $payload_array["business_address"] = $business_address;
            $payload_array["business_phone_number"] = $business_phone_number;
            $payload_array["exp"] = time() + 3600;
            $payload = $payload_array;
            $response['status'] = 'logged in';
            $jwt = JWT::encode($payload, $key, 'HS256');
            $response['jwt'] = $jwt;
            echo json_encode($response);
        }else{
             $response["status"] = "Wrong Credintials";
             echo json_encode($response);
        }
    }
    else if($usertype_id ==2){
        $get_user_data_query = $mysqli->prepare("select user_id,password,first_name,last_name,address,phone_number from users where email = ?");
        $get_user_data_query->bind_param('s',$email);
        $get_user_data_query->execute();
        $get_user_data_query->store_result();
        $get_user_data_query->bind_result($user_id,$hashed_password,$first_name,$last_name, $address,$phone_number);
        $get_user_data_query->fetch();
        if(password_verify($password,$hashed_password)){
            $key = "EH";
            $payload_array = [];
            $payload_array["user_id"] =$user_id;
            $payload_array["usertype_id"] = $usertype_id;
            $payload_array["first_name"] = $first_name;
            $payload_array["last_name"] = $last_name;
            $payload_array["address"] = $address;
            $payload_array["phone_number"] = $phone_number;
            $payload = $payload_array;
            $response["status"] = "logged in";
            $jwt = JWT::encode($payload,$key,'HS256');
            $response["jwt"] = $jwt;
            echo json_encode($response);
        }
        else{
            $response["status"] = "Wrong Credintials";
            echo json_encode($response);
        }
    }
}