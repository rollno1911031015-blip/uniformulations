<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("UPDATE po_det set qty=? where id=?");
    $stmt->bind_param("ii",$_POST['qty'],$_POST['id']);
    $stmt->execute();
    
}
  ?>