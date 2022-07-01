<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 02/06/2022
 * Time: 20:07
 * @var $connection PDO
 */

/*
 * Validate http method
 */


/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$id_cashier = $res['id_cashier'] ?? '';
/**
 * Validation int value
 */
$id_cashierFilter = filter_var($id_cashier, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($id_cashierFilter === false){
    $reply['error'] = "ID Cashier harus format INT";
    $isValidated = false;
}
if(empty($id_cashier)){
    $reply['error'] = 'ID Cashier harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 *
 * Cek apakah ID Cashier tersedia
 */
try{
    $queryCheck = "SELECT * FROM cashier where id_cashier = :id_cashier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_cashier', $id_cashier);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Cashier '.$id_cashier;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM cashier where id_cashier = :id_cashier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_cashier', $id_cashier);
    $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
$reply['status'] = true;
echo json_encode($reply);