<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['challan_id'])){
    $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,c.name as user_name FROM challan_logs a JOIN products b on a.product_id=b.id JOIN users c on a.user_id=c.id WHERE  a.challan_id=?  and (a.particulars='Edit Inward Stock' or a.particulars='Add Inward Stock'  or a.particulars='Delete Inward') ");
    $stmt->bind_param("i",$_POST['challan_id']);
    $stmt->execute();
    $data=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
 
        echo json_encode($data);
 
  }
