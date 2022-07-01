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
$kode_bunga = $res['kode_bunga'] ?? '';
/**
 * Validation int value
 */
$kode_bungaFilter = filter_var($kode_bunga, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($kode_bungaFilter === false){
    $reply['error'] = "kode bunga harus format INT";
    $isValidated = false;
}
if(empty($kode_bunga)){
    $reply['error'] = 'kode bunga harus diisi';
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
 * Cek apakah ID Teknisi tersedia
 */
try{
    $queryCheck = "SELECT * FROM bunga where kode_bunga = :kode_bunga";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode_bunga', $kode_bunga);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan kdoe '.$kode_bunga;
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
    $queryCheck = "DELETE FROM bunga where kode_bunga = :kode_bunga";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode_bunga', $kode_bunga);
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