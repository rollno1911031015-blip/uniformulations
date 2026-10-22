<?php include("../config.php");


if (isset($_SESSION['customer_token'])) {
    $stmt = $mysqli->prepare("SELECT a.*,b.company_name as name FROM lead a join company b on a.company_id=b.id WHERE   token=? ");
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
if (isset($_POST['logout'])) {

	$stmt = $mysqli->prepare("UPDATE `lead` set token='' WHERE  id=" . $user['id'] . "   ");
   // $stmt->bind_param("s", $_SESSION['customer_token']);
	$stmt->execute();
	$stmt->close();
	redirect("index");

}
$footer_cart_count=$cart;
 
?>
 <!DOCTYPE html>
<html lang="en">

<head>

	<!-- Title -->
	<title>DM Pharma</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
	<meta name="theme-color" content="teal">
 
	<meta property="og:image" content="../public/logo.png">
	<meta name="format-detection" content="telephone=no">

	<!-- Favicons Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="../public/logo.png">

	<!-- PWA Version -->
	<link rel="manifest" href="manifest.json">

	<!-- Global CSS -->
	<link href="css/bootstrap-select.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/jquery.bootstrap-touchspin.min.css">
	<link rel="stylesheet" href="css/swiper-bundle.min.css">

	<!-- Stylesheets -->
	<link rel="stylesheet" class="main-css" type="text/css" href="css/style.css">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

</head>

<body>
	<div class="page-wrapper">

		<!-- Preloader -->
		
		<!-- Preloader end-->

		<!-- Header -->
		<header class="header header-fixed">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="menu-toggler">
							<i class="icon feather icon-menu"></i>
						</a>
						<h6 class="title font-14">Home</h6>
					</div>
					<div class="mid-content header-logo">
					</div>
					<div class="right-content dz-meta">
						<a href="shop" class="icon">
							<i class="icon feather icon-search"></i>
						</a>
						 
						<a href="cart" class="icon shopping-cart">
							<i class="icon feather icon-shopping-cart"></i>
							<span class="badge badge-primary"><?= count($cart) ?? 0 ?></span>
						</a>
					</div>
				</div>
			</div>
		</header>
		<!-- Header -->

		<!-- Sidebar -->
		<div class="dark-overlay"></div>
		<div class="sidebar">
			<div class="inner-sidebar">
				<a href="profile" class="author-box">
					<div class="dz-media">
						<img src="images/user-profile.jpg" alt="author-image">
					</div>
					<div class="dz-info">
						<h5 class="name"><?=$user['name']?></h5>
						<span><?=$user['mobile']?></span>
					</div>
				</a>
				<ul class="nav navbar-nav">
					<li>
						<a class="nav-link" href="index">
							<span class="dz-icon">
								<i class="icon feather icon-home"></i>
							</span>
							<span>Home</span>
						</a>
					</li>
					<li>
						<a class="nav-link" href="shop">
							<span class="dz-icon">
								<i class="icon feather icon-layers"></i>
							</span>
							<span>Products</span>
						</a>
					</li>
					<li>
						<a class="nav-link" href="category">
							<span class="dz-icon">
								<i class="icon feather icon-grid"></i>
							</span>
							<span>Category</span>
						</a>
					</li>
				 
				 
					 
					<li>
						<a class="nav-link" href="cart">
							<span class="dz-icon">
								<i class="icon feather icon-shopping-cart"></i>
							</span>
							<span>My Cart</span>
						</a>
					</li>
					<li>
						<a class="nav-link" href="order">
							<span class="dz-icon">
								<i class="icon feather icon-shopping-cart"></i>
							</span>
							<span>My Orders</span>
						</a>
					</li>
					<li>
						<a class="nav-link" href="profile">
							<span class="dz-icon">
								<i class="icon feather icon-user"></i>
							</span>
							<span>Profile</span>
						</a>
					</li>
					<li>
						<a class="nav-link" href="logout">
							
							<span>
							<form method="POST">
                             <button class="btn btn-danger btn-sm" type="submit" name="logout">  
								<i class="icon feather icon-log-out"></i>
							 Logout</button>
                         </form>
							</span>
						</a>
					</li>
				</ul>
				<div class="sidebar-bottom">
					<ul class="app-setting">
						 
						<li>
							<div class="mode">
								<span class="dz-icon">
									<i class="icon feather icon-moon"></i>
								</span>
								<span>Dark Mode</span>
								<div class="custom-switch">
									<input type="checkbox" class="switch-input theme-btn" id="toggle-dark-menu">
									<label class="custom-switch-label" for="toggle-dark-menu"></label>
								</div>
							</div>
						</li>
						 
					</ul>
					<div class="app-info">
						<h6 class="name">DM Pharma Store App</h6>
						<span class="ver-info">App Version 1.0</span>
					</div>
				</div>
			</div>
		</div>
		<!-- Sidebar End -->