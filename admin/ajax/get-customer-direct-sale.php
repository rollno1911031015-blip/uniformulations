<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['id'])) {
    try {
        $stmt = $mysqli->prepare("SELECT a.*,b.company_name FROM lead a join company b on a.company_id=b.id WHERE FIND_IN_SET(?, a.division)");

    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $error = false;
    $stmt->close();
    } catch (\Throwable $th) {
        echo json_encode($th->getMessage());
    }

    if ($data) {
        echo json_encode($data);
    } else {
        echo "error";
    }
}
