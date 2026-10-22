<?php
include("../layouts/header.php");




// $stmt1 = $mysqli->prepare("SELECT * from role");

// $stmt1->execute();
// $role_mst = $stmt1->get_result();
// while ($row1 = $role_mst->fetch_assoc()) {


//     $stmt = $mysqli->prepare("SELECT * from permission_mst");
//     $stmt->execute();
//     $company = $stmt->get_result();
//     while ($row = $company->fetch_assoc()) {
//         $stmt2 = $mysqli->prepare("INSERT into permission (role_id,permission_id,name) values (?,?,?)");
//         $stmt2->bind_param("iis", $row1['id'], $row['id'],$row['name']);
//         $stmt2->execute();
//         $stmt2->close();

//     }
// }



$arr = "TABLET,SYRUP,DROPS,INTIMATE WASH
,CAPSULES
,OINTMENT
,INJECTION
,LOTION
,GEL
,NASAL SPRAY
,EYE DROP
,MOUTH PAINT
,SOAP
,CREAM
,MOUTH WASH
,SUSPENSION
,DRY SYRUP 
,SACHET
,EAR DROP
,NASAL DROP
,PROTIEN POWDER
,SHOTS
,RESPULE
,DENTAL
,OIL
,POWDER
,JUICE
,INFUSION
,SHAMPOO
,PROMOTIONAL
,OINTMENT 
";
// $ar=explode(",",$arr);


// $stmt1 = $mysqli->prepare("SELECT * from brand");

// $stmt1->execute();
// $role_mst = $stmt1->get_result();
// while ($row1 = $role_mst->fetch_assoc()) {

//     foreach ($ar as $key => $value) {

//     $stmt2 = $mysqli->prepare("INSERT into  categories (name,brand_id) values (?,?)");
//     $stmt2->bind_param("si", $value,$row1['id']);
//     $stmt2->execute();
//     $stmt2->close();

//     }



// }


$stmt = $mysqli->prepare("SELECT a.*,b.company_name as company from lead a join company b on a.company_id=b.id where a.status=4");
// $stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$customer = $stmt->get_result();
while ($pr = $customer->fetch_assoc()) {
    # code...



    $division = explode(", ", $pr['division']);
 
 
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
            $stmt1->bind_param('iii', $row['id'], $value, $pr['id']);
            $stmt1->execute();
            $customer_price = $stmt1->get_result()->fetch_assoc();
            if (!$customer_price) {

                $stmt = $mysqli->prepare("INSERT into customer_price (division_id,product_id,customer_id,price,sale_price) values (?,?,?,?,?)");
                $stmt->bind_param("iiiss",  $value, $row['id'], $pr['id'], $row['price'], $row['sale_rete']);
                $stmt->execute();
            }
        }
        $stmt->close();
    }
   
}
