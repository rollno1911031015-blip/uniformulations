<?php
include("../config.php");


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
if (isset($_POST['BtnLogin'])) {


    $stmt = $mysqli->prepare("UPDATE `lead` set select_division=? WHERE id=?   ");
    $stmt->bind_param("ii", $_POST['division'],$user['id']);
    $stmt->execute();
    $stmt->close();
    redirect("index");
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
                            DM Pharma Global <br>Select Your Division</h3>
                        <form method="post" class="registration-form">
                            <div class="input-wrapper">
                                <label for="number">Select Division</label> <br>
                                <select name="division" id="division" class="select w-100" required>
                                    <option value="">Select Division</option>
                                    <?php

                                    $stmt = $mysqli->prepare("SELECT * FROM brand WHERE  id in (".$user['division'].") ");
                                 //   $stmt->bind_param("s", $user['division']);
                                    $stmt->execute();
                                    $division = $stmt->get_result();
                                    while ($row=$division->fetch_assoc()) {
                                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <button class="rts-btn btn-primary mt-4" type="submit" name="BtnLogin" style="margin-top: 70px !important;">Save</button>

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