<?php
include('../connection.php');
$email = $_POST["email"];
$password = $_POST["password"];
$usertype_id = 1;
$business_name = $_POST["business_name"];
$business_address = $_POST["business_address"];
$business_phone_number = $_POST["business_phone_number"];

$hashed_password = password_hash($password,PASSWORD_DEFAULT);

$check_seller_query = $mysqli->prepare('select user_id from users where email = ?');
$check_seller_query->bind_param('s',$email);
$check_seller_query->execute();
$check_seller_query->store_result();
$num_rows = $check_seller_query->num_rows;

$response = [];
if($num_rows>0){
    $response["status"] = "User already exists";
    echo json_encode($response);
} else{
    $query = $mysqli->prepare('insert into users(email,password,usertype_id,business_name,business_address,business_phone_number) 
    values(?,?,?,?,?,?)');
    $query->bind_param('ssisss',$email,$hashed_password,$usertype_id,$business_name,$business_address,$business_phone_number);
    $query->execute();
    $response["status"] = "Registered Successfully!";
    echo json_encode($response);
}


