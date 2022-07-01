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
$id_cashier = $_GET['id_cashier'] ?? '';

if(empty($id_cashier)){
    $reply['error'] = 'ID Cashier tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM cashier where id_cashier = :id_cashier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_cashier', $id_cashier);
    $statement->execute();
    $dataCashier = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin berdasarkan kolom jenis_kelamin
     */
    if($dataCashier) {
        $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
        $stmJenisKelamin->bindValue(':id', $dataCashier['jenis_kelamin']);
        $stmJenisKelamin->execute();
        $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $dataCashier['jenis_kelamin'],
            'gender' => 'Tidak diketahui'
        ];
        if ($resultJenisKelamin) {
            $jenis_kelamin = [
                'id' => $resultJenisKelamin['id'],
                'gender' => $resultJenisKelamin['gender']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan jenis_kelamin
         * Gabungkan data berdasarkan kolom id jenis_kelamin
         * Jika id jenis kelamin tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_cashier' => $dataCashier['id_cashier'],
            'nama_lengkap' => $dataCashier['nama_lengkap'],
            'alamat' => $dataCashier['alamat'],
            'nomor_hp' => $dataCashier['nomor_hp'],
            'jenis_kelamin' => $jenis_kelamin,
            'no_kassa' => $dataCashier['no_kassa'],
            'shift' => $dataCashier['shift'],
            'createad_at' => $dataCashier['created_at']
        ];
    }

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
    $reply['error'] = 'Data tidak ditemukan ID Cashier '.$id_cashier;
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