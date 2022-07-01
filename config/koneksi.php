<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:21
 */
$dbuser = "root";
$dbpassword = "";
$dbserver = "localhost";
$dbname = "floristya";

$dsn = "mysql:host={$dbserver};dbname={$dbname}";

$connection = null;
try{
    $connection = new PDO($dsn, $dbuser, $dbpassword);
}catch (Exception $exception){
    $response['error'] = $exception->getMessage();
    echo json_encode($response);
    die();
}