<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['customer_id'])){
    $stmt=$mysqli->prepare("SELECT * from   lead_address where lead_id=?");
    $stmt->bind_param("i",$_POST['customer_id']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Address--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'">'.$row['address'].' '.$row['city'].'  '.$row['state'].'  '.$row['pincode'].'</option>';
  
      echo $html;

}
  ?>