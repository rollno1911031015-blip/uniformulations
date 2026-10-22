 <?php include("header.php");
	if (isset($_POST['logout'])) {

		$stmt = $mysqli->prepare("UPDATE `lead` set token='' WHERE  id=" . $user['id'] . "   ");
		// $stmt->bind_param("s", $_SESSION['customer_token']);
		$stmt->execute();
		$stmt->close();
		redirect("index");
	}

	if (isset($_POST['BtnUpdate'])) {

		$stmt = $mysqli->prepare("UPDATE lead set name=?, mobile=?,email=?,gst=?,password=?,select_division=? WHERE  id=?   ");
		$stmt->bind_param("sssssii", $_POST['name'], $_POST['mobile'], $_POST['email'], $_POST['gst'], $_POST['password'],$_POST['division'], $user['id']);
		$stmt->execute();
		$stmt->close();
		redirect("?");
	}

	?>

 <header class="header header-fixed">
 	<div class="container">
 		<div class="header-content">
 			<div class="left-content">
 				<a href="javascript:void(0);" class="back-btn">
 					<i class="icon feather icon-chevron-left"></i>
 				</a>
 			</div>
 			<div class="mid-content">
 				<h6 class="title">Profile</h6>
 			</div>
 		 
 		</div>
 	</div>
 </header>
 <!-- Header -->

 <!-- Page Content Start -->
 <div class="page-content space-top">
 	<div class="container mb-5">
 		<div class="profile-area">
 			<div class="main-profile">
 				<div class="media media-60 me-3 rounded-circle">
 					<img src="images/user-profile.jpg" alt="profile-image">
 				</div>
 				<div class="profile-detail">
 					<h6 class="name"><?= $user['name'] ?></h6>
 					<span class="font-12"> <?= $user['mobile'] ?></span>
 				</div>
 				 
 			</div>
 			<div class="content-box">
 				<ul class="row g-2">
 					<li class="col-6">
 						<a href="order">
 							<div class="dz-icon-box">
 								<i class="icon feather icon-package"></i>
 							</div>
 							<span>Order</span>
 						</a>
 					</li>
 					<li class="col-6">
 						<a href="cart">
 							<div class="dz-icon-box">
 								<i class="icon feather icon-heart"></i>
 							</div>
 							<span>Cart</span>
 						</a>
 					</li>

 				</ul>
 			</div>
 			<div class="title-bar">
 				<h6 class="title mb-0">Account Settings</h6>
 			</div>
 			<div class="dz-list style-1">

 				<form method="POST" class="account-details-area">
 					<h2 class="title">Account Details</h2>
 					<div class="input-half-area">
 						<div class="single-input">
 							<input type="text" class="form-control" value="<?= $user['name'] ?>" name="name" placeholder="First Name">
 						</div>

 					</div>


 					<input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" placeholder="Email Address *" required>
 					<input type="number" name="mobile" class="form-control" value="<?= $user['mobile'] ?>" placeholder="mobile" required>
 					<input type="gst" name="gst" class="form-control" value="<?= $user['gst'] ?>" placeholder="gst ">

 					<input type="password" class="form-control" name="password" placeholder="Password *" value="<?= $user['password'] ?>" required>

					 <select name="division" id="division" class="form-control" required>
                                    <option value="">Select Division</option>
                                    <?php

                                    $stmt = $mysqli->prepare("SELECT * FROM brand WHERE  id in (".$user['division'].") ");
                                 //   $stmt->bind_param("s", $user['division']);
                                    $stmt->execute();
                                    $division = $stmt->get_result();
                                    while ($row = $division->fetch_assoc()) {
										$selected = ($row['id'] == $user['select_division']) ? 'selected' : '';
										echo '<option value="' . $row['id']. '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
									}
                                    ?>
                                </select>

 					<button class="btn btn-primary btn-sm mt-3" type="submit" name="BtnUpdate">Update</button>
 				</form>
 				<ul>

 					<li>
 						<a href="welcome.html" class="item-content item-link">
 							 
 							<div class="dz-inner">
 								<span class="title">
								 <form method="POST">
                             <button class="btn btn-danger" type="submit" name="logout"> <i class="icon feather icon-log-out"></i>  Logout</button>
                         </form>
								</span>
 							</div>
 						</a>
 					</li>
 				</ul>
 			</div>
 		</div>
 	</div>
 </div>

 <?php


	include("footer.php");


	?>