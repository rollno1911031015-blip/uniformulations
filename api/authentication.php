<?php
require '../config.php';


$error = true;
$data = array();
$msg = '';


$headers = apache_request_headers();
$headers = array_change_key_case($headers, CASE_LOWER);
$token = $headers['token'];

$stmt = $mysqli->prepare("SELECT * FROM lead WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (empty($user)) {
    $msg = "Unauthorized request";
    $response = array('data' => $data, 'error' => $error, 'msg' => $msg);
    echo json_encode($response);
    die;
}
