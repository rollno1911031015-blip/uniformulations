<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("SELECT  a.*,b.name  from  purchase_return_det a JOIN  products b on a.product_id=b.id where a.p_return_id=?");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $res=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode($res);
}
  ?>