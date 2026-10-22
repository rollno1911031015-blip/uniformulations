<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("SELECT  a.*,b.name,b.id as product_id,c.id as invoice_id,a.qty-a.return_qty as qty from stock_outward_det a JOIN products b on a.product_id=b.id join stock_outward_mst c on a.stock_outward_mst_id=c.id where c.lead_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Product.--</option>';
    while($row=$res->fetch_assoc()) 
    $html.= '<option value="'.$row['product_id'].'" data-qty="'.$row['qty'].'">'.$row['name'].' (Qty : '.$row['qty'].')</option>';
  
      echo $html;

}
  ?>