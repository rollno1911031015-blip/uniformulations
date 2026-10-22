<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT  * from  order_mst where customer_id=? and status='pending for approval'");
  $stmt->bind_param("i", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  echo json_encode($res);
}
