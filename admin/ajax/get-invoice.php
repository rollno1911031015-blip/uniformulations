<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT a.* from stock_inward_mst a join stock_inward_det b on a.id=b.stock_mst_id where b.batch_no=? group by b.product_id");
  $stmt->bind_param("i", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $html = '<option value="">--Select Invoice--</option>';
  while ($row = $res->fetch_assoc())
    $html .= '<option value="' . $row['id'] . '">' . $row['gr_no'] . ' ('.$row['supplier_invoice'].')</option>';

  echo $html;
}
