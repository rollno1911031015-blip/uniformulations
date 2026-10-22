<?php
require "../../config.php";
if (empty($_SESSION['token'])) {
    exit('error');
}

if (isset($_POST['id'])) {
    $stmt = $mysqli->prepare("SELECT * from lead where id=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    $res1 = [];
    $ids = explode(", ", $res['division']);
    foreach ($ids as $key => $value) {
        $stmt1 = $mysqli->prepare("SELECT * FROM admin_users WHERE FIND_IN_SET(?, division)  and role=2");
        $stmt1->bind_param("i", $value);
        $stmt1->execute();
        $res1[] = $stmt1->get_result()->fetch_assoc();
    }





    $json = json_encode($res1);
    $array = json_decode($json, true);

    $array = array_filter($array);

    // Step 3: Optionally remove duplicates (based on 'id' or another unique field)
    $uniqueArray = [];
    foreach ($array as $item) {
        if (!isset($uniqueArray[$item['id']])) {
            $uniqueArray[$item['id']] = $item;
        }
    }

    // Step 4: Re-encode to JSON
    $cleanedJson = json_encode(array_values($uniqueArray), JSON_PRETTY_PRINT);

    echo $cleanedJson;
}
