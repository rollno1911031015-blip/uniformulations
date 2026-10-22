 <?php include("header.php");

    if (isset($_POST['addQty'])) {
        $qty = 1;
        $stmt = $mysqli->prepare("UPDATE  cart set qty=qty+? where  customer_id=? and product_id=? ;");
        $stmt->bind_param("iii", $qty, $user['id'], $_POST['product_id']);
        $stmt->execute();
        $stmt->close();
        redirect("?");
    }


    if (isset($_POST['minusQty'])) {

        $stmt = $mysqli->prepare("SELECT * from cart where customer_id=? and product_id=?");
        $stmt->bind_param("ii", $user['id'], $_POST['product_id']);
        $stmt->execute();
        $cart = $stmt->get_result()->fetch_assoc();


        $qty = $cart['qty'] - 1;
        if ($qty == 0) {
            $stmt = $mysqli->prepare("DELETE  from cart where  customer_id=? and product_id=? ;");
            $stmt->bind_param("ii",  $user['id'], $_POST['product_id']);
            $stmt->execute();
            $stmt->close();
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("UPDATE  cart set qty=qty-1 where  customer_id=? and product_id=? ;");
            $stmt->bind_param("ii",  $user['id'], $_POST['product_id']);
            $stmt->execute();
            $stmt->close();
            redirect("?");
        }
    }


    if (isset($_POST['btnProceed'])) {


        try {
            //code...
      

        $_POST['order_date']=date("Y-m-d");
        $_POST['delivery_date']=date("Y-m-d");
        $_POST['order_status']="order";
        $stmt = $mysqli->prepare("INSERT into order_mst(customer_id,order_date,delivery_date,order_status,user_type,status,user_id) values(?,?,?,?,'customer','pending for approval',1);");
        $stmt->bind_param("isss", $user['id'],    $_POST['order_date'], $_POST['delivery_date'],  $_POST['order_status']);
        $stmt->execute();
        $order_id = $mysqli->insert_id;
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,b.price FROM cart a join products b on a.product_id=b.id where a.customer_id=? ");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $cart = $stmt->get_result();



        while ($row = $cart->fetch_assoc()) {


            $stmt = $mysqli->prepare("INSERT into  order_det(mst_id,product_id,qty,price) values(?,?,?,?);");
                    $stmt->bind_param("iiid",  $order_id, $row['product_id'], $row['qty'], $row['price']);
                    $stmt->execute();
                    $stmt->close();

        }
        $stmt = $mysqli->prepare("DELETE from  cart where customer_id=? ;");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

    } catch (\Throwable $th) {
        echo $th->getMessage();
        die;
    }
        redirect("?");

    }

    if (isset($_POST['btnDelete'])) {
        $stmt = $mysqli->prepare("DELETE from  cart where id=? ;");
        $stmt->bind_param("i", $_POST['btnDelete']);
        $stmt->execute();
        redirect("?");
    }
    ?>

 <style>
     tr,
     th,
     td {
         border: solid 1px gray;
         padding: 20px !important;
     }
 </style>

 <body class="shop-main-h">


     <div class="rts-cart-area rts-section-gap bg_light-1">
         <div class="container">
             <div class="row g-5">
                 <div class="col-xl-9 col-lg-12 col-md-12 col-12 order-2 order-xl-1 order-lg-2 order-md-2 order-sm-2 p-4" style="background-color: white;">

                     <table class="table">
                         <thead>
                             <tr>
                                 <th>S.No</th>
                                 <th>Product</th>
                                 <th>Qty</th>
                                 <th>Price</th>
                                 <th>Action</th>
                             </tr>
                         </thead>
                         <tbody>

                             <?php
                                $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,b.price FROM cart a join products b on a.product_id=b.id where a.customer_id=? ");
                                $stmt->bind_param("i", $user['id']);
                                $stmt->execute();
                                $cart = $stmt->get_result();
                                $sno = 1;
                                $total = 0;
                                while ($row = $cart->fetch_assoc()) {
                                    $total += $row['qty'] * $row['price'];
                                ?>

                                 <tr>
                                     <td><?= $sno++ ?></td>
                                     <td><?= $row['product_name'] ?></td>
                                     <td><?= $row['qty'] ?></td>
                                     <td><?= $row['price'] ?></td>
                                     <td>
                                         <form method="POST">
                                             <button class="btn btn-danger btn-sm w-25" type="submit" name="btnDelete" value="<?= $row['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                         </form>
                                     </td>

                                 </tr>
                             <?php


                                }

                                ?>
                         </tbody>

                     </table>
                 </div>
                 <div class="col-xl-3 col-lg-12 col-md-12 col-12 order-1 order-xl-2 order-lg-1 order-md-1 order-sm-1">
                     <div class="cart-total-area-start-right">
                         <h5 class="title">Cart Totals</h5>


                         <div class="bottom">
                             <div class="wrapper">
                                 <span>Subtotal</span>
                                 <h6 class="price"><?= $total ?? 0 ?></h6>
                             </div>
                             <form method="POST">
                                 <div class="button-area">
                                     <button class="rts-btn btn-primary" type="submit" name="btnProceed">Proceed To Checkout</button>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>



 </body>



 </html>

 <?php


    include("footer.php");


    ?>