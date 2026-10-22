<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['product_id'])) {
    $stmt = $mysqli->prepare("SELECT IFNULL(b.price, 0) as price FROM po_mst a JOIN po_det b ON a.id = b.po_id WHERE a.vendor_id = ? AND b.product_id = ? order by b.id desc");
    $stmt->bind_param("ii", $_POST['vendor_id'], $_POST['product_id']);
    $stmt->execute();
    $po_mst = $stmt->get_result()->fetch_assoc();
    if ($po_mst) {
       echo $po_mst['price'];
    } else {
        echo 0;
    }
      
}
