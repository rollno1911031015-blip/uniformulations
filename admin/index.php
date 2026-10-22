<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {

    $city = "";

    if (empty($_POST['user_id'])) {
        alert("User ID Not Found", "error", "error");
        redirect("?");
    }

    if (!empty($_POST['city'])) {
        $city = implode(',', $_POST['city']);
    }
    if (empty($_POST['date'])) {
        $_POST['date'] = "0000-00-00";
    }
    if (empty($_POST['remind_time'])) {
        $_POST['remind_time'] = "00:00:00";
    }
    if (empty($_POST['remind_date'])) {
        $_POST['remind_date'] = "0000-00-00";
    }

    if (!empty($_POST['id'])) {

        $stmt = $mysqli->prepare("UPDATE  lead set division=?,name=?,mobile=?,email=?,source=?,state=?,city=?,address=?,date=?,remarks=?,status=?,remind_time=?,remind_date=?,user_id=?,alternate_phone=? where id=?");
        $stmt->bind_param("isssssssssissisi", $_POST['division'], $_POST['name'], $_POST['mobile'], $_POST['email'], $_POST['source'], $_POST['state'], $city, $_POST['address'], $_POST['date'], $_POST['remarks'], $_POST['status'], $_POST['remind_time'], $_POST['remind_date'], $_POST['user_id'], $_POST['alternate_phone'], $_POST['id']);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("INSERT into leads_comment (lead_id,user_id,status,remarks,remind_time,remind_date) values (?,?,?,?,?,?) ;");
        $stmt->bind_param("iiisss", $_POST['id'], $user['id'], $_POST['status'], $_POST['remarks'], $_POST['remind_time'], $_POST['remind_date']);
        $stmt->execute();

        $stmt->close();
    }
    alert("Save successfully.", "success", "success");
    redirect("?");
}


