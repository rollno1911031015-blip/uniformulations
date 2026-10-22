<?php
include("../layouts/header.php");

// $stmt = $mysqli->prepare("SELECT a.*,b.company_name as company from lead a join company b on a.company_id=b.id where a.id=?");
// $stmt->bind_param('i', $_GET['id']);
// $stmt->execute();
// $customer = $stmt->get_result()->fetch_assoc();
// $stmt->close();


// $division = explode(", ", $customer['division']);


// foreach ($division as $key => $value) {

//     $stmt = $mysqli->prepare("SELECT * from brand where id=?");
//     $stmt->bind_param('i', $value);
//     $stmt->execute();
//     $brand = $stmt->get_result()->fetch_assoc();
//     $stmt->close();

//     $stmt = $mysqli->prepare("SELECT * from products where brand=?");
//     $stmt->bind_param('s', $brand['name']);
//     $stmt->execute();
//     $products = $stmt->get_result();
//     while ($row = $products->fetch_assoc()) {

//         $stmt1 = $mysqli->prepare("SELECT * from customer_price where product_id=? and division_id=? and customer_id=?");
//         $stmt1->bind_param('iii', $row['id'], $value, $_GET['id']);
//         $stmt1->execute();
//         $customer_price = $stmt1->get_result()->fetch_assoc();
//         if (!$customer_price) {

//             $stmt = $mysqli->prepare("INSERT into customer_price (division_id,product_id,customer_id,price,sale_price) values (?,?,?,?,?)");
//             $stmt->bind_param("iiiss",  $value, $row['id'], $_GET['id'], $row['price'], $row['sale_rete']);
//             $stmt->execute();
//         }
//     }
//     $stmt->close();
// }



if (isset($_POST['btnSave'])) {


    foreach ($_POST['update_price'] as $key => $value) {
        $stmt = $mysqli->prepare("UPDATE customer_price set price=? where id=?");
        $stmt->bind_param("di",  $value[0], $key);
        $stmt->execute();
    }
    foreach ($_POST['sale_price'] as $key => $value) {
        $stmt = $mysqli->prepare("UPDATE customer_price set sale_price=? where id=?");
        $stmt->bind_param("di",  $value[0], $key);
        $stmt->execute();
    }
    alert("Save successfully", "success");
    redirect("?id=" . $_GET['id'] . "");
}


?>


<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Customer Products <?= $customer['company'] ?></h4>
        </div>
        <div class="">



        </div>
    </div>
    <div class="card-body">

        <form method="POST">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product Name</th>
                        <th>Division</th>
                        <th>MRP</th>
                        <th>Sale Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT a.*,b.name as product,c.name as brand from customer_price a join products b on a.product_id=b.id join brand c on a.division_id=c.id where a.customer_id=?");
                    $stmt->bind_param('i', $_GET['id']);
                    $stmt->execute();
                    $customer_price = $stmt->get_result();
                    $sno = 1;
                    while ($row = $customer_price->fetch_assoc()) {
                        echo '<tr>
                        <td>' . $sno++ . '</td>
                        <td>' . $row['product'] . '</td>
                        <td>' . $row['brand'] . '</td>
                        <td>
                            <input type="number" name="update_price[' . $row['id'] . '][]" step="0.01" class="form-control" value="' . $row['price'] . '">
                        </td>
                    <td>
                            <input type="number" name="sale_price[' . $row['id'] . '][]" step="0.01" class="form-control" value="' . $row['sale_price'] . '">
                        </td>
                    
                        </tr>';
                    }


                    ?>

                </tbody>

            </table>

            <div class="mt-4 text-center">
                <button name="btnSave" type="submit" class="btn btn-primary">Submit</button>

            </div>
        </form>

    </div>

</div>






<?php include("../layouts/footer.php"); ?>