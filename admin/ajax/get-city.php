<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['state'])){
    $stmt=$mysqli->prepare("SELECT  * from state_district where state=?");
    $stmt->bind_param("s",$_POST['state']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select City .--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option value="'.$row['District'].'">'.$row['District'].'</option>';
  
      echo $html;

}
  ?>