<?php
require "../../config.php";
if(empty($_SESSION['token'])){
    exit('error');
  }
   
  if(isset($_POST['nlocid'])){
    $stmt=$mysqli->prepare("SELECT a.*,b.current_stock from products a left join current_stock b on a.id=b.prod_id and b.loc_id=? where b.loc_id=? and a.sub_category=?");
    $stmt->bind_param("iis",$_POST['nlocid'],$_POST['nlocid'],$_POST['subcategory']);
    $stmt->execute();
    $res=$stmt->get_result();
    $html= '<option value="">--Select Product--</option>';
    while($row=$res->fetch_assoc()) 
      $html.= '<option data-stock="'.$row['current_stock'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
  
      echo $html;

}
  ?>