<?php
header('Access-Contol-Allow-Origin:*');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Headers: GET, POST, PUT , DELETE, OPTIONS');

$host = "localhost";
$db_user = "root";
$db_pass = null;
$db_name = "Ecommerce_db";

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die("" . $mysqli->connect_error);
}

