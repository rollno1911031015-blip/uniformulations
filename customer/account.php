 <?php include("header.php");

    if (isset($_POST['logout'])) {

        $stmt = $mysqli->prepare("UPDATE `lead` set token='' WHERE  id=" . $user['id'] . "   ");
       // $stmt->bind_param("s", $_SESSION['customer_token']);
        $stmt->execute();
        $stmt->close();
        redirect("index");

    }

    if(isset($_POST['BtnUpdate'])){

        $stmt = $mysqli->prepare("UPDATE lead set name=?, mobile=?,email=?,gst=?,password=? WHERE  id=?   ");
        $stmt->bind_param("sssssi", $_POST['name'],$_POST['mobile'],$_POST['email'],$_POST['gst'],$_POST['password'],$user['id']);
         $stmt->execute();
         $stmt->close();
         redirect("?");

    }
    ?>



 <body class="shop-main-h">


     <div class="account-tab-area-start rts-section-gap">
         <div class="container-2">
             <div class="row">
                 <div class="col-lg-3">
                     <div class="nav accout-dashborard-nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                         <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false"><i class="fa-regular fa-bag-shopping"></i>Order</button>

                         <button class="nav-link" id="v-pills-settingsa-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settingsa" type="button" role="tab" aria-controls="v-pills-settingsa" aria-selected="false"><i class="fa-light fa-user"></i>Account Details</button>
                         <form method="POST">
                             <button class="btn btn-danger" type="submit" name="logout"><i class="fa-light fa-right-from-bracket"></i> Logout</button>
                         </form>
                     </div>
                 </div>
                 <div class="col-lg-9 pl--50 pl_md--10 pl_sm--10 pt_md--30 pt_sm--30">
                     <div class="tab-content" id="v-pills-tabContent">

                         <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
                             <div class="order-table-account">
                                 <div class="h2 title">Your Orders</div>
                                 <div class="table-responsive">
                                     <table class="table">
                                         <thead>
                                             <tr>
                                                 <th>Order</th>
                                                 <th>Date</th>
                                                 <th>Status</th>
                                                 <th>Mode of Transport</th>
                                                 <th>Actions</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <?php

                                                $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer FROM order_mst a JOIN lead b ON a.customer_id = b.id WHERE   a.user_type = 'customer' and a.order_status='Order' and a.customer_id=?;");


                                                $stmt->bind_param('i', $user['id']);
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_assoc()) {
                                                    echo '<tr>
                                                            <td>' . $row['id'] . '</td>
                                                            <td>' . $row['order_date'] . '</td>
                                                            <td>' . $row['status'] . '</td>
                                                            <td>' . $row['mode_of_transport'] . '</td>
                                                            <td><a href="view-invoice?id=' . $row['id'] . '"> View </a></td>
                                                        </tr>';
                                                }
                                                ?>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>



                         <div class="tab-pane fade" id="v-pills-settingsa" role="tabpanel" aria-labelledby="v-pills-settingsa-tab" tabindex="0">
                             <form method="POST" class="account-details-area">
                                 <h2 class="title">Account Details</h2>
                                 <div class="input-half-area">
                                     <div class="single-input">
                                         <input type="text" value="<?=$user['name']?>" name="name" placeholder="First Name">
                                     </div>
                                    
                                 </div>

                            
                                 <input type="email" name="email"  value="<?=$user['email']?>" placeholder="Email Address *" required>
                                 <input type="number" name="mobile"  value="<?=$user['mobile']?>" placeholder="mobile" required>
                                 <input type="gst" name="gst"  value="<?=$user['gst']?>" placeholder="gst " >
                                
                                 <input type="password" name="password" placeholder="Password *" value="<?=$user['password']?>" required>
                             
                                 <button class="rts-btn btn-primary" type="submit" name="BtnUpdate">Save Change</button>
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