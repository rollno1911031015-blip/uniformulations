<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id']) && $_POST['id']>0){
    $stmt=$mysqli->prepare("SELECT  a.*,b.name,b.id as product_id from stock_outward_det a JOIN products b on a.product_id=b.id where a.stock_outward_mst_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Products--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['product_id'].'" data-qty="'.$row['qty'].'">'.$row['name'].' (Qty : '.$row['qty'].')</option>';
    echo $html;
  }
  else if(isset($_POST['prev_challan']) && $_POST['prev_challan']>0){
    $stmt=$mysqli->prepare("SELECT  * from products");
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Products--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'" data-qty="0">'.$row['name'].'</option>';
    echo $html;
  }
  ?>