<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['name'])){
    $stmt=$mysqli->prepare("SELECT * from  products where brand=?");
    $stmt->bind_param("s",$_POST['name']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Product--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'">'.$row['name'].' ('.$row['=product_code'].')</option>';
  
      echo $html;

}
  ?>