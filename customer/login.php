<?php
include("../config.php");
if (isset($_POST['BtnLogin'])) {



    $stmt = $mysqli->prepare("SELECT * FROM lead WHERE   mobile=? and password=? ");
    $stmt->bind_param("ss", $_POST['mobile'], $_POST['password']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    if (empty($user)) {
        alert('Incorrect mobile or password.', 'error', 'Login Failed');
        redirect("login");
    } else {
        $_SESSION['user'] = $user;
        $_SESSION['customer_token'] = generate_token(12);
        $_SESSION['last_login'] = $user['last_login'];
        setcookie('customer_token',  $_SESSION['customer_token'], time() + (30 * 86400), '/', '', true, true);
        $stmt = $mysqli->prepare("UPDATE `lead` set token=? WHERE  id=" . $user['id'] . "   ");
        $stmt->bind_param("s", $_SESSION['customer_token']);
        $stmt->execute();
        $stmt->close();
        redirect("login-division");
    }
}

if (isset($_COOKIE['customer_token'])) {
  
    $stmt = $mysqli->prepare("SELECT * FROM lead WHERE   token=? ");
    $stmt->bind_param("s", $_SESSION['customer_token']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($user) {
        redirect("login-division");
    }
}


?>

<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from html.themewant.com/ekomart/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 24 Sep 2024 10:21:01 GMT -->

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- plugins css -->
    <link rel="stylesheet preload" href="assets/css/plugins.css" as="style">
    <link rel="stylesheet preload" href="assets/css/style.css" as="style">

</head>

<body>



    <!-- rts register area start -->
    <div class="rts-register-area rts-section-gap bg_light-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="registration-wrapper-1">
                        <div class="logo-area mb--0">
                            <img class="mb--10" src="../public/logo.png" alt="logo">
                        </div>
                        <h3 class="title">
                            DM Pharma Global <br>Login Into Your Account</h3>
                        <form method="post" class="registration-form">
                            <div class="input-wrapper">
                                <label for="number">Number*</label>
                                <input type="number" id="number" name="mobile">
                            </div>
                            <div class="input-wrapper">
                                <label for="password">Password*</label>
                                <input type="password" id="password" name="password">
                            </div>
                            <button class="rts-btn btn-primary" type="submit" name="BtnLogin">Login Account</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- rts register area end -->












    <!-- plugins js -->
    <script defer src="assets/js/plugins.js"></script>

    <!-- custom js -->
    <script defer src="assets/js/main.js"></script>
    <!-- header style two End -->

</body>


<!-- Mirrored from html.themewant.com/ekomart/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 24 Sep 2024 10:21:01 GMT -->

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