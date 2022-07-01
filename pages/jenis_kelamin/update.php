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

$id = $formData['id'] ?? '';
$gender = $formData['gender'] ?? '';

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($id)){
    $reply['error'] = 'ID harus di isi';
    $isValidated = false;
}
if(empty($gender)){
    $reply['error'] = 'Gender harus diisi';
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
    $queryCheck = "SELECT * FROM jenis_kelamin where id = :id";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID'.$id;
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
    $query = "UPDATE jenis_kelamin SET gender = :gender
WHERE id = :id";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id", $id);
    $statement->bindValue(":gender", $gender);

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


/**
 * Show output to client
 */

$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);