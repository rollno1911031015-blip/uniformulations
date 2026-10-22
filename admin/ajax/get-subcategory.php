<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['name'])){
    $stmt=$mysqli->prepare("SELECT   * from sub_categories where category_id=?");
    $stmt->bind_param("s",$_POST['name']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Subcategory--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['name'].'">'.$row['name'].'</option>';
  
      echo $html;

}
  ?>