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

$id_cashier = $formData['id_cashier'] ?? '';
$nama_lengkap = $formData['nama_lengkap'] ?? '';
$alamat = $formData['alamat'] ?? '';
$nomor_hp = $formData['nomor_hp'] ?? '';
$idjenis_kelamin = $formData['jenis_kelamin'] ?? 0;
$no_kassa = $formData['no_kassa'] ?? '';
$shift = $formData['shift'] ?? '';

/**
 * Validation int value
 */
$id_cashierFilter = filter_var($id_cashier, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($id_cashierFilter === false){
    $reply['error'] = "ID Cashier harus format INT";
    $isValidated = false;
}
if(empty($id_cashier)){
    $reply['error'] = 'ID Cashier harus di isi';
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
if(empty($idjenis_kelamin)){
    $reply['error'] = 'Jenis Kelamin harus di isi';
    $isValidated = false;
}
if(empty($no_kassa)){
    $reply['error'] = 'Nomor Kassa harus di isi';
    $isValidated = false;
}
if(empty($shift)){
    $reply['error'] = 'Shift harus di isi';
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
    $queryCheck = "SELECT * FROM cashier where id_cashier = :id_cashier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_cashier', $id_cashierFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Teknisi '.$id_cashierFilter;
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
    $query = "UPDATE cashier SET nama_lengkap = :nama_lengkap, alamat = :alamat, nomor_hp = :nomor_hp, jenis_kelamin = :jenis_kelamin, no_kassa = :no_kassa, shift = :shift
WHERE id_cashier = :id_cashier";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_cashier", $id_cashier);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":jenis_kelamin", $idjenis_kelamin);
    $statement->bindValue(":no_kassa", $no_kassa);
    $statement->bindValue(":shift", $shift);
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
$stmSelect = $connection->prepare("SELECT * FROM cashier where id_cashier = :id_cashier");
$stmSelect->bindValue(':id_cashier', $id_cashierFilter);
$stmSelect->execute();
$dataCashier = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data jenis kelamin berdasarkan kolom jenis kelamin
 */
$dataFinal = [];
if($dataCashier) {
    $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
    $stmJenisKelamin->bindValue(':id', $dataCashier['jenis_kelamin']);
    $stmJenisKelamin->execute();
    $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat jenis kelamin 'Tidak diketahui'
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
     *
     */

            $dataFinal = [
                'id_cashier' => $dataCashier['id_cashier'],
                'nama_lengkap' => $dataCashier['nama_lengkap'],
                'alamat' => $dataCashier['alamat'],
                'nomor_hp' => $dataCashier['nomor_hp'],
                'jenis_kelamin' => $jenis_kelamin,
                'no_kassa' => $dataCashier['no_kassa'],
                'shift' => $dataCashier['shift'],
                'created_at' => $dataCashier['created_at']
            ];
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);