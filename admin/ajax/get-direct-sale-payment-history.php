<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT  a.*,b.name as user from  direct_sale_payment a join admin_users b on a.user_id=b.id where direct_sale_id=?");
  $stmt->bind_param("i", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  echo json_encode($res);
}
