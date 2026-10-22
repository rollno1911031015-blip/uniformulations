<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['id'])) {

    $ids=implode(', ',$_POST['id']);
    
    $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name from  po_det a join products b on a.product_id=b.id where a.po_id in (".$ids.")");
   // $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  
 
    echo json_encode($res) ;
}
