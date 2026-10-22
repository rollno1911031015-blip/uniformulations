<?php
    require 'authentication.php';

    $error = true;
    $data = NULL;
    $msg = '';

    $stmt = $mysqli->prepare("SELECT * FROM brand");
    $stmt->execute();
    $brand = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($brand)) {
        $error = false;
        $data = $brand;
    }else{
        $error = true;
        $msg = 'Data not found.';   
    }

    $response = array('data' => $data, 'error' => $error, 'msg' => $msg);
    echo json_encode($response);

    $mysqli->close();
?>
