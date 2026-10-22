<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['subcategory'])){
    $stmt=$mysqli->prepare("SELECT * from  products where sub_category=?");
    $stmt->bind_param("s",$_POST['subcategory']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Product--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['id'].'">'.$row['name'].' ('.$row['=product_code'].')</option>';
  
      echo $html;

}
  ?>