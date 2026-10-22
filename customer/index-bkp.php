 <?php include("header.php");
    if (isset($_POST['BtnAdd'])) {


        $stmt = $mysqli->prepare("SELECT * from cart where customer_id=? and product_id=?");
        $stmt->bind_param("ii", $user['id'], $_POST['product_id']);
        $stmt->execute();
        $cart = $stmt->get_result()->fetch_assoc();


        if (!$cart) {
            $stmt = $mysqli->prepare("INSERT into cart (customer_id,product_id,qty) values (?,?,?) ;");
            $stmt->bind_param("iii", $user['id'], $_POST['product_id'], $_POST['qty']);
            $stmt->execute();
            $stmt->close();
        }
        alert("success", "Item added successfully.", "Item added successfully");
        redirect("?");
    }

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



    ?>
<style>
    .single-category-one{
        min-height: 80px !important;
        height: 0 !important;
    }
</style>
 <body class="shop-main-h">





     <div class="background-light-gray-color rts-section-gap bg_light-1 pt_sm--20" style="padding-top: 10px !important;">
         <!-- rts banner area start -->
         <div class="rts-banner-area-one mb--30">
             <div class="container">
                 <div class="row">
                     <div class="col-lg-12">
                         <div class="category-area-main-wrapper-one">
                             <div class="swiper mySwiper-category-1 swiper-data" data-swiper='{
                                "spaceBetween":1,
                                "slidesPerView":1,
                                "loop": true,
                                "speed": 2000,
                                "autoplay":{
                                    "delay":"4000"
                                },
                                "navigation":{
                                    "nextEl":".swiper-button-next",
                                    "prevEl":".swiper-button-prev"
                                },
                                "breakpoints":{
                                "0":{
                                    "slidesPerView":1,
                                    "spaceBetween": 0},
                                "320":{
                                    "slidesPerView":1,
                                    "spaceBetween":0},
                                "480":{
                                    "slidesPerView":1,
                                    "spaceBetween":0},
                                "640":{
                                    "slidesPerView":1,
                                    "spaceBetween":0},
                                "840":{
                                    "slidesPerView":1,
                                    "spaceBetween":0},
                                "1140":{
                                    "slidesPerView":1,
                                    "spaceBetween":0}
                                }
                            }'>

                                 <?php
                                    $stmt = $mysqli->prepare("SELECT * from sliders");

                                    $stmt->execute();
                                    $slider = $stmt->get_result();
                                    while ($row = $slider->fetch_assoc()) {
                                    ?>


                                     <div class="swiper-wrapper">
                                         <!-- single swiper start -->
                                         <div class="swiper-slide mt-0">
                                             <div class="60" style="">
                                                 <img src="../admin/<?= $row['image'] ?>" style="">
                                             </div>
                                         </div>

                                     </div>
                                 <?php
                                    }

                                    ?>

                                 <button class="swiper-button-next"><i class="fa-regular fa-arrow-right"></i></button>
                                 <button class="swiper-button-prev"><i class="fa-regular fa-arrow-left"></i></button>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <!-- rts banner area end -->
         <!-- rts category area satart -->
         <div class="rts-caregory-area-one ">
             <div class="container">
                <h5>Categories</h5>
                 <div class="row">

                     <?php

                        $stmt = $mysqli->prepare("SELECT a.*,b.name as category FROM brand a  join categories b on a.id=b.brand_id  where a.id in (" . $user['division'] . ")");
                        $stmt->execute();
                        $category = $stmt->get_result();
                        while ($row = $category->fetch_assoc()) {
                            $image = "";
                            if (empty($row['image'])) {
                                $image = "../public/logo.png";
                            } else {
                                $image = "../admin/" . $row['image'];
                            }

                            echo '   <div class="col-lg-1 col-md-6 col-sm-6 col-3"> <div class="">
                                         <a href="?search='.$row['category'].'" class="single-category-one"  >
                                           <i style="color:green" class="fa-solid fa-briefcase-medical"></i>
                                             <p style="font-size:12px;line-height:1;margin-top:5    px">' . $row['category'] . '</p>
                                         </a>
                                     </div>  </div>';
                        }
                        ?>
                 </div>
             </div>
         </div>
         <!-- rts category area end -->
     </div>




     <!-- best selling groceris -->
     <div class="weekly-best-selling-area rts-section-gap bg_light-1 pt-2">
         <div class="container">
             <div class="row">
                 <div class="col-lg-12">
                     <div class="title-area-between">
                         <h2 class="title-left">
                             Our Products
                         </h2>

                     </div>
                 </div>
             </div>
             <div class="row">
                 <div class="col-lg-12">
                     <div class="tab-content" id="myTabContent">
                         <!-- first tabs area start-->
                         <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                             <div class="row g-4">

                                 <?php

                                    if (empty($_GET['limit'])) {
                                        $_GET['limit'] = 48;
                                    }
                                    $recordsPerPage = $_GET['limit'];

                                    // Determine the current page
                                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                                        $currentPage = intval($_GET['page']);
                                    } else {
                                        $currentPage = 1;
                                    }


                                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                                    // Prepare the search query (if a search term is provided)
                                    $searchQuery = '';
                                    $searchParam = '';
                                    if (!empty($searchTerm)) {
                                        $searchQuery = " WHERE (a.name LIKE ? OR a.category LIKE ?) AND b.customer_id = ? AND b.division_id IN (" . $user['division'] . ")";
                                        $searchParam = ["%" . $searchTerm . "%", "%" . $searchTerm . "%", $user['id']];
                                    }
                                    
                                    $totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM products a JOIN customer_price b ON a.id = b.product_id $searchQuery");
                                    
                                    if (!empty($searchTerm)) {
                                        // Bind each parameter separately
                                        $totalRecordsQuery->bind_param("ssi", $searchParam[0], $searchParam[1], $searchParam[2]);
                                    }
                                    
                                    $totalRecordsQuery->execute();
                                    $totalRecordsResult = $totalRecordsQuery->get_result();
                                    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
                                    
                                  
 
                                    // Determine the total number of pages
                                    if (!empty($searchTerm)) {
                                        $searchQuery = " WHERE (a.name LIKE ? OR a.category LIKE ?) AND b.customer_id = ? AND b.division_id IN (" . $user['division'] . ")";
                                        $searchParam = ["%" . $searchTerm . "%", "%" . $searchTerm . "%", $user['id']];
                                    } else {
                                        $searchQuery = " WHERE b.customer_id = ? AND b.division_id IN (" . $user['division'] . ")";
                                        $searchParam = [$user['id']];
                                    }
                                    
                                    $stmt = $mysqli->prepare("SELECT a.*, b.sale_price AS price 
                                        FROM products a 
                                        JOIN customer_price b ON a.id = b.product_id 
                                        $searchQuery 
                                        ORDER BY a.id DESC 
                                        LIMIT ?, ?");
                                    
                                    // Bind parameters based on the query
                                    if (!empty($searchTerm)) {
                                        $stmt->bind_param("ssiii", $searchParam[0], $searchParam[1], $searchParam[2], $startFrom, $recordsPerPage);
                                    } else {
                                        $stmt->bind_param("iii", $searchParam[0], $startFrom, $recordsPerPage);
                                    }
                                    
                                    // Execute the query
                                    $stmt->execute();
                                    $products = $stmt->get_result();
                                    
                                    while ($row = $products->fetch_assoc()) {
                                        $image = "";
                                        if (empty($row['image'])) {
                                            $image = "../public/logo.png";
                                        } else {
                                            $image = "../admin/" . $row['image'];
                                        }
                                    ?>
                                     <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
                                         <form method="POST">
                                             <div class="single-shopping-card-one">
                                                 <!-- iamge and sction area start -->
                                                 <div class="image-and-action-area-wrapper">
                                                     <a href="/customer" class="thumbnail-preview">

                                                         <img src="<?= $image ?>" alt="grocery">
                                                     </a>

                                                 </div>
                                                 <!-- iamge and sction area start -->
                                                 <div class="body-content">

                                                     <a href="/customer">
                                                         <h4 class="title"><?= $row['name'] ?></h4>
                                                     </a>
                                                     <span class="availability"> <?= $row['category'] ?? "Not Found" ?></span>
                                                     <div class="price-area">
                                                         <span class="current"> <?= $row['price'] ?></span>


                                                         <input type="hidden" class="input" value="<?= $row['id'] ?>" name="product_id">
                                                     </div>

                                                     <?php
                                                        $found = false;
                                                        $qty = 0;
                                                        foreach ($cart as $item) {
                                                            if ($item['customer_id'] == $user['id'] && $item['product_id'] == $row['id']) {
                                                                $found = true;
                                                                $qty = $item['qty'];
                                                                break;
                                                            }
                                                        }
                                                        if ($found == true) {
                                                        ?>
                                                         <div class="cart-counter-action">
                                                             <div class="quantity-edit" style="width: 80%;">

                                                                 <input type="text" class="input" value="<?= $qty ?>" name="cart_qty">
                                                                 <div class="button-wrapper-action">
                                                                     <button class="button" type="submit" name="minusQty"><i class="fa-regular fa-chevron-down"></i></button>
                                                                     <button class="button plus" type="submit" name="addQty">+<i class="fa-regular fa-chevron-up"></i></button>
                                                                 </div>
                                                             </div>


                                                         </div>


                                                     <?php
                                                        } else {
                                                        ?>

                                                         <div class="cart-counter-action">
                                                             <div class="quantity-edit">

                                                                 <input type="text" class="input" value="1" name="qty">
                                                                 <div class="button-wrapper-action">
                                                                     <button class="button" type="button"><i class="fa-regular fa-chevron-down"></i></button>
                                                                     <button class="button plus" type="button">+<i class="fa-regular fa-chevron-up"></i></button>
                                                                 </div>
                                                             </div>
                                                             <button type="submit" name="BtnAdd" class="rts-btn btn-primary radious-sm with-icon">
                                                                 <div class="btn-text">
                                                                     Add
                                                                 </div>
                                                                 <div class="arrow-icon">
                                                                     <i class="fa-regular fa-cart-shopping"></i>
                                                                 </div>
                                                                 <div class="arrow-icon">
                                                                     <i class="fa-regular fa-cart-shopping"></i>
                                                                 </div>
                                                             </button>
                                                         </div>

                                                     <?php


                                                        }
                                                        ?>


                                                 </div>
                                             </div>
                                         </form>
                                     </div>
                                 <?php

                                    }

                                    ?>


                             </div>
                         </div>


                     </div>
                 </div>
                 <nav aria-label="...">
                     <ul class="pagination float-end">
                         <?php
                            $search = "";

                            if (!empty($_GET['search'])) {
                                $search = $_GET['search'];
                            }

                            $range = 2; // Number of pages before and after the current page to show

                            if ($totalPages > 1) {
                                for ($page = max(1, $currentPage - $range); $page <= min($totalPages, $currentPage + $range); $page++) {
                                    echo ($page == $currentPage) ? '<strong class="rts-btn btn-primary ">' . $page . '</strong> ' : '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '&search=' . $search . '" class="rts-btn btn-dark mx-2">' . $page . '</a> ';
                                }

                                // Add ellipses if there are more pages
                                if ($currentPage - $range > 1) {
                                    echo '... ';
                                }

                                // First page link
                                if ($currentPage - $range > 2) {
                                    echo '<a href="?page=1&limit=' . $_GET['limit'] . '&search=' . $search . '"  class="rts-btn btn  btn-dark">1</a> ';
                                }

                                // Last page link
                                if ($currentPage + $range < $totalPages - 1) {
                                    echo '... <a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '&search=' . $search . '" class="rts-btn btn-success">' . $totalPages . '</a>';
                                }
                            }


                            ?>



                     </ul>
                 </nav>
             </div>
         </div>
     </div>
     <!-- best selling groceris end -->



 </body>



 </html>

 <?php


    include("footer.php");


    ?>