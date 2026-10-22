<?php
include("config.php");
if (isset($_POST['BtnLogin'])) {
    $stmt = $mysqli->prepare("SELECT * FROM `lead` WHERE   mobile=? and password=? ");
    $stmt->bind_param("ss", $_POST['mobile'], $_POST['password']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (empty($user)) {
        alert('Incorrect Mobile or password.', 'error', 'Login Failed');
        redirect("customer-login");
    } else {
        $_SESSION['user'] = $user;
        $_SESSION['token'] = generate_token(12);
  

        $stmt = $mysqli->prepare("UPDATE `lead` set token=? WHERE  id=" . $user['id'] . "   ");
        $stmt->bind_param("s", $_SESSION['token']);
        $stmt->execute();
        $stmt->close();
        redirect("customer/index");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title>Login - Pro inventory</title>

    <link rel="shortcut icon" type="image/x-icon" href="public/favicon.png">

    <link rel="stylesheet" href="public/css/bootstrap.min.css">

    <link rel="stylesheet" href="public/css/fontawesome.min.css">
    <link rel="stylesheet" href="public/css/all.min.css">

    <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper bg-img">
                <div class="login-content">
                    <form method="POST">
                        <div class="login-userset">
                            <div class="login-logo logo-normal">
                                <img src="public/logo.png" alt="img">
                            </div>
                            <a href="index" class="login-logo logo-white">
                                <img src="public/logo.png" alt="">
                            </a>
                            <div class="login-userheading">
                                <h3>Sign In Customer</h3>
                                <h4>Access the Inventory panel using your Mobile and password.</h4>
                            </div>
                            <div class="form-login mb-3">
                                <label class="form-label">Mobile </label>
                                <div class="form-addons">
                                    <input type="number" onkeydown="if(this.value.length==10) return false" class="form-control" name="mobile" placeholder="Enter Mobile" required >
                                    <img src="public/images/mail.svg" alt="img">
                                </div>
                            </div>
                            <div class="form-login mb-3">
                                <label class="form-label">Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control" name="password" placeholder="Enter Password" required>
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>

                            <div class="form-login">
                                <button type="submit" class="btn btn-login" name="BtnLogin">Sign In</button>
                            </div>


                            <div class="form-sociallink">

                                <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                                    <p>Copyright © <?= date('Y') ?> Clikzop Innovations. All rights reserved</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="customizer-links" id="setdata">
        <ul class="sticky-sidebar">
            <li class="sidebar-icons">
                <a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="Theme">
                    <i data-feather="settings" class="feather-five"></i>
                </a>
            </li>
        </ul>
    </div>

    <script src="public/js/jquery-3.7.1.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>

    <script src="public/js/feather.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>

    <script src="public/js/bootstrap.bundle.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="public/js/theme-script.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="public/js/script.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="public/js/rocket-loader.min.js" data-cf-settings="c5db94e3416bf781b99349f4-|49" defer=""></script>
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
<?php
toaster();
?>