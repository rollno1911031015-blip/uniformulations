<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['vendor_id'])) {
  $stmt = $mysqli->prepare("SELECT * from  po_mst where vendor_id=? and  status='Generate PO'");
  $stmt->bind_param("i", $_POST['vendor_id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $html = ' ';
  while ($row = $res->fetch_assoc())
    $html .= '<option value="' . $row['id'] . '">' . $row['id'] . ' (' . $row['description'] . ') / (' . $row['delivery_date'] . ')</option>';

  echo $html;
}
