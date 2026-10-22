<?php
require "../../config.php";

try {
    if (!empty($_POST['type']) &&  !empty($_POST['id'])) {
        if ($_POST['type'] == "edit") {


            $stmt = $mysqli->prepare("UPDATE  permission  set edit=? where id=?;");
            $stmt->bind_param("ii", $_POST['check'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $mysqli->prepare("UPDATE  permission  set view=? where id=?;");
            $stmt->bind_param("ii", $_POST['check'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(array('msg' => 'Success', 'error' => false));
    } else {
        echo json_encode(array('msg' => 'Error', 'error' => true));
    }
} catch (Exception $e) {
    echo json_encode(array('msg' => $e->getMessage(), 'error' => true));
}
