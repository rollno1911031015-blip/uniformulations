	<!-- Menubar -->
	<div class="menubar-area footer-fixed rounded-0 border-top">
		<div class="toolbar-inner menubar-nav">
			<a href="index" class="nav-link">
				<i class="icon feather icon-home"></i>
				<span>Home</span>
			</a>
			<a href="category" class="nav-link ">
				<i class="icon feather icon-grid"></i>
				<span>Categories</span>
			</a>
			<a href="cart" class="nav-link ">


				<i class="icon feather icon-shopping-cart"></i>


				<span style="display: flex; margin-left: 30%;"> Cart <span class="badge badge-primary" > <?= count($footer_cart_count) ?? 0 ?></span></span>

			</a>
			<a href="profile" class="nav-link">
				<i class="icon feather icon-user"></i>
				<span>Profile</span>
			</a>
		</div>
	</div>


	<!-- PWA Offcanvas End -->


	<!-- Multicolor Canvas Start -->
	<div class="offcanvas offcanvas-bottom m-3 rounded" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
		<div class="offcanvas-body small">
			<ul class="theme-color-settings">
				<li>
					<input class="filled-in" id="primary_color_1" name="theme_color" type="radio" value="color-primary">
					<label for="primary_color_1"></label>
					<span>Default</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_2" name="theme_color" type="radio" value="color-green">
					<label for="primary_color_2"></label>
					<span>Green</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_3" name="theme_color" type="radio" value="color-blue">
					<label for="primary_color_3"></label>
					<span>Blue</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_4" name="theme_color" type="radio" value="color-pink">
					<label for="primary_color_4"></label>
					<span>Pink</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_5" name="theme_color" type="radio" value="color-yellow">
					<label for="primary_color_5"></label>
					<span>Yellow</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_6" name="theme_color" type="radio" value="color-orange">
					<label for="primary_color_6"></label>
					<span>Orange</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_7" name="theme_color" type="radio" value="color-purple">
					<label for="primary_color_7"></label>
					<span>Purple</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_8" name="theme_color" type="radio" value="color-red">
					<label for="primary_color_8"></label>
					<span>Red</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_9" name="theme_color" type="radio" value="color-lightblue">
					<label for="primary_color_9"></label>
					<span>Lightblue</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_10" name="theme_color" type="radio" value="color-teal">
					<label for="primary_color_10"></label>
					<span>Teal</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_11" name="theme_color" type="radio" value="color-lime">
					<label for="primary_color_11"></label>
					<span>Lime</span>
				</li>
				<li>
					<input class="filled-in" id="primary_color_12" name="theme_color" type="radio" value="color-deeporange">
					<label for="primary_color_12"></label>
					<span>Deeporange</span>
				</li>
			</ul>
		</div>
	</div>
	<!-- Multicolor Canvas Start -->

	</div>
	<!--**********************************
    Scripts
***********************************-->
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/swiper-bundle.min.js"></script><!-- Swiper -->
	<script src="js/jquery.countdown.js"></script><!-- COUNTDOWN FUCTIONS  -->
	<script src="js/jquery.bootstrap-touchspin.min.js"></script><!-- Swiper -->
	<script src="js/dz.carousel.js"></script><!-- Swiper -->
	<script src="js/settings.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/index.js"></script>

	</body>

	</html>

	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<script>
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": false,
			"progressBar": true,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
	</script>
	<?php toaster(); ?>