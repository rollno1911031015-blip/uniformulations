<?php
require '../config.php';

$error = true;
$data = NULL;
$msg = '';

if (!empty($_POST['mobile']) && !empty($_POST['password'])) {
    $stmt = $mysqli->prepare("SELECT * FROM lead WHERE    mobile = ? and  password = ?; ");
    $stmt->bind_param("ss", $_POST['mobile'], $_POST['password']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if (empty($res)) {
        $error = true;
        $msg = 'Invalid mobile or password.';
    } else {

        $token = generate_token(12);
        $stmt = $mysqli->prepare("UPDATE lead SET token=? WHERE id = ?");
        $stmt->bind_param("si", $token, $res['id']);
        $stmt->execute();
        $res['token'] = $token;
        $error = false;
        $msg = 'User logged in successfully.';
        $data = $res;
    }
} else {
    $error = true;
    $msg = 'Required fields are missing or empty.';
}


header('Content-Type: application/json');
$response = array('data' => $data, 'error' => $error, 'msg' => $msg);
echo json_encode($response);

$mysqli->close();
