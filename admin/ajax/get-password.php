<?php
require "../../config.php";

 
  if(isset($_POST['passwrdd'])){
     
    $stmt=$mysqli->prepare("SELECT *from settings where override_passwrd=? ");
    $stmt->bind_param("s",$_POST['passwrdd']);
    $stmt->execute();
    $res=$stmt->get_result();
    while($row=$res->fetch_assoc()){
           $chk= $row['override_passwrd'];
    } 
     error_reporting(0);
  echo '<input type="hidden" value="'.$chk.'" id="chkpasswrd">';
      

}
  ?>