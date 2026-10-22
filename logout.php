<?php

require('config.php');
$blank='';

$stmt = $mysqli->prepare("UPDATE `admin_users` set token=?,last_login=now(),last_ip=? WHERE  token=?  ");
$stmt->bind_param("sss",$blank,$_SERVER['REMOTE_ADDR'],$_SESSION['token']);
$stmt->execute();

unset($_COOKIE['token']); 
setcookie('token', null, -1, '/'); 

session_destroy();
header('Location:index');exit();

?>