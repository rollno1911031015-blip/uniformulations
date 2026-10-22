<?php
try {
    $mysqli = $_SERVER['SERVER_NAME'] == 'localhost' ? new mysqli("localhost", "root", "", "dm-pharma-global","3307") : new mysqli("localhost", "u261892001_dmpharmaglobal", "Dmpharmaglobal@3210", "u261892001_dmpharmaglobal");

    $mysqli->set_charset("utf8mb4");
    date_default_timezone_set('Asia/Calcutta');
    $now = new DateTime();
    $mins = $now->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
    $stmt = $mysqli->prepare("SET time_zone='$offset';");
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo 'Error connecting to database'; //Should be a message a typical user could understand
}



try {
    $stmt = $mysqli->prepare("SELECT  * from lead  where cron_status=1");
    $stmt->execute();
    $lead = $stmt->get_result()->fetch_assoc();
    $stmt->close();

   




    $stmt = $mysqli->prepare("SELECT a.* from lead a  where a.id=?");
    $stmt->bind_param('i', $lead["id"]);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    $division = explode(", ", $customer['division']);
 
    foreach ($division as $key => $value) {

        $stmt = $mysqli->prepare("SELECT * from brand where id=?");
        $stmt->bind_param('i', $value);
        $stmt->execute();
        $brand = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT * from products where brand=?");
        $stmt->bind_param('s', $brand['name']);
        $stmt->execute();
        $products = $stmt->get_result();
        while ($row = $products->fetch_assoc()) {

            $stmt1 = $mysqli->prepare("SELECT * from customer_price where product_id=? and division_id=? and customer_id=?");
            $stmt1->bind_param('iii', $row['id'], $value, $lead["id"]);
            $stmt1->execute();
            $customer_price = $stmt1->get_result()->fetch_assoc();
            if (!$customer_price) {

                $stmt = $mysqli->prepare("INSERT into customer_price (division_id,product_id,customer_id,price,sale_price) values (?,?,?,?,?)");
                $stmt->bind_param("iiiss",  $value, $row['id'], $lead["id"], $row['price'], $row['sale_rete']);
                $stmt->execute();
            }
        }
        $stmt->close();
    }
    $stmt = $mysqli->prepare("UPDATE lead SET cron_status = 0 WHERE id = ?");
    $stmt->bind_param("i", $lead["id"]);
    $stmt->execute();
    $stmt->close();
    echo "Save Successfully";
} catch (\Throwable $th) {
    echo $th->getMessage();
}
