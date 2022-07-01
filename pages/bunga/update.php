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
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$kode_bunga = $formData['kode_bunga'] ?? '';
$nama_bunga = $formData['nama_bunga'] ?? '';
$jenis_bunga = $formData['jenis_bunga'] ?? '';
$warna_bunga = $formData['warna_bunga'] ?? '';
$kategori_bunga = $formData['kategori_bunga'] ?? '';
$deskripsi_bunga = $formData['deskripsi_bunga']?? '';
$harga_bunga = $formData['harga_bunga']?? '';

/**
 * Validation int value
 */
$kode_bungaFilter = filter_var($kode_bunga, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($kode_bungaFilter === false){
    $reply['error'] = "Kode Bunga harus format INT";
    $isValidated = false;
}
if(empty($kode_bunga)){
    $reply['error'] = 'Kode Bunga harus di isi';
    $isValidated = false;
}
if(empty($nama_bunga)){
    $reply['error'] = 'Nama Bunga harus diisi';
    $isValidated = false;
}
if(empty($jenis_bunga)){
    $reply['error'] = 'Jenis Bunga harus di isi';
    $isValidated = false;
}
if(empty($warna_bunga)){
    $reply['error'] = 'Warna Bunga harus di isi';
    $isValidated = false;
}
if(empty($kategori_bunga)){
    $reply['error'] = 'Kategori Bunga harus di isi';
    $isValidated = false;
}
if(empty($deskripsi_bunga)){
    $reply['error'] = 'Deskripsi Bunga harus di isi';
    $isValidated = false;
}
if(empty($harga_bunga)){
    $reply['error'] = 'Harga Bunga harus di isi';
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM bunga where kode_bunga = :kode_bunga";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode_bunga', $kode_bungaFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan kode bunga'.$kode_bungaFilter;
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
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE bunga SET nama_bunga = :nama_bunga, jenis_bunga = :jenis_bunga, warna_bunga = :warna_bunga, kategori_bunga = :kategori_bunga, deskripsi_bunga = :deskripsi_bunga, harga_bunga = :harga_bunga
WHERE kode_bunga = :kode_bunga";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":kode_bunga", $kode_bunga);
    $statement->bindValue(":nama_bunga", $nama_bunga);
    $statement->bindValue(":jenis_bunga", $jenis_bunga);
    $statement->bindValue(":warna_bunga", $warna_bunga);
    $statement->bindValue(":kategori_bunga", $kategori_bunga);
    $statement->bindValue(":deskripsi_bunga", $deskripsi_bunga);
    $statement->bindValue(":harga_bunga", $harga_bunga);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);