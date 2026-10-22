 <?php include("header.php");

    ?>

 <header class="header header-fixed">
     <div class="container">
         <div class="header-content">
             <div class="left-content">
                 <a href="javascript:void(0);" class="back-btn">
                     <i class="icon feather icon-chevron-left"></i>
                 </a>
                 <h6 class="title">Orders</h6>
             </div>
             <div class="mid-content">
             </div>
            
         </div>
     </div>
 </header>
 <!-- Header -->


 <!-- Page Content Start -->
 <div class="page-content space-top">
 	<div class="container py-0 mb-5">
         <div class="row">
             <?php

                $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer FROM order_mst a JOIN lead b ON a.customer_id = b.id WHERE   a.user_type = 'customer' and a.order_status='Order' and a.customer_id=?;");


                $stmt->bind_param('i', $user['id']);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) { ?>

                 <div class="col-12">
                     <div class="dz-order">
                         <div class="order-info">
                             <div class="pe-3">
                                 <span class="productId">#<?= $row['id'] ?></span>
                                 <h6 class="title"><a href=" "><?= $row['order_date'] ?></a></h6>

                             </div>
                             <div class="media media-70">
                                 <a class="btn btn-outline-primary" href="view-invoice?id=<?= $row['id'] ?>"> View </a>
                             </div>
                         </div>
                         <div class="order-detail">
                             <span> <?= $row['mode_of_transport'] ?></span>


                         </div>
                         <div class="status">
                             <a href="javascript:void(0);" class="badge badge-lg badge-outline-secondary me-4">
                                 <i class="fa-solid fa-circle"></i>
                                 <span><?= $row['status'] ?></span>
                             </a>

                         </div>
                     </div>
                 </div>

             <?php } ?>

         </div>
     </div>
 </div>
 <?php


    include("footer.php");

    ?>