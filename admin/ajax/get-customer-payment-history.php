<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}
if (isset($_POST['id'])) {
     $stmt = $mysqli->prepare("SELECT a.*,c.company_name as name from lead_pay_followup  a join lead b on a.lead_id=b.id join company c on b.company_id=c.id where a.lead_id=? and b.active=1");
    // $stmt->bind_param("is", $_GET['customer_id'],$_GET['year']);
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode($res);
}
