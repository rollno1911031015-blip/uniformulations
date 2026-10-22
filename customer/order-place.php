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


            $_POST['order_date'] = date("Y-m-d");
            $_POST['delivery_date'] = date("Y-m-d");
            $_POST['order_status'] = "order";
            $stmt = $mysqli->prepare("INSERT into order_mst(customer_id,order_date,delivery_date,order_status,user_type,status,user_id) values(?,?,?,?,'customer','pending for approval',1);");
            $stmt->bind_param("isss", $user['id'],    $_POST['order_date'], $_POST['delivery_date'],  $_POST['order_status']);
            $stmt->execute();
            $order_id = $mysqli->insert_id;
            $stmt->close();

            $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,c.sale_price as price FROM cart a join products b on a.product_id=b.id JOIN customer_price c ON a.customer_id = c.customer_id and a.product_id=c.product_id where a.customer_id=? ");
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
        alert("Thank you for shopping","success","success");

        redirect("view-invoice?id=".  $order_id ."");
    }

    if (isset($_POST['btnDelete'])) {
        $stmt = $mysqli->prepare("DELETE from  cart where id=? ;");
        $stmt->bind_param("i", $_POST['btnDelete']);
        $stmt->execute();
        redirect("?");
    }
    ?>

 <div class="page-wrapper">



     <!-- Header -->
     <header class="header header-fixed">
         <div class="container">
             <div class="header-content">
                 <div class="left-content">
                     <a href="javascript:void(0);" class="back-btn">
                         <i class="icon feather icon-chevron-left"></i>
                     </a>
                 </div>
                 <div class="mid-content">
                     <h6 class="title">Cart Summary</h6>
                 </div>
                 <div class="right-content">
                     <a href="javascript:void(0);">
                         <i class="icon feather icon-more-vertical-"></i>
                     </a>
                 </div>
             </div>
         </div>
     </header>
     <!-- Header -->

     <!-- Page Content Start -->
     <div class="page-content space-top p-b50">
         <div class="container mb-5">
             <!-- <div class="user-status m-b15">
                 <div class="d-flex align-items-center">
                     <div class="media media-35 rounded-circle me-2">
                         <img src="assets/images/user.png" alt="">
                     </div>
                     <h6 class="mb-0 font-14 font-w400">Delivery to Tushar</h6>
                 </div>
                 <h6 class="mb-0 font-14 font-w500 text-primary">
                     <a href="javascript:void(0);" class="d-flex align-items-center">Ram krishan, puram
                         <i class="icon feather icon-chevron-down font-16"></i>
                     </a>
                 </h6>
             </div> -->

             <?php
                $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,c.sale_price as price,b.category FROM cart a join products b on a.product_id=b.id JOIN customer_price c ON a.customer_id = c.customer_id and a.product_id=c.product_id where a.customer_id=? ");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $cart = $stmt->get_result();
                $sno = 1;
                $total = 0;
                while ($row = $cart->fetch_assoc()) {
                    $image = "";
                    if (empty($row['image'])) {
                        $image = "../public/logo.png";
                    } else {
                        $image = "../admin/" . $row['image'];
                    }
                    $total += $row['qty'] * $row['price'];
                ?>

                 <div class="cart-box">
                     <div class="dz-media">
                         <img src="<?= $image ?>" alt="">
                     </div>
                     <div class="cart-content">
                         <h6 class="title mb-1"><a href=""><?= $row['product_name'] ?></a> </h6>
                         <span class="font-12 brand-tag"><?= $row['category'] ?></span> <br>
                         <span class="font-12 brand-tag"><?= $row['qty'] ?></span>
                         <div class="cart-footer">

                             <h6 class="price mb-0"><?= $row['price'] ?></h6>
                             <div class="dz-stepper style-2">
                                
                             </div>
                         </div>
                     </div>
                 </div>

             <?php } ?>

             <div class="view-cart mb-2">
                 <ul>
                     <li>
                         <span class="name">Price :</span>
                         <span class="text-secondary font-w500"><?= $total ?? 0 ?></span>
                     </li>


                     <li class="divider divider-dashed mt-0 pb-0"></li>
                     <li>
                         <span class="name">Total :</span>
                         <h4 class="price"><?= $total ?? 0 ?></h4>
                     </li>
                 </ul>
                 <form method="POST" class="mt-3">
                     <div class="button-area">
                         <button class="btn btn-primary w-100" type="submit" name="btnProceed">Place Order</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>

 </div>

 <?php


    include("footer.php");


    ?>