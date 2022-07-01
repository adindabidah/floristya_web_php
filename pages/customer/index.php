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
    $statement = $connection->prepare("select * from customer order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsCustomer = $statement->fetchAll(PDO::FETCH_ASSOC);

        /*
            * Ambil data bunga
            */
        $stmBunga = $connection->prepare("select * from bunga");
        $isOk = $stmBunga->execute();
        $resultBunga = $stmBunga->fetchAll(PDO::FETCH_ASSOC);

        /*
         * Transoform hasil query dari table customer dan bunga
         * Gabungkan data berdasarkan kolom kode_bunga bunga
         * Jika kode_bunga bunga tidak ditemukan, default "tidak diketahui'
         */
        $finalResults = [];
        $idsBunga = array_column($resultBunga, 'kode_bunga');
        foreach ($resultsCustomer as $customer) {
            /*
             * Default cashier 'Tidak diketahui'
             */
            $bunga = [
                'kode_bunga' => $customer['bunga'],
                'nama_bunga' => 'Tidak diketahui'
            ];
            /*
             * Cari bunga berd kode_bunga
             */
            $findByIdBunga = array_search($customer['bunga'], $idsBunga);

            /*
             * Jika kode_bunga ditemukan
             */
            if ($findByIdBunga !== false) {
                $findDataBunga = $resultBunga[$findByIdBunga];
                $bunga= [
                    'kode_bunga' => $findDataBunga['bunga'],
                    'nama_bunga' => $findDataBunga['nama_bunga']
                ];
            }
            /*
         * Ambil data cashier
         */
            $stmCashier = $connection->prepare("select * from cashier");
            $isOk = $stmCashier->execute();
            $resultCashier = $stmCashier->fetchAll(PDO::FETCH_ASSOC);

            /*
             * Transoform hasil query dari table customer dan cashier
             * Gabungkan data berdasarkan kolom id_cashier cashier
             * Jika id_cashier cashier tidak ditemukan, default "tidak diketahui'
             */
            $finalResults = [];
            $idsCashier = array_column($resultCashier, 'id_cashier');
            foreach ($resultsCustomer as $customer) {
                /*
                 * Default cashier 'Tidak diketahui'
                 */
                $cashier = [
                    'id_cashier' => $customer['cashier'],
                    'nama_lengkap' => 'Tidak diketahui'
                ];
                /*
                 * Cari cashier berd kode
                 */
                $findByIdCashier = array_search($customer['cashier'], $idsCashier);

                /*
                 * Jika id_cashier ditemukan
                 */
                if ($findByIdCashier !== false) {
                    $findDataCashier = $resultCashier[$findByIdCashier];
                    $cashier = [
                        'id_cashier' => $findDataCashier['id_cashier'],
                        'nama_lengkap' => $findDataCashier['nama_lengkap']
                    ];
                }
                $finalResults[] = [
                    'id_customer' => $customer['id_customer'],
                    'nama_lengkap' => $customer['nama_lengkap'],
                    'alamat' => $customer['alamat'],
                    'nomor_hp' => $customer['nomor_hp'],
                    'cashier' => $cashier,
                    'bunga' => $bunga,
                    'created_at' => $customer['created_at']
                ];
            }
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