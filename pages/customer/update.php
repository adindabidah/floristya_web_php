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

$id_customer = $formData['id_customer'] ?? '';
$nama_lengkap = $formData['nama_lengkap'] ?? '';
$alamat = $formData['alamat'] ?? '';
$nomor_hp = $formData['nomor_hp'] ?? '';
$idcashier = $formData['cashier'] ?? '';
$idbunga = $formData['bunga'] ?? '';

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($id_customer)){
    $reply['error'] = 'ID Customer harus di isi';
    $isValidated = false;
}
if(empty($nama_lengkap)){
    $reply['error'] = 'Nama Lengkap harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'Alamat harus di isi';
    $isValidated = false;
}
if(empty($nomor_hp)){
    $reply['error'] = 'Nomor HP harus di isi';
    $isValidated = false;
}
if(empty($idcashier)){
    $reply['error'] = 'Cashier harus di isi';
    $isValidated = false;
}
if(empty($idbunga)){
    $reply['error'] = 'Bunga harus di isi';
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
    $queryCheck = "SELECT * FROM customer where id_customer = :id_customer";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_customer', $id_customer);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Customer '.$id_customer;
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
    $query = "UPDATE customer SET nama_lengkap = :nama_lengkap, alamat = :alamat, nomor_hp = :nomor_hp, cashier = :cashier, bunga = :bunga
WHERE id_customer = :id_customer";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */  $statement->bindValue(":id_customer", $id_customer);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":cashier", $idcashier);
    $statement->bindValue(":bunga", $idbunga);

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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM customer where id_customer = :id_customer");
$stmSelect->bindValue(':id_customer', $id_customer);
$stmSelect->execute();
$dataCustomer = $stmSelect->fetch(PDO::FETCH_ASSOC);

    /*
 * Ambil data cashier berdasarkan kolom cashier
 */
    $dataFinal = [];
    if ($dataCustomer) {
        $stmCashier = $connection->prepare("select * from cashier where id_cashier = :id_cashier");
        $stmCashier->bindValue(':id_cashier', $dataCustomer['cashier']);
        $stmCashier->execute();
        $resultCashier = $stmCashier->fetch(PDO::FETCH_ASSOC);
        /*
         * Defulat cashier 'Tidak diketahui'
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
         * Gabungkan data berdasarkan kolom id_teknisi cashier
         * Jika id_teknisi cashier tidak ditemukan, default "tidak diketahui'
         */

        /*
* Ambil data bunga berdasarkan kolom bunga
*/
        $dataFinal = [];
        if ($dataCustomer) {
            $stmBunga = $connection->prepare("select * from bunga where kode_bunga = :kode_bunga");
            $stmBunga->bindValue(':kode_bunga', $dataCustomer['bunga']);
            $stmBunga->execute();
            $resultBunga = $stmBunga->fetch(PDO::FETCH_ASSOC);
            /*
             * Defulat bunga 'Tidak diketahui'
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
             * Transoform hasil query dari table customer dan bunga
             * Gabungkan data berdasarkan kolom kode bunga
             * Jika kode bunga tidak ditemukan, default "tidak diketahui'
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
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);