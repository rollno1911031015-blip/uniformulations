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

<!-- Page Content Start -->
<div class="page-content space-top p-b65">
	<div class="container py-0 mb-5">
		<!-- Banner -->
		<div class="dz-banner">
			<div class="swiper banner-swiper">
				<div class="swiper-wrapper">
					<?php
					$stmt = $mysqli->prepare("SELECT * from sliders");

					$stmt->execute();
					$slider = $stmt->get_result();
					while ($row = $slider->fetch_assoc()) {
					?>
						<div class="swiper-slide">
							<div class="dz-media">
								<img src="../admin/<?= $row['image'] ?>" alt="">
							</div>
						</div>
					<?php
					}
					?>


				</div>
				<div class="swiper-pagination style-2"></div>
			</div>
		</div>
		<!-- Banner End -->

		<!-- Catagory Start -->
		<div class="category-area">

			<div class="title-bar">
				<h6 class="title mb-0">Category</h6>

			</div>

			<ul class="row g-3">




				<li class="category-item col-3">
					<a href="shop">
						<div class="media media-55">
							<i class="fa-solid fa-pump-medical" style="font-size: 25px; margin-left: 15px;" class="fa-solid fa-briefcase-medical"></i>
						</div>
						<span>Products</span>
					</a>
				</li>
				<li class="category-item col-3">
					<a href="#">
						<div class="media media-55">
							<i class="fa-solid fa-images" style="font-size: 25px; margin-left: 15px;" class="fa-solid fa-briefcase-medical"></i>
						</div>
						<span>Visual Ads</span>
					</a>
				</li>

				<li class="category-item col-3">
					<a href="shop?new_launch=1">
						<div class="media media-55">
							<i class="fa-solid fa-hospital-user" style="font-size: 25px; margin-left: 15px;" class="fa-solid fa-briefcase-medical"></i>
						</div>
						<span>New Launch</span>
					</a>
				</li>

				<li class="category-item col-3">
					<a href="order">
						<div class="media media-55">
							<i class="fa-solid fa-truck-fast" style="font-size: 25px; margin-left: 15px;" class="fa-solid fa-briefcase-medical"></i>
						</div>
						<span>Orders</span>
					</a>
				</li>

			</ul>
		</div>

		<div class="category-area">

			<div class="title-bar">
				<h6 class="title mb-0">Category</h6>
				<a href="category">View all <i class="icon feather icon-chevron-right"></i></a>
			</div>

			<ul class="row g-3">

				<?php

				$stmt = $mysqli->prepare("SELECT a.*,b.name as category,b.image FROM brand a  join categories b on a.id=b.brand_id  where a.id in (" . $user['select_division'] . ") limit 16");
				$stmt->execute();
				$category = $stmt->get_result();
				while ($row = $category->fetch_assoc()) {

					$img = "";
					if (!empty($row['image'])) {
						$img = '  <img src="../admin/' . $row['image'] . '">';
					} else {
						$img = '<i style="font-size: 20px; margin-left: 10px;" class="fa-solid fa-briefcase-medical"></i>';
					} ?>


					<li class="category-item col-3">
						<a href="shop?search=<?= $row['category'] ?>">
							<div class="media media-40">
								<?= $img ?>
							</div>
							<span><?= $row['category'] ?></span>
						</a>
					</li>
				<?php }
				?>

			</ul>
		</div>
		 <div class="row">
			<div class="col-12 mt-3"  style="height: 100px;" >
			<div class="dz-banner">
			<div class="swiper banner-swiper">
				<div class="swiper-wrapper">
					<?php
					$stmt = $mysqli->prepare("SELECT * from advertisement");

					$stmt->execute();
					$slider = $stmt->get_result();
					while ($row = $slider->fetch_assoc()) {
					?>
						<div class="swiper-slide">
							<div class="dz-media">
								<img src="../admin/<?= $row['image'] ?>" alt="">
							</div>
						</div>
					<?php
					}
					?>


				</div>
				<div class="swiper-pagination style-2"></div>
			</div>
		</div>

			</div>

		 </div>

	 
	</div>
	<!-- Page Content End -->

	<?php
	include("footer.php");
	?>