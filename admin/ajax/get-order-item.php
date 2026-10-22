<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['order_id'])) {
    $stmt = $mysqli->prepare("SELECT a.*,b.current_stock as current_stock,p.name as product_name,a.qty-a.out_qty as qty,b.batch_no,b.expiry_date,b.id as cs_id from  order_det a join current_stock b on a.product_id=b.prod_id and a.batch_no=b.batch_no join products p on a.product_id=p.id where a.mst_id=? and b.loc_id=? AND (a.qty != a.out_qty)");
    $stmt->bind_param("ii", $_POST['order_id'], $_POST['loc_id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


    echo  json_encode($res);
}
