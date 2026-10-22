<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
  $stmt = $mysqli->prepare("SELECT * FROM `users` WHERE  token=?  and user_type='vendor' ");
  $stmt->bind_param("s",$_SESSION['token']);
  $stmt->execute();
  $user= $stmt->get_result()->fetch_assoc();
  
  if(empty($user)){
    exit('error');
  }
  if(isset($_POST['id'])){
    $stmt = $mysqli->prepare("SELECT * FROM `warranty` WHERE  cust_id=? and vendor_id=? ");
    $stmt->bind_param("ii",$_POST['id'],$user['id']);
    $stmt->execute();
    $data=$stmt->get_result();
    $error=false;
    $stmt->close();
    $html= '<option value="">--Select Purchased Product--</option>';
    while($row=$data->fetch_assoc()) 
      $html.= '<option value="'.$row['product_id'].'" >'.$row['product_name'].'</option>';

    if($data){
        echo $html;
    }
    else{
        echo "error";
    }
  }
 
  ?>