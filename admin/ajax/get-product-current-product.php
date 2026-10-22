<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("SELECT a.*,b.name as product_name,b.id as prod_id from current_stock a JOIN products b on a.prod_id=b.id where a.loc_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Invoice--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['prod_id'].'" data-qty="'.$row['current_stock'].'">'.$row['product_name'].'  (Qty : '.$row['current_stock'].')</option>';
  
      echo $html;

}
  ?>