<?php
include("header.php");
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
<form method="GET" class="search-header" action="shop">
    <header class="header header-fixed">
        <div class="container">
            <div class="header-content">
                <div class="input-group search-input">
                    <a href="javascript:void(0);" class="search-icon">

                    </a>
                    <input type="search" placeholder="Search here..." value="<?php if (!empty($_GET['search'])) echo $_GET['search'] ?>" name="search" class="form-control main-in">
                    <button type="submit" class="btn btn-secondary"> <i class="icon feather icon-search"></i></button>
                </div>
            </div>
        </div>
    </header>
</form>
<div class="page-content space-top p-b65">
    <div class="container py-0 mb-5">
        <div class="top-selection">
            <div class="title-bar mt-0">
                <h6 class="title text-white">Products</h6>
            </div>
            <div class="row g-3">



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
                $new_launch = isset($_GET['new_launch']) ? $_GET['new_launch'] : 0;

                $searchQuery = " WHERE b.customer_id = ? AND b.division_id IN (" . $user['select_division'] . ")";
                $params = [$user['id']];

                // Add search term condition
                if (!empty($searchTerm)) {
                    $searchQuery .= " AND (a.name LIKE ? OR a.category LIKE ? OR a.composition LIKE ?)";
                    $params[] = "%" . $searchTerm . "%";
                    $params[] = "%" . $searchTerm . "%";
                    $params[] = "%" . $searchTerm . "%";
                }

                // Add new_launch condition
                if (!empty($new_launch)) {
                    $searchQuery .= " AND a.new_launch = ?";
                    $params[] = $new_launch;
                }

                // Prepare and execute the query
                $query = "SELECT COUNT(*) AS total FROM products a 
                          JOIN customer_price b ON a.id = b.product_id" . $searchQuery;

                $totalRecordsQuery = $mysqli->prepare($query);
                $types = str_repeat('s', count($params) - 1) . 'i'; // Adjust types dynamically (s for string, i for integer)
                $totalRecordsQuery->bind_param($types, ...$params);

                $totalRecordsQuery->execute();
                $totalRecordsResult = $totalRecordsQuery->get_result();
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];




                // Prepare the search query and parameters
                if (!empty($searchTerm)) {
                    $searchQuery = " WHERE (a.name LIKE ? OR a.category LIKE ? or a.composition LIKE ?) AND b.customer_id = ? AND b.division_id IN (" . $user['select_division'] . ") AND a.new_launch = ?";
                    $searchParam = ["%" . $searchTerm . "%", "%" . $searchTerm . "%", "%" . $searchTerm . "%", $user['id'], $new_launch];
                } else {
                    $searchQuery = " WHERE b.customer_id = ? AND b.division_id IN (" . $user['select_division'] . ") AND a.new_launch = ?";
                    $searchParam = [$user['id'], $new_launch];
                }

                $stmt = $mysqli->prepare("SELECT a.*, b.sale_price AS price FROM products a 
                                          JOIN customer_price b ON a.id = b.product_id 
                                          $searchQuery 
                                          ORDER BY a.id DESC 
                                          LIMIT ?, ?");

                // Bind parameters based on the query
                if (!empty($searchTerm)) {
                    $stmt->bind_param("sssiiii", $searchParam[0], $searchParam[1], $searchParam[2], $searchParam[3], $searchParam[4], $startFrom, $recordsPerPage);
                } else {
                    $stmt->bind_param("iiii", $searchParam[0], $searchParam[1], $startFrom, $recordsPerPage);
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

                    <div class="col-6">
                        <form method="POST">
                            <div class="shop-card">
                                <div class="dz-media">
                                    <a href="">
                                        <img src="<?= $image ?>" alt="image">
                                    </a>
                                </div>
                                <div class="dz-content">
                                    <h6 class="title"><a href=""><?= $row['category'] ?? "Not Found" ?></a></h6>
                                    <h6 class="title"><a href=""><?= $row['name'] ?></a></h6>
                                    <?php
                                    $composition = $row['composition'];
                                    $maxLength = 15; // Maximum length before truncation
                                    $isTruncated = strlen($composition) > $maxLength;
                                    $shortenedComposition = $isTruncated ? substr($composition, 0, $maxLength) . '...' : $composition;
                                    ?>

                                    <span class="composition">
                                        <span class="short-text"><?= htmlspecialchars($shortenedComposition) ?></span>
                                        <span class="full-text" style="display: none;"><?= htmlspecialchars($composition) ?></span>
                                        <?php if ($isTruncated): ?>
                                            <a href="javascript:void(0);" class="toggle-text">See more</a>
                                        <?php endif; ?>
                                    </span> <br>
                                    <span class="offer"><?= $row['price'] ?></span>
                                </div>
                            </div>
                            <input type="hidden" class="input" value="<?= $row['id'] ?>" name="product_id">
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
                                    <div class="quantity-edit">


                                        <div class="button-wrapper-action d-flex">
                                            <button class="button btn btn-primary  btn-sm" type="submit" name="minusQty">- </button>
                                            <input type="text" class="input form-control" value="<?= $qty ?>" name="cart_qty" style="padding: 0px 20px;height: auto;">
                                            <button class="button plus btn btn-primary   btn-sm" type="submit" name="addQty">+</button>
                                        </div>
                                    </div>


                                </div>


                            <?php
                            } else {
                            ?>

                                <div class="cart-counter-action">
                                    <div class="quantity-edit">


                                        <div class="d-flex mt-1 ">

                                            <input type="text" class="input form-control" value="1" name="qty" style="padding: 0px 20px;height: auto;">
                                            <button type="submit" name="BtnAdd" class="btn btn-primary btn-sm">
                                                Add

                                            </button>

                                        </div>
                                    </div>

                                </div>

                            <?php


                            }
                            ?>
                        </form>
                    </div>

                <?php

                }

                ?>

            </div>
            <!-- Top Selection End -->
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


<?php
include("footer.php");
?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-text').forEach(link => {
            link.addEventListener('click', (e) => {
                const parent = e.target.closest('.composition');
                const shortText = parent.querySelector('.short-text');
                const fullText = parent.querySelector('.full-text');

                if (fullText.style.display === 'none') {
                    shortText.style.display = 'none';
                    fullText.style.display = 'inline';
                    e.target.textContent = 'See less';
                } else {
                    shortText.style.display = 'inline';
                    fullText.style.display = 'none';
                    e.target.textContent = 'See more';
                }
            });
        });
    });
</script>