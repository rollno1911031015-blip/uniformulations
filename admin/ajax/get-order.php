<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['customer_id'])){
    $stmt=$mysqli->prepare("SELECT * from  order_mst where customer_id=? and (status!='delivered' or status!='pending')");
    $stmt->bind_param("s",$_POST['customer_id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Order--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'">'.$row['id'].' ('.$row['order_id'].')</option>';
  
      echo $html;

}
  ?>