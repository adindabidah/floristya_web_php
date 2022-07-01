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
$id_customer = $_GET['id_customer'] ?? '';

if(empty($id_customer)){
    $reply['error'] = 'ID Customer tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM customer where id_customer = :id_customer";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_customer', $id_customer);
    $statement->execute();
    $dataCustomer = $statement->fetch(PDO::FETCH_ASSOC);

    if($dataCustomer) {
        $stmCashier = $connection->prepare("select * from cashier where id_cashier = :id_cashier");
        $stmCashier->bindValue(':id_cashier', $dataCustomer['cashier']);
        $stmCashier->execute();
        $resultCashier = $stmCashier->fetch(PDO::FETCH_ASSOC);
        /*
         * Default cashier 'Tidak diketahui'
         */
        $cashier = [
            'id_cashier' => $dataCustomer['cashier'],
            'nama_lengkap' => 'Tidak diketahui'
        ];
        if ($resultCashier) {
            $cashier = [
                'id_cashier' => $resultCashier['id_cashier'],
                'nama_lengkap' => $resultCashier['nama_lengkap']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan cashier
         * Gabungkan data berdasarkan kolom id cashier
         * Jika id cashier tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_customer' => $dataCustomer['id_customer'],
            'nama_lengkap' => $dataCustomer['nama_lengkap'],
            'alamat' => $dataCustomer['alamat'],
            'nomor_hp' => $dataCustomer['nomor_hp'],
            'cashier' => $cashier,
            'createad_at' => $dataCustomer['created_at']
        ];
    }
    /*
 * Ambil data bunga berdasarkan kolom bunga
 */
    if($dataCustomer) {
        $stmBunga = $connection->prepare("select * from bunga where kode_bunga = :kode_bunga");
        $stmBunga->bindValue(':kode_bunga', $dataCustomer['bunga']);
        $stmBunga->execute();
        $resultBunga = $stmBunga->fetch(PDO::FETCH_ASSOC);
        /*
         * Default bunga 'Tidak diketahui'
         */
        $bunga = [
            'kode_bunga' => $dataCustomer['bunga'],
            'nama_bunga' => 'Tidak diketahui'
        ];
        if ($resultBunga) {
            $bunga = [
                'kode_bunga' => $resultBunga['kode_bunga'],
                'nama_bunga' => $resultBunga['nama_bunga']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan cashier
         * Gabungkan data berdasarkan kolom id cashier
         * Jika id cashier tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_customer' => $dataCustomer['id_customer'],
            'nama_lengkap' => $dataCustomer['nama_lengkap'],
            'alamat' => $dataCustomer['alamat'],
            'nomor_hp' => $dataCustomer['nomor_hp'],
            'cashier' => $cashier,
            'bunga' => $bunga,
            'createad_at' => $dataCustomer['created_at']
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
    $reply['error'] = 'Data tidak ditemukan ID Customer '.$id_customer;
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