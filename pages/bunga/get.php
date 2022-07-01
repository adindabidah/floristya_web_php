<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 09/06/2022
 * Time: 16:19
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$kode_bunga = $_GET['kode_bunga'] ?? '';

if(empty($kode_bunga)){
    $reply['error'] = 'kode bunga tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM bunga where kode_bunga = :kode_bunga";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode_bunga', $kode_bunga);
    $statement->execute();
    $dataBunga = $statement->fetch(PDO::FETCH_ASSOC);

        $dataFinal = [
            'kode_bunga' => $dataBunga['kode_bunga'],
            'nama_bunga' => $dataBunga['nama_bunga'],
            'jenis_bunga' => $dataBunga['jenis_bunga'],
            'warna_bunga' => $dataBunga['warna_bunga'],
            'kategori_bunga' => $dataBunga['kategori_bunga'],
            'deskripsi_bunga' => $dataBunga['deskripsi_bunga'],
            'harga_bunga' => $dataBunga['deskripsi_bunga'],
            'createad_at' => $dataBunga['created_at']
        ];


}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Show response
 */
if(!$dataFinal){
    $reply['error'] = 'Data tidak ditemukan kode '.$kode_bunga;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Otherwise show data
 */
$reply['status'] = true;
$reply['data'] = $dataFinal;
header('Content-Type: application/json');
echo json_encode($reply);