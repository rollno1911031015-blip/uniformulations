<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,b.id as prod_id,a.qty-a.return_qty as qty from stock_inward_det a JOIN products b on a.product_id=b.id  join stock_inward_mst c on a.stock_mst_id=c.id where c.vendor_id=?");
  $stmt->bind_param("i", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $html = '<option value="">--Select Product--</option>';
  while ($row = $res->fetch_assoc())
    $html .= '<option value="' . $row['prod_id'] . '" data-qty="' . $row['qty'] . '">' . $row['product_name'] . '  (Qty : ' . $row['qty'] . ')</option>';

  echo $html;
}
