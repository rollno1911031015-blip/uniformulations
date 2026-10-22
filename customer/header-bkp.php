<?php include("../config.php");


if (isset($_SESSION['customer_token'])) {
    $stmt = $mysqli->prepare("SELECT * FROM lead WHERE   token=? ");
    $stmt->bind_param("s", $_SESSION['customer_token']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$user) {
        session_unset();
        session_destroy();
        redirect("login");
    }
}

if (empty($_SESSION['customer_token'])) {
    session_unset();
    session_destroy();
    redirect("login");
}
$stmt = $mysqli->prepare("SELECT * FROM cart WHERE   customer_id=? ");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$cart = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

 
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from html.themewant.com/ekomart/index by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 24 Sep 2024 10:16:49 GMT -->

<head>
    <meta charset="UTF-8">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">




    <link rel="stylesheet preload" href="assets/css/plugins.css" as="style">
    <link rel="stylesheet preload" href="assets/css/style.css" as="style">

</head>

<body class="shop-main-h">



    <div class="search-header-area-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="logo-search-category-wrapper">
                        <a href="index" class="logo-area">
                            <img src="../public/logo.png" width="100px" alt="logo-main" class="logo">
                        </a>
                        <div class="category-search-wrapper">

                            <form method="GET" class="search-header" action="index">

                                <input type="text" placeholder="Search for products, categories or brands" name="search"
                                    value="<?php if (!empty($_GET['search'])) echo $_GET['search'] ?>">
                                <button type="submit" class="rts-btn btn-primary radious-sm with-icon">
                                    <div class="btn-text">
                                        Search
                                    </div>
                                    <div class="arrow-icon">
                                        <i class="fa-light fa-magnifying-glass"></i>
                                    </div>
                                    <div class="arrow-icon">
                                        <i class="fa-light fa-magnifying-glass"></i>
                                    </div>
                                </button>
                            </form>
                        </div>
                        <div class="actions-area">
                            <div class="search-btn" id="searchs">

                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="#1F1F25"></path>
                                </svg>

                            </div>
                            <div class="menu-btn" id="menu-btn">

                                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect y="14" width="20" height="2" fill="#1F1F25"></rect>
                                    <rect y="7" width="20" height="2" fill="#1F1F25"></rect>
                                    <rect width="20" height="2" fill="#1F1F25"></rect>
                                </svg>

                            </div>
                        </div>
                        <div class="accont-wishlist-cart-area-header">
                            <a href="account" class="btn-border-only account">
                                <i class="fa-light fa-user"></i>
                                <span>Account</span>
                            </a>

                            <div class="btn-border-only cart category-hover-header">
                                <i class="fa-sharp fa-regular fa-cart-shopping"></i>
                                <span class="text">My Cart</span>
                                <span class="number"><?= count($cart) ?? 0 ?></span>

                                <a href="cart" class="over_link"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="rts-header-nav-area-one header--sticky">
        <div class="container">
            <div class="row">

                <div class="col-lg-12">
                    <div class="logo-search-category-wrapper after-md-device-header">
                        <a href="index" class="logo-area">
                            <img src="../public/logo.png" alt="logo-main" class="logo" width="75px">
                        </a>

                        <div class="main-wrapper-action-2 d-flex">
                            <div class="accont-wishlist-cart-area-header">
                            <div class="btn-border-only cart category-hover-header">
                                    <i class="fa-sharp fa-regular fa-user"></i>
                                    <span class="text">Account</span>

                                    <a href="account" class="over_link"></a>
                                </div>

                                <div class="btn-border-only cart category-hover-header">
                                    <i class="fa-sharp fa-regular fa-cart-shopping"></i>
                                    <span class="text">My Cart</span>

                                    <a href="cart" class="over_link"></a>
                                </div>
                            </div>
                            <div class="actions-area">
                                <div class="search-btn" id="search">

                                    <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="#1F1F25"></path>
                                    </svg>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</body>



</html>