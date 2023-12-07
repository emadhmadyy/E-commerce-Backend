<?php
include('../connection.php');
$email = $_POST["email"];
$password = $_POST["password"];
$usertype_id = 2;
$first_name = $_POST["first_name"];
$last_name = $_POST['last_name'];
$address = $_POST["address"];
$phone_number = $_POST["phone_number"];

$hashed_password = password_hash($password,PASSWORD_DEFAULT);

$check_user_query = $mysqli->prepare('select user_id from users where email = ?');
$check_user_query->bind_param('s',$email);
$check_user_query->execute();
$check_user_query->store_result();
$num_rows = $check_user_query->num_rows;

$response = [];
if($num_rows>0){
    $response["status"] = "User already exists";
    echo json_encode($response);
}
else{
    $query = $mysqli->prepare('insert into users(email,password,usertype_id,first_name,last_name,address,phone_number) 
    values(?,?,?,?,?,?,?)');
    $query->bind_param('ssissss',$email,$hashed_password,$usertype_id,$first_name,$last_name, $address,$phone_number);
    $query->execute();
    $response["status"] = "Registered Successfully!";
    echo json_encode($response);
}


