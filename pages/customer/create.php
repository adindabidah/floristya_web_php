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
$id_customer = $_POST['id_customer'] ?? '';
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nomor_hp = $_POST['nomor_hp'] ?? '';
$cashier = $_POST['cashier'] ?? '';
$bunga = $_POST['bunga'] ?? 0;


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
if(empty($cashier)){
    $reply['error'] = 'Cashier harus di isi';
    $isValidated = false;
}
if(empty($bunga)){
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
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO customer (id_customer, nama_lengkap, alamat, nomor_hp, cashier, bunga) 
VALUES (:id_customer, :nama_lengkap, :alamat, :nomor_hp, :cashier, :bunga)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_customer", $id_customer);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":cashier", $cashier);
    $statement->bindValue(":bunga", $bunga);
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
$getResult = "SELECT * FROM customer WHERE id_customer = :id_customer";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_customer', $id_customer);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/*
 * Get cashier
 */
$stmCashier = $connection->prepare("SELECT * FROM cashier where id_cashier = :id_cashier");
$stmCashier->bindValue(':id_cashier', $result['cashier']);
$stmCashier->execute();
$resultCashier = $stmCashier->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat cashier'Tidak diketahui'
 */
$cashier = [
    'id_cashier' => $result['cashier'],
    'nama_lengkap' => 'Tidak diketahui'
];
if ($resultCashier) {
    $cashier = [
        'id_cashier' => $resultCashier['id_cashier'],
        'nama_lengkap' => $resultCashier['nama_lengkap']
    ];
}
/*
 * Get bunga
 */
$stmBunga = $connection->prepare("SELECT * FROM bunga where kode_bunga = :kode_bunga");
$stmBunga->bindValue(':kode_bunga', $result['bunga']);
$stmBunga->execute();
$resultBunga = $stmBunga->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat bunga 'Tidak diketahui'
 */
$bunga = [
    'kode_bunga' => $result['bunga'],
    'nama_bunga' => 'Tidak diketahui'
];
if ($resultBunga) {
    $bunga = [
        'kode_bunga' => $resultBunga['kode_bunga'],
        'nama_bunga' => $resultBunga['nama_bunga']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'id_customer' => $result['id_customer'],
    'nama_lengkap' => $result['nama_lengkap'],
    'alamat' => $result['alamat'],
    'nomor_hp' => $result['nomor_hp'],
    'cashier' => $cashier,
    'bunga' => $bunga,
    'createad_at' => $result['created_at']
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);