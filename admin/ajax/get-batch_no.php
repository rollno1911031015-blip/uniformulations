<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT * from current_stock where prod_id=?");
  $stmt->bind_param("i", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $html = '<option value="">--Select Batch No--</option>';
  while ($row = $res->fetch_assoc())
    $html .= '<option value="' . $row['id'] . '" data-cs="'.$row['current_stock'].'">' . $row['batch_no'] . '</option>';

  echo $html;
}
