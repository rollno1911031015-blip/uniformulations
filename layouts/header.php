<?php
include("../config.php");
if (!empty($_SESSION['token'])) {
    $stmt = $mysqli->prepare("SELECT * FROM admin_users   WHERE   token=?  ");
    $stmt->bind_param("s", $_SESSION['token']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    function GetIds($userId, $mysqli)
    {

        $child_ids = [];
        $iterable = [];
        $child_id = "";
        array_push($iterable, $userId);

        while (sizeOf($iterable) > 0) {
            $iterable = implode(', ', $iterable);
            array_push($child_ids, $iterable);

            try {
                $stmt = $mysqli->prepare("SELECT id from admin_users where parent_id in (" . $iterable . ")");
                $stmt->execute();
                $result = $stmt->get_result();
                $iterable = [];
                while ($row = $result->fetch_assoc())
                    array_push($iterable, $row['id']);
            } catch (\Throwable $th) {
                $error = true;
                $msg = $th->getMessage();
            }
        }
        $child_id = implode(', ', $child_ids);
        return $child_id;
    }
    $userIds = GetIds($user['id'], $mysqli);


    if (!$user) {
        redirect("../index");
    }
} else {
    redirect("../index");
}



$start_date = new DateTime('first day of this month');

// Get the last day of the current month
$end_date = new DateTime('last day of this month');

// Format the dates as needed (e.g., 'Y-m-d' for '2024-07-01' and '2024-07-31')
$start_date = $start_date->format('Y-m-d');
$end_date = $end_date->format('Y-m-d');



function permissions($id, $mysqli, $user) 
{

    $stmt = $mysqli->prepare("SELECT * FROM permission   WHERE   permission_id=? and role_id=?  ");
    $stmt->bind_param("ii", $id, $user['role']);
    $stmt->execute();
    $permission = $stmt->get_result()->fetch_assoc();

    return $permission;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title> Inventory</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../public/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="../public/css/bootstrap-datetimepicker.min.css">

    <!-- animation CSS -->
    <link rel="stylesheet" href="../public/css/animate.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="../public/css/select2.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="../public/css/fontawesome.min.css">
    <link rel="stylesheet" href="../public/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/dataTables/datatables.min.css">

</head>

<body>
    <!-- <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div> -->
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">

            <!-- Logo -->
            <div class="header-left active">
                <a href="index" class="logo logo-normal">
                    <img src="../public/logo.png" alt="">
                </a>
                <a href="index" class="logo logo-white">
                    <img src="../public/favicon.png" alt="">
                </a>
                <a href="index" class="logo-small">
                    <img src="../public/favicon.png" alt="">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                    <i data-feather="chevrons-left" class="feather-16"></i>
                </a>
            </div>
            <!-- /Logo -->

            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <!-- Header Menu -->
            <ul class="nav user-menu">

                <!-- Search -->
                <li class="nav-item nav-searchinputs">

                </li>
                <!-- /Search -->



                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                        <span class="user-info">

                            <i class="fa fa-user-circle" style="font-size: 40px; margin-right: 10px;"></i>
                            <span class="user-detail">
                                <span class="user-name"><?= $user['name'] ?></span>
                                <span class="user-role"><?= $user['user_type'] ?></span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">
                            <div class="profileset">
                                <!-- <span class="user-img"><img src="../public/images/avator1.jpg" alt=""> -->
                                <i class="fa fa-user-circle" style="font-size: 28px;"></i>
                                <span class="status online"></span></span>
                                <div class="profilesets">
                                    <h6><?= $user['name'] ?></h6>
                                    <h5><?= $user['user_type'] ?></h5>
                                </div>
                            </div>
                            <hr class="m-0">
                            <?php
                            if ($_SESSION['user']['user_type'] == 'admin') {
                            ?>
                                <a class="dropdown-item" href="profile"> <i class="me-2" data-feather="user"></i> My Profile</a>
                            <?php } ?>
                            <a class="dropdown-item logout pb-0" href="../logout"><img src="../public/images/log-out.svg" class="me-2" alt="img">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
            <!-- /Header Menu -->

            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.html">My Profile</a>
                    <a class="dropdown-item" href="">Settings</a>
                    <a class="dropdown-item" href="signin.html">Logout</a>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>

                        <li class="submenu-open">

                            <ul>
                                <li><a href="index"><i data-feather="home"></i><span>Dashboard</span></a></li>
                            </ul>
                        </li>

                        <?php
                        $per = permissions(1, $mysqli, $user);
                        if ($per['view'] == 1) {
                        ?>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">User Management</h6>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Master</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="designation"><i data-feather="user"></i><span>Designation</span></a></li>
                                            <li><a href="user-role"><i data-feather="user"></i><span>User Role</span></a></li>

                                        </ul>
                                    </li>

                                    <li><a href="users"><i data-feather="user"></i><span>Users</span></a></li>

                                </ul>
                            </li>
                        <?php
                        }

                        ?>

                        <?php
                        $per = permissions(2, $mysqli, $user);
                        if ($per['view'] == 1) {
                        ?>

                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Master</h6>
                                <ul>

                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Master's</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>
                                            <li><a href="company">Company</a></li>
                                            <li><a href="vendor">Vendor</a></li>
                                            <li><a href="store-location">Store Location</a></li>
                                            <li><a href="mode-of-transport">Transport Mode</a></li>

                                            <li><a href="email-templates">Email Templates</a></li>
                                            <li><a href="sliders">Sliders</a></li>
                                            <li><a href="advertisement">Advertisement</a></li>
                                            <li><a href="days">Days</a></li>
                                            <li><a href="rating">Rating</a></li>



                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        <?php
                        }
                        ?>
                        <li class="submenu-open">

                            <ul>

                                <li class="submenu">
                                    <a href="javascript:void(0);"><i data-feather="file-minus"></i><span>Purchase Management</span><span class="menu-arrow"></span></a>
                                    <ul>
                                        <?php
                                        $per = permissions(3, $mysqli, $user);
                                        if ($per['view'] == 1) {
                                        ?>

                                            <li class="submenu submenu-two"><a href="javascript:void(0);">Product Master<span class="menu-arrow inside-submenu"></span></a>
                                                <ul>
                                                    <li><a href="unit-type">Unit Type</a></li>
                                                    <li><a href="brand">Division</a></li>
                                                    <li><a href="category">Category</a></li>
                                                    <li><a href="pack">Pack</a></li>
                                                    <li><a href="products">Product</a></li>

                                                </ul>
                                            </li>
                                        <?php } ?>
                                        <?php
                                        $per = permissions(4, $mysqli, $user);
                                        if ($per['view'] == 1) {
                                        ?>
                                            <li class="submenu">
                                                <a href="javascript:void(0);">Purchase Order<span class="menu-arrow inside-submenu"></span></a>
                                                <ul>
                                                    <li><a href="requirement">Requirement</a></li>
                                                    <li><a href="requirement-list">Requirement List</a></li>
                                                    <li><a href="generated-po?status=Generate PO">Purchase Order</a></li>
                                                    <li><a href="inward-stock-report?loc_id=1&fromDt=<?= $start_date ?>&toDt=<?= $end_date ?>">Inward Stock</a></li>
                                                    <li><a href="purchase-return?fromDt=<?= $start_date ?>&toDt=<?= $end_date ?>">Purchase Return</a></li>





                                                </ul>
                                            </li>
                                        <?php } ?>
                                        <?php
                                        $per = permissions(5, $mysqli, $user);
                                        if ($per['view'] == 1) {
                                        ?>
                                            <li><a href=""><span>Reports</span></a></li>
                                        <?php } ?>

                                    </ul>
                                </li>
                            </ul>
                        </li>




                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Lead Management</h6>
                            <ul>

                                <?php
                                $per = permissions(6, $mysqli, $user);
                                if ($per['view'] == 1) {
                                ?>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Master's</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="source">Source</a></li>
                                            <li><a href="status">Status</a></li>



                                        </ul>
                                    </li>

                                <?php } ?>
                                <?php
                                $per = permissions(7, $mysqli, $user);
                                if ($per['view'] == 1) {
                                ?>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Lead Management</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <?php

                                            $stmt = $mysqli->prepare("SELECT * from status");
                                            // $stmt->bind_param("i", $_POST['id']);
                                            $stmt->execute();
                                            $company = $stmt->get_result();
                                            $no = 1;
                                            while ($row = $company->fetch_assoc()) {
                                                echo ' <li><a href="lead?status=' . $row['id'] . '">' . $row['name'] . '</a></li>';
                                            }
                                            ?>




                                        </ul>
                                    </li>

                                <?php } ?>
                                <?php
                                $per = permissions(8, $mysqli, $user);
                                if ($per['view'] == 1) {
                                ?>
                                    <li><a href="customers"><i data-feather="users"></i><span>Customer's</span></a></li>
                                    <li><a href="ledger"><i data-feather="users"></i><span>Ledger</span></a></li>
                                <?php } ?>
                            </ul>

                        </li>

                        <?php
                        $per = permissions(9, $mysqli, $user);
                        if ($per['view'] == 1) {
                        ?>

                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Order Master</h6>


                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Sale Management</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>
                                            <?php
                                            if ($per['edit'] == 1) {
                                                echo '  <li><a href="generate-order">Generate Order</a></li>';
                                            }
                                            ?>

                                            <li><a href="orders?status=pending for approval">Customer Orders</a></li>
                                            <li><a href="pi?status=processing">Quote</a></li>
                                            <li><a href="orders?status=processing">Processing Order</a></li>
                                            <li><a href="outward-stock-report?status=pending&loc_id=1&fromDt=<?= $start_date ?>&toDt=<?= $end_date ?>">Outward Challan</a></li>
                                            <li><a href="orders?status=delivered">Delivered Order</a></li>
                                            <li><a href="orders?status=pending">Pending Order</a></li>
                                            <li><a href="orders?status=cancelled">Cancelled Order</a></li>
                                            <li><a href="sale-return?fromDt=<?= $start_date ?>&toDt=<?= $end_date ?>">Sale Return</a></li>

                                            <li><a href="">Reports</a></li>





                                        </ul>
                                    </li>
                                    <li><a href="direct-sale"><i data-feather="layers"></i><span>Direct Sale</span></a></li>
                                    <li><a href="sale-due-date-report"><i data-feather="layers"></i><span>Sale Due Date</span></a></li>
                                </ul>
                            </li>
                        <?php } ?>


                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Reports</h6>
                            <ul>
                                <?php
                                $per = permissions(10, $mysqli, $user);
                                if ($per['view'] == 1) {
                                ?>

                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Stock Reports</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="current-stock">Current Stock</a></li>
                                            <li><a href="defective-stock">Defective Stock</a></li>
                                            <li><a href="minimum-stock">Minimum Stock</a></li>
                                            <li><a href="customer-report">Customer Report</a></li>
                                            <li><a href="product-report">Product Report</a></li>
                                        </ul>
                                    </li>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Follow Up Reports</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="follow-up-report-date-wise">Date Wise </a></li>
                                            <li><a href="follow-up-report-customer-wise">Customer Wise</a></li>
                                          
                                        </ul>
                                    </li>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Gift Reports</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="gift-report-date-wise">Date Wise </a></li>
                                            <li><a href="gift-report-customer-wise">Customer Wise</a></li>
                                          
                                        </ul>
                                    </li>

                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Reports</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="division-wise-report">Division Wise </a></li>
                                            <li><a href="state-wise-report">State Wise </a></li>
                                            <li><a href="city-wise-report">City Wise </a></li>
                                            <li><a href="direct-division-wise-report">Direct Division Wise </a></li>
                                            <li><a href="direct-state-wise-report">Direct State Wise </a></li>
                                            <li><a href="direct-city-wise-report">Direct City Wise </a></li>
                                            <li><a href="lead-report">Lead Report </a></li>
                                            <li><a href="source-report">Source Report </a></li>
                                            <li><a href="untouched-report">Untouched Report </a></li>
                                            <li><a href="new-lead-report">New Lead Report </a></li>
                               
                                          
                                        </ul>
                                    </li>

                                <?php } ?>
                                <?php
                                $per = permissions(11, $mysqli, $user);
                                if ($per['view'] == 1) {
                                ?>


                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i data-feather="layers"></i><span>Audit Report</span><span class="menu-arrow"></span>
                                        </a>
                                        <ul>
                                            <?php
                                            if ($per['edit'] == 1) {
                                                echo '<li><a href="audit-settings">Audit Settings</a></li>';
                                            }
                                            ?>


                                            <li><a href="audit-report?status=pending">Audit Report</a></li>




                                        </ul>
                                    </li>

                                <?php } ?>

                            </ul>



                        </li>




                    </ul>

                    <ul>
                        <?php
                        $per = permissions(12, $mysqli, $user);
                        if ($per['view'] == 1) {
                        ?>

                            <li class="submenu">

                                <a href="javascript:void(0);">
                                    <i data-feather="layers"></i><span>Repair</span><span class="menu-arrow"></span>
                                </a>

                                <ul>
                                    <?php
                                    if ($per['edit'] == 1) {
                                        echo '<li><a href="new-repair-order">Create Order</a></li>';
                                    }
                                    ?>


                                    <li><a href="repair-orders?status=repair pending">Orders</a>
                                    </li>



                                </ul>

                            </li>
                        <?php } ?>

                        <?php
                        if ($_SESSION['user']['user_type'] == 'admin') {
                        ?>

                            <li><a href="profile"><i data-feather="user"></i><span>Profile</span></a></li>
                        <?php } ?>
                        <li><a href="../logout"><i data-feather="log-out"></i><span>Logout</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->



        <div class="page-wrapper">
            <div class="content">