$year=date("Y");
if ($_GET["year"]) {
$year=$_GET["year"];
}
$stmt1 = $mysqli->prepare("SELECT 
        SUM(CASE WHEN MONTH(sale_date) = 1 THEN a.amount ELSE 0 END) AS jan,
        SUM(CASE WHEN MONTH(sale_date) = 2 THEN a.amount ELSE 0 END) AS feb,
        SUM(CASE WHEN MONTH(sale_date) = 3 THEN a.amount ELSE 0 END) AS mar,
        SUM(CASE WHEN MONTH(sale_date) = 4 THEN a.amount ELSE 0 END) AS apr,
        SUM(CASE WHEN MONTH(sale_date) = 5 THEN a.amount ELSE 0 END) AS may,
        SUM(CASE WHEN MONTH(sale_date) = 6 THEN a.amount ELSE 0 END) AS jun,
        SUM(CASE WHEN MONTH(sale_date) = 7 THEN a.amount ELSE 0 END) AS jul,
        SUM(CASE WHEN MONTH(sale_date) = 8 THEN a.amount ELSE 0 END) AS aug,
        SUM(CASE WHEN MONTH(sale_date) = 9 THEN a.amount ELSE 0 END) AS sep,
        SUM(CASE WHEN MONTH(sale_date) = 10 THEN a.amount ELSE 0 END) AS oct,
        SUM(CASE WHEN MONTH(sale_date) = 11 THEN a.amount ELSE 0 END) AS nov,
        SUM(CASE WHEN MONTH(sale_date) = 12 THEN a.amount ELSE 0 END) AS dece
    FROM direct_sale a
    JOIN lead b ON a.customer_id = b.id
 
      AND b.active = 1
      AND YEAR(a.sale_date) = $year
");


$stmt1->execute();
$sale_monthly = $stmt1->get_result()->fetch_assoc();
$stmt1->close();



?>

<div class="row">
    <div class="col-12 ">
        <div class="d-flex float-end">
            <div>
                <form action="">
                    <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                        <option value="">Select Year</option>
                        <?php


                        for ($i = date("Y"); $i > 2020; $i--) {
                            $selected = "";
                            if ($i == $_GET["year"]) {
                                $selected = "selected";
                            }
                            echo "<option value='" . $i . "' ".$selected.">" . $i . "</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>

        </div>

    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-widget w-100">
            <div class="dash-widgetimg">
                <span><img src="../public/images/dash1.svg" alt="img"></span>
            </div>
            <div class="dash-widgetcontent">

                <?php
                $stmt = $mysqli->prepare("SELECT  count(id) as stock_inward_mst from stock_inward_mst  ");
                //$stmt->bind_param("i",$user['id']);
                $stmt->execute();
                $stock_inward_mst = $stmt->get_result()->fetch_assoc();

                $stmt->close();

                ?>

                <h5><span class="counters" data-count="<?= $stock_inward_mst['stock_inward_mst'] ?>"><?= $stock_inward_mst['stock_inward_mst'] ?></span></h5>
                <h6>Total Inward </h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-widget dash1 w-100">
            <div class="dash-widgetimg">
                <span><img src="../public/images/dash2.svg" alt="img"></span>
            </div>
            <div class="dash-widgetcontent">

                <?php
                $stmt = $mysqli->prepare("SELECT  count(id) as stock_outward_mst from stock_outward_mst  ");
                //$stmt->bind_param("i",$user['id']);
                $stmt->execute();
                $stock_outward_mst = $stmt->get_result()->fetch_assoc();

                $stmt->close();

                ?>

                <h5><span class="counters" data-count="<?= $stock_outward_mst['stock_outward_mst'] ?>"></span></h5>
                <h6>Total Outward</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-widget dash2 w-100">
            <div class="dash-widgetimg">
                <span><img src="../public/images/dash3.svg" alt="img"></span>
            </div>
            <div class="dash-widgetcontent">

                <?php
                $stmt = $mysqli->prepare("SELECT  count(id) as  purchase_return_mst from  purchase_return_mst  ");
                //$stmt->bind_param("i",$user['id']);
                $stmt->execute();
                $purchase_return_mst = $stmt->get_result()->fetch_assoc();

                $stmt->close();

                ?>
                <h5><span class="counters" data-count="<?= $purchase_return_mst['purchase_return_mst'] ?>">$385,656.50</span></h5>
                <h6>Total Purchase Return</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-widget dash3 w-100">
            <div class="dash-widgetimg">
                <span><img src="../public/images/dash4.svg" alt="img"></span>
            </div>
            <div class="dash-widgetcontent">

                <?php
                $stmt = $mysqli->prepare("SELECT  count(id) as  sale_return_mst from  sale_return_mst  ");
                //$stmt->bind_param("i",$user['id']);
                $stmt->execute();
                $sale_return_mst = $stmt->get_result()->fetch_assoc();

                $stmt->close();

                ?>
                <h5><span class="counters" data-count="<?= $sale_return_mst['sale_return_mst'] ?>">$400.00</span></h5>
                <h6>Total Sale Return</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-count">
            <a href="customers">
                <div class="dash-counts">
                    <?php
                    $stmt = $mysqli->prepare("SELECT  count(id) as total_customers from lead where status=4 and active=1 ");
                    //$stmt->bind_param("i",$user['id']);
                    $stmt->execute();
                    $total_customers = $stmt->get_result()->fetch_assoc();

                    $stmt->close();

                    ?>

                    <h4><?= $total_customers['total_customers'] ?></h4>
                    <h5>Total Customers</h5>
                </div>
            </a>
            <div class="dash-imgs">
                <i data-feather="user"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das1">
            <a href="vendor">
                <div class="dash-counts">

                    <?php
                    $stmt = $mysqli->prepare("SELECT  count(id) as total_vendor from vendor ");
                    //$stmt->bind_param("i",$user['id']);
                    $stmt->execute();
                    $total_vendor = $stmt->get_result()->fetch_assoc();

                    $stmt->close();

                    ?>
                    <h4><?= $total_vendor['total_vendor'] ?></h4>
                    <h5>Total Vendors</h5>
                </div>
            </a>
            <div class="dash-imgs">
                <i data-feather="user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das2">
            <a href="products">
                <div class="dash-counts">
                    <?php
                    $stmt = $mysqli->prepare("SELECT  count(id) as total_products from  products ");
                    //$stmt->bind_param("i",$user['id']);
                    $stmt->execute();
                    $total_products = $stmt->get_result()->fetch_assoc();

                    $stmt->close();

                    ?>

                    <h4><?= $total_products['total_products'] ?></h4>
                    <h5>Total Products</h5>
                </div>
            </a>
            <div class="dash-imgs">
                <img src="../public/images/file-text-icon-01.svg" class="img-fluid" alt="icon">
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das3">
            <a href="minimum-stock">
                <div class="dash-counts">
                    <?php
                    $stmt = $mysqli->prepare("SELECT  count(id) as current_stock from  current_stock where current_stock<10 ");
                    //$stmt->bind_param("i",$user['id']);
                    $stmt->execute();
                    $current_stock = $stmt->get_result()->fetch_assoc();

                    $stmt->close();

                    ?>

                    <h4><?= $current_stock['current_stock'] ?></h4>
                    <h5>Near by Minimum stock</h5>
                </div>
            </a>
            <div class="dash-imgs">
                <i data-feather="file"></i>
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->

<div class="row">
    <div class="col-xl-7 col-sm-12 col-12  ">
        <div>
            <canvas id="myChart"></canvas>
        </div>
    </div>
    <div class="col-xl-5 col-sm-12 col-12 d-flex">
        <div class="card flex-fill default-cover mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Fast Moving Products</h4>

            </div>
            <div class="card-body">
                <div class="table-responsive dataview">
                    <table class="table dashboard-recent-products">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Products</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $mysqli->prepare("SELECT COUNT(a.id) AS product, SUM(a.qty) AS qty, b.name AS product_name
                                    FROM stock_outward_det a
                                    JOIN products b ON a.product_id = b.id
                                    GROUP BY a.product_id, b.name LIMIT 4;
                                    ");
                            //$stmt->bind_param("i",$user['id']);
                            $stmt->execute();
                            $total_products = $stmt->get_result();
                            $stmt->close();
                            while ($row = $total_products->fetch_assoc()) {
                                echo '<tr>
                                                <td>' . $row['product'] . '</td>
                                                <td>' . $row['product_name'] . '</td>
                                                <td>' . $row['qty'] . '</td>
                                                </tr>';
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">


        <div class="row mt-5 ">

            <div class="col-md-12 mt-3">
                <h5>Today Call Scheduled</h5>
                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>S.no</th>
                            <th> Name</th>
                            <th>Number</th>
                            <th>Address</th>
                            <th>Created at</th>
                            <th>Remarks</th>


                            <th>User</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php {

                            $stmt = $mysqli->prepare("SELECT a.*,b.name as user_name from  lead a JOIN admin_users b on a.user_id=b.id  where date(a.remind_date)=date(now()) and  user_id in (" . $userIds . ") and a.status=2 ");
                            //  $stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $count = 1;
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td scope="row">' . $count++ . '</td>';
                                echo '<td>' . $row['name'] . '</td>
                          <td>' . $row['mobile'] . '</td> 
                        
                              <td>' . $row['address'] . '</td>
                              <td>' . $row['created_at'] . '</td>
                              <td>' . $row['remarks'] . '</td>
                        
                              <td>' . $row['user_name'] . '</td>
                           
                           <td><button type="button" class="btn btn-primary btn-sm edit_company" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                            <button type="button" class="btn btn-success btn-sm GetComment"   data-id="' . $row['id'] . '" ><i class="fa fa-eye"></i></button>
                           </td>
                          </tr>';
                            }
                        }

                        ?>


                    </tbody>
                </table>
            </div>

            <div class="col-md-12 mt-3">
                <h5>Today Visit Scheduled</h5>
                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>S.no</th>
                            <th> Name</th>
                            <th>Number</th>
                            <th>Address</th>
                            <th>Created at</th>
                            <th>Remarks</th>


                            <th>User</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php {

                            $stmt = $mysqli->prepare("SELECT a.*,b.name as user_name from  lead a JOIN admin_users b on a.user_id=b.id  where date(a.remind_date)=date(now()) and  user_id in (" . $userIds . ") and a.status=3 ");
                            //  $stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $count = 1;
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td scope="row">' . $count++ . '</td>';
                                echo '<td>' . $row['name'] . '</td>
                          <td>' . $row['mobile'] . '</td> 
                        
                              <td>' . $row['address'] . '</td>
                              <td>' . $row['created_at'] . '</td>
                              <td>' . $row['remarks'] . '</td>
                        
                              <td>' . $row['user_name'] . '</td>
                           
                           <td><button type="button" class="btn btn-primary btn-sm edit_company" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                               <button type="button" class="btn btn-success btn-sm GetComment"   data-id="' . $row['id'] . '" ><i class="fa fa-eye"></i></button>
                           </td>
                          </tr>';
                            }
                        }

                        ?>


                    </tbody>
                </table>
            </div>


            <div class="col-md-12 mt-3">
                <h5>Missed Follow Ups</h5>
                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>S.no</th>
                            <th> Name</th>
                            <th>Number</th>
                            <th>Address</th>
                            <th>Created at</th>
                            <th>Remarks</th>


                            <th>User</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php {

                            $stmt = $mysqli->prepare("SELECT a.*,b.name as user_name from  lead a JOIN admin_users b on a.user_id=b.id  where date(a.remind_date)<date(now()) and (a.status=3 or a.status=2) and  user_id in (" . $userIds . ") ");
                            //  $stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $count = 1;
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td scope="row">' . $count++ . '</td>';
                                echo '<td>' . $row['name'] . '</td>
                          <td>' . $row['mobile'] . '</td> 
                        
                              <td>' . $row['address'] . '</td>
                              <td>' . $row['created_at'] . '</td>
                              <td>' . $row['remarks'] . '</td>
                        
                              <td>' . $row['user_name'] . '</td>
                           
                           <td><button type="button" class="btn btn-primary btn-sm edit_company" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                               <button type="button" class="btn btn-success btn-sm GetComment"   data-id="' . $row['id'] . '" ><i class="fa fa-eye"></i></button>
                           </td>
                          </tr>';
                            }
                        }

                        ?>


                    </tbody>
                </table>
            </div>

            <div class="col-md-12">
                <h5>Todays Stock Inward</h5>

                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>#</th>
                            <th>Created Date</th>
                            <th>Vendor</th>
                            <th>Invoice No.</th>
                            <th>Location</th>
                            <th>User</xth>
                            <th>Action</xth>

                        </tr>
                    </thead>
                    <tbody>
                        <?php {

                            $stmt = $mysqli->prepare("SELECT a.*,b.qty as quantity,d.name as loc,d.name as address,e.name as user_name,f.con_person_name as vendor from   stock_inward_mst a JOIN stock_inward_det b on a.id=b.stock_mst_id JOIN locations d on a.loc_id=d.id JOIN admin_users e on a.user_id=e.id JOIN vendor f on a.vendor_id=f.id where date(a.created_date)=date(now())");
                            //  $stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $count = 1;
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td scope="row">' . $count++ . '</td>';
                                echo '<td>' . $row['created_date'] . '</td>
                           <td>' . $row['vendor'] . '</td>
                              <td>' . $row['invoice'] . ' </td>
                              <td>' . $row['loc'] . ' ' . $row['address'] . '</td>
                              <td>' . $row['user_name'] . '</td>
                              <td><a class="btn btn-primary btn-sm" href="inward-report.php?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a></td>
                              ';

                                echo '
                        
                          </tr>';
                            }
                        }

                        ?>


                    </tbody>
                </table>

            </div>
            <div class="col-md-12 mt-3">
                <h5>Todays Stock Outward</h5>
                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>S.no</th>
                            <th>Challan ID</th>
                            <th>Date</th>
                            <th>Lead</th>

                            <th>Location</th>
                            <th>User</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php {

                            $stmt = $mysqli->prepare("SELECT a.*,b.name,c.name as loc,d.name as user_name from stock_outward_mst a JOIN lead b on a.lead_id=b.id JOIN locations c on a.loc_id=c.id JOIN admin_users d on a.user_id=d.id  where date(a.created_date)=date(now()) ");
                            //  $stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $count = 1;
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td scope="row">' . $count++ . '</td>';
                                echo '<td>' . $row['id'] . '</td>
                          <td>' . $row['created_date'] . '</td> 
                        
                              <td>' . $row['name'] . '</td>
                              <td>' . $row['loc'] . '  </td>
                              <td>' . $row['user_name'] . '</td>
                              <td><a class="btn btn-primary btn-sm" href="outward-report.php?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a></td>';

                                echo '
                        
                          </tr>';
                            }
                        }

                        ?>


                    </tbody>
                </table>
            </div>



        </div>
    </div>
