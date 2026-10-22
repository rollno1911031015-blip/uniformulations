<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['lead_id'])){
    $stmt=$mysqli->prepare("SELECT  a.* from stock_outward_mst a join stock_outward_det b on a.id=b.stock_outward_mst_id  where a.lead_id=? and b.product_id=?");
    $stmt->bind_param("ii",$_POST['lead_id'],$_POST['product_id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Products--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'" >'.$row['invoice_date'].' (Gr nO:  '.$row['gr_no'].')</option>';
    echo $html;
  }
  
  ?>