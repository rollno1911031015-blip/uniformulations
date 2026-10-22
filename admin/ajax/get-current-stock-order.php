<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("SELECT a.*,p.price as sale_rate from current_stock a join products p on a.prod_id=p.id  where a.prod_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    
    $stmt = $mysqli->prepare("SELECT IFNULL(b.price, 0) as price FROM order_mst a JOIN order_det b ON a.id = b.mst_id WHERE a.customer_id = ? AND b.product_id = ? order by b.id desc");
    $stmt->bind_param("ii", $_POST['customer_id'], $_POST['id']);
    $stmt->execute();
    $po_mst = $stmt->get_result()->fetch_assoc();
    $old_price=0;
    if ($po_mst) {
       $old_price= $po_mst['price'];
    } else {
       $old_price=0;
    }
    foreach ($res as &$item) {
      $item['old_price'] = $old_price;
  }
     echo  json_encode($res);



}
  ?>