</div>

<?php include("../layouts/footer.php"); ?>
<div class="modal fade" id="exampleModal">
    <div class="modal-dialog modal-lg">
        <form class="row g-3 needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Lead</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label"> Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="">

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label"> mobile</label>
                        <input type="text" class="form-control" id="mobile" list="datalistOptions" name="mobile" onkeypress="if(this.value.length==10) return false" value="" required>
                        <datalist id="datalistOptions">
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `lead` where status!=4");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['mobile'] . '">';
                            }
                            ?>



                        </datalist>


                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label">Alternate mobile</label>
                        <input type="text" class="form-control" id="alternate_phone" name="alternate_phone" value="">

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="">

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Division</label>
                        <select class="form-control" id="division" name="division" value="">
                            <option value="">--Select--</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `brand` order by id desc");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Source</label>
                        <select class="form-control" id="source" name="source" value="">
                            <option value="">--Select--</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `source` order by id desc");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select State</label>
                        <select class="form-control state" id="state" name="state" value="">
                            <option value="">Select</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT DISTINCT state from  state_district");
                            $stmt->execute();
                            $state = $stmt->get_result();
                            while ($row = $state->fetch_assoc()) {
                                echo '<option value="' . $row['state'] . '">' . $row['state'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select City</label>
                        <select class="form-control city" id="city" multiple name="city[]" value="">
                            <option value="">Select City</option>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select User</label>
                        <select class="form-control" id="user_id" name="user_id" value="">
                            <option value="">Select</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT * from admin_users where id in (" . $userIds . ")");
                            $stmt->execute();
                            $state = $stmt->get_result();
                            while ($row = $state->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3 d-none">
                        <label for="" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="">


                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" value=""></textarea>


                    </div>



                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select Status</label>
                        <select class="form-control state" id="status" name="status" value="">

                            <?php
                            $stmt = $mysqli->prepare("SELECT * from status");
                            $stmt->execute();
                            $state = $stmt->get_result();
                            while ($row = $state->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-4 mt-3 remind">
                        <label for="validationCustom01" class="form-label"> Remind Date</label>
                        <input type="date" class="form-control" id="remind_date" name="remind_date" value="">

                    </div>
                    <div class="col-md-4 mt-3 remind">
                        <label for="validationCustom01" class="form-label"> Remind Time</label>
                        <input type="time" class="form-control" id="remind_time" name="remind_time" value="">

                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" value=""></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave" id="btn_save">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div
    class="modal fade"
    id="commentModal">
    <div
        class="modal-dialog modal-xl "
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Comments
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>

                            <th>User</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Remind Time</th>
                            <th>Remind Date</th>
                            <th>Created at</th>
                        </tr>
                    </thead>
                    <tbody id="CommentList">

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<script>
    $("#city").select2()
    $("#state").on("change", function() {
        state = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {

                $("#city").html(data);

            }
        });
    });
    $(document).on("click", ".edit_company", function() {
        var id = $(this).data("id");
        $("#id").val(id);

        $.ajax({
            method: 'POST',
            url: 'ajax/get-lead.php',
            data: {
                id: id
            },
            success: function(data) {

                data = JSON.parse(data);
                $.each(data, function(i, o) {
                    $('input[name=' + i + ']').val(o);
                    $('select[name=' + i + ']').val(o);
                    $('textarea[name=' + i + ']').val(o);
                    if (i == "city") {
                        $("#city").html("<option value=" + o + ">" + o + "</option>")
                    }
                })
                $("#modal_name").text("Update Lead");
                $("#exampleModal").modal("show");

            }
        });



    });
</script>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');
    const salesData = <?php echo json_encode(array_values($sale_monthly)); ?>;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Month Wise Sale',
                data: salesData,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    $(document).on("click", ".GetComment", function() {
        var id = $(this).data("id");
        $("#id").val(id);

        $.ajax({
            method: 'POST',
            url: 'ajax/get-comments.php',
            data: {
                id: id
            },
            success: function(data) {
                var html = "";
                data = JSON.parse(data)
                var sno = 1;
                data.forEach(element => {
                    html += `<tr>
                        <td>${sno++}</td>
                       
                        <td>${element.user}</td>
                        <td>${element.remarks}</td>
                        <td>${element.status}</td>
                        <td>${element.remind_date}</td>
                        <td>${element.remind_time}</td>
                        <td>${element.created_at}</td>
                        </tr>`;
                });

                $("#CommentList").html(html)

                $("#commentModal").modal("show");

            }
        });
    });
</script>