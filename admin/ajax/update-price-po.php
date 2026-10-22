<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['id'])){
    $stmt=$mysqli->prepare("UPDATE po_det set price=? where id=?");
    $stmt->bind_param("di",$_POST['price'],$_POST['id']);
    $stmt->execute();
    
}
  ?>