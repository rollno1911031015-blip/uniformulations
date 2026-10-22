<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("SELECT a.*,b.name as prod_name,b.price as price from  requirement_det a join products b on a.product_id=b.id where a.mst_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
 
  
      echo  json_encode($res);

}
  ?>