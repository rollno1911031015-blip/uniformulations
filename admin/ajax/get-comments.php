<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
  exit('error');
}

if (isset($_POST['id'])) {
  $stmt = $mysqli->prepare("SELECT  a.*,b.name as user,c.name as status from  leads_comment a join admin_users b on a.user_id=b.id join status c on a.status=c.id where a.lead_id=?");
  $stmt->bind_param("s", $_POST['id']);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  echo json_encode($res);
}
