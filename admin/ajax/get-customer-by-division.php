<?php
require "../../config.php";
 
  
  if(isset($_POST['id'])){
    $stmt = $mysqli->prepare("SELECT a.*,b.company_name FROM lead a  join company b on a.company_id=b.id WHERE FIND_IN_SET(?, a.division) and status=4 ");
    $stmt->bind_param("i",$_POST['id']);
    $stmt->execute();
    $data=$stmt->get_result();
    while ($row=$data->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . $row['company_name'] . ' (' . $row['name'] . ') / (' . $row['mobile'] . ')</option>';
    }
    
  }
 
  ?>