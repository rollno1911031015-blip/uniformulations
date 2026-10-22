<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['id'])) {
    $stmt = $mysqli->prepare("SELECT * from  products  where id=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();


    echo json_encode($res);
}
