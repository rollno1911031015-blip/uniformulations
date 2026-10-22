<?php
    require 'authentication.php';

    $error = true;
    $data = NULL;
    $msg = '';

    $stmt = $mysqli->prepare("SELECT * FROM categories");
    $stmt->execute();
    $category = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($category)) {
        $error = false;
        $data = $category;
    }else{
        $error = true;
        $msg = 'Data not found.';   
    }

    $response = array('data' => $data, 'error' => $error, 'msg' => $msg);
    echo json_encode($response);

    $mysqli->close();
?>
