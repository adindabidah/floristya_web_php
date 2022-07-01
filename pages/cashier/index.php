<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:22
 * @var $connection PDO
 */
try{
    /**
     * Prepare query customer limit 50 rows
     */
    $statement = $connection->prepare("select * from cashier order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsCashier = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin
     */
    $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin");
    $isOk = $stmJenisKelamin->execute();
    $resultJenisKelamin = $stmJenisKelamin->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table customer dan jenis_kelamin
     * Gabungkan data berdasarkan kolom id jenis_kelamin
     * Jika id jenis_kelamin tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsJenisKelamin = array_column($resultJenisKelamin, 'id');
    foreach ($resultsCashier as $cashier) {
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $cashier['jenis_kelamin'],
            'gender' => 'Tidak diketahui'
        ];
        /*
         * Cari jenis kelamin berd id
         */
        $findByIdJenisKelamin = array_search($cashier['jenis_kelamin'], $idsJenisKelamin);

        /*
         * Jika id ditemukan
         */
        if ($findByIdJenisKelamin !== false) {
            $findDataJenisKelamin = $resultJenisKelamin[$findByIdJenisKelamin];
            $jenis_kelamin = [
                'id' => $findDataJenisKelamin['id'],
                'gender' => $findDataJenisKelamin['gender']
            ];
        }

                $finalResults[] = [
                    'id_cashier' => $cashier['id_cashier'],
                    'nama_lengkap' => $cashier['nama_lengkap'],
                    'alamat' => $cashier['alamat'],
                    'nomor_hp' => $cashier['nomor_hp'],
                    'jenis_kelamin' => $jenis_kelamin,
                    'no_kassa' => $cashier['no_kassa'],
                    'shift' => $cashier['shift'],
                    'created_at' => $cashier['created_at']
                ];
            }

    $reply['data'] = $finalResults;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
$reply['status'] = true;
echo json_encode($reply);