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
    $stmt = $mysqli->prepare("SELECT * FROM `customers` WHERE  id=?  and vendor_id=? ");
    $stmt->bind_param("ii",$_POST['id'],$user['id']);
    $stmt->execute();
    $data=$stmt->get_result()->fetch_assoc();
    $error=false;
    $stmt->close();
    if($data){
        echo json_encode($data);
    }
    else{
        echo "error";
    }
  }
 
  ?>