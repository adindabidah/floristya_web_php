<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:25
 *
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$kode_bunga = $_POST['kode_bunga'] ?? '';
$nama_bunga = $_POST['nama_bunga'] ?? '';
$jenis_bunga = $_POST['jenis_bunga'] ?? '';
$warna_bunga = $_POST['warna_bunga'] ?? '';
$kategori_bunga = $_POST['kategori_bunga'] ?? '';
$deskripsi_bunga = $_POST['deskripsi_bunga']?? '';
$harga_bunga = $_POST['harga_bunga']?? '';



/**
 * Validation empty fields
 */
$isValidated = true;

if(empty($kode_bunga)){
    $reply['error'] = 'Kode Bunga harus diisi';
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
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO bunga (kode_bunga, nama_bunga, jenis_bunga, warna_bunga, kategori_bunga, deskripsi_bunga, harga_bunga) 
VALUES (:kode_bunga, :nama_bunga, :jenis_bunga, :warna_bunga, :kategori_bunga, :deskripsi_bunga, :harga_bunga)";
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

/*
 * Get last data
 */

$getResult = "SELECT * FROM bunga WHERE kode_bunga = :kode_bunga";
$stm = $connection->prepare($getResult);
$stm->bindValue(':kode_bunga', $kode_bunga);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);




/**
 * Show output to client
 * Set status info true
 */

$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);







/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);