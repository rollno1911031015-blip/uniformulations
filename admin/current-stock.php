<?php
include("../layouts/header.php");



if (isset($_GET['loc_id'])) {
    $loc_id = implode(',', $_GET['loc_id']);
    $stmt = $mysqli->prepare("SELECT a.*,b.name as loc,b.address as addres,p.name as pro_name,a.current_stock as quantity,p.* from  current_stock a JOIN locations b on a.loc_id=b.id JOIN products p on a.prod_id=p.id where a.loc_id in (" . $loc_id . ")");
    //  $stmt->bind_param('i', $_GET['loc_id']);
    $stmt->execute();
    $current_stock = $stmt->get_result();
}

if (isset($_GET['product_btn'])) {
    $stmt = $mysqli->prepare("SELECT a.*,b.name as loc,b.address as addres,p.name as pro_name,a.current_stock as quantity,p.* from  current_stock a JOIN locations b on a.loc_id=b.id JOIN products p on a.prod_id=p.id     where a.prod_id=? group by a.prod_id order by created_date desc");
    $stmt->bind_param('i', $_GET['product_id']);
    $stmt->execute();
    $current_stock = $stmt->get_result();
}

if (isset($_GET['brand_srch'])) {
    $stmt = $mysqli->prepare("SELECT a.*,b.name as loc,b.address as addres,p.name as pro_name,a.current_stock as quantity,p.* from  current_stock a JOIN locations b on a.loc_id=b.id JOIN products p on a.prod_id=p.id   join brand x on p.brand=x.name    where x.id=? group by a.prod_id");
    $stmt->bind_param('i', $_GET['brand_id']);
    $stmt->execute();
    $current_stock = $stmt->get_result();
}

if (isset($_POST['BtnUpload'])) {


    $upload_dir = 'temp/';
    $file_url = "";
    if (isset($_FILES['file']['name'])) {
        $pic_name = $_FILES["file"]["name"];
        $pic_tmp_name = $_FILES["file"]["tmp_name"];
        $error = $_FILES["file"]["error"];
        if ($error <= 0) {
            $random_name = rand(1000, 1000000) . "-" . $pic_name;
            $upload_name = $upload_dir . strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);
            try {
                $res = move_uploaded_file($pic_tmp_name, $upload_name);
                $file_url = $upload_name;
            } catch (Exception $e) {
                alert($e->getMessage(), "error", "error");
            }
        }
    }

    if ($file_url !== '') {
        $file = $file_url;
        $handle = fopen($file, 'r');
        $i = 1;
        $success = 0;
        $duplicate = 0;
        $error = "";
        $count = 0;
        while (($filesop = fgetcsv($handle, 1000, ',')) !== false) {

            if ($i >= 1 && !empty($filesop[0])) {



                $category = $filesop[0];
                $name = $filesop[1];
                $batch_no = $filesop[2];
                $expiry_date = date('m-Y', strtotime($filesop[3]));
                $purchase_rate = $filesop[4];
                $stock = $filesop[5];
                $location = $filesop[6];

                $stmt = $mysqli->prepare("SELECT * from  categories where name=?   ;");
                $stmt->bind_param('s', $category);
                $stmt->execute();
                $categories = $stmt->get_result()->fetch_assoc();

                if ($categories) {
                    # code...


                    $count++;

                    $stmt = $mysqli->prepare("SELECT * from  products where name=? and category=?  ;");
                    $stmt->bind_param('ss', $name, $categories['name']);
                    $stmt->execute();
                    $products = $stmt->get_result()->fetch_assoc();

                   
                    $stmt = $mysqli->prepare("SELECT * from   locations where name=?  ;");
                    $stmt->bind_param('s', $location);
                    $stmt->execute();
                    $locations = $stmt->get_result()->fetch_assoc();
                    if ($locations) {

                        if ($products) {

                            try {

                                $stmt = $mysqli->prepare("SELECT * from  current_stock where loc_id=? and prod_id=? and batch_no=?  ;");
                                $stmt->bind_param('iis', $locations['id'], $products['id'], $batch_no);
                                $stmt->execute();
                                $res = $stmt->get_result()->fetch_assoc();

                                if (empty($res)) {
                                    $cstock = $mysqli->prepare("INSERT INTO current_stock(loc_id,prod_id,current_stock,batch_no,expiry_date,purchase_rate) VALUES (?,?,?,?,?,?) ;");
                                    $cstock->bind_param("iiissd", $locations['id'], $products['id'],   $stock, $batch_no, $expiry_date, $purchase_rate);
                                    $cstock->execute();
                                    $cstock->close();
                                    $success++;
                                } else {
                                    $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock+?,purchase_rate=?,expiry_date=? where loc_id =? and prod_id=? and batch_no=?");
                                    $cstock->bind_param("idsiss", $stock, $purchase_rate, $expiry_date, $locations['id'], $products['id'], $batch_no);
                                    $cstock->execute();
                                    $cstock->close();
                                    $success++;
                                }
                            } catch (\Throwable $th) {
                                echo $th->getMessage();
                            }
                        } else {
                            $error .= 'products not found ' . $i . ' Batch No : '.$batch_no.' <br>';
                        }
                    } else {
                        $error .= 'locations not found ' . $i . ' Batch No : '.$batch_no.'<br>';
                    }
                } else {
                    $error .= 'Category not found  ' . $i . ' Batch No : '.$batch_no.'<br>';
                }
            } else {
                $error .= 'Category not found  ' . $i . ' Batch No : '.$batch_no.'<br>';
            }
            $i++;
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        alert($msg, 'info');
        echo $msg;
        echo $error;

        fclose($handle);
    }
    //  redirect('?');
}

if (isset($_POST['btnClearStock'])) {

    $stmt = $mysqli->prepare("TRUNCATE TABLE `u261892001_dmpharmaglobal`.`current_stock` ");

    $stmt->execute();
    redirect("?");
}
?>


<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Current Stock</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="float-end d-flex">
                    <div>
                        <input type="file" name="file">
                    </div>
                    <div>
                        <button class="btn btn-dark" type="submit" name="BtnUpload">Upload</button>
                        <a class="btn btn-primary" href="import-current-stock.csv" download="import-current-stock.csv">Download Sample File</a>

                    </div>
                    <div>
                        <button type="button" class="btn btn-danger mx-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Clear Stock
                        </button>
                    </div>

                </div>

            </form>

        </div>
        <div class="card-body table-responsive">
            <div class="form-group col-md-12">
                <table class="table">
                    <form method="get">
                        <tr>
                            <td style="width: 15rem;">
                                <select name="loc_id[]" id="loc_id" multiple="multiple" class="form-control">

                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '">' . $row['name'] . ' ' . $row['address'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="getrecord" class="btn btn-success">Search</button>
                            </td>


                            <td style="width: 15rem;">
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">--Select product--</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from  products ;");
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="product_btn" class="btn btn-success">Search</button>
                            </td>



                            <!-- <td style="width: 15rem;">
                                <select name="brand_id" id="brand_id" class="form-control">
                                    <option value="">--Select Brand--</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from  brand ;");
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td> -->
                            <!-- <td>
                                <button type="submit" name="brand_srch" class="btn btn-success">Search</button>
                            </td> -->

                        </tr>
                    </form>
                </table>

            </div>

            <table class="table table-hover dataTable ">
                <thead class="bg-ligth text-dark">
                    <tr>
                        <th>#</th>

                        <th>Category Name</th>
                        <th>Product Name</th>
                        <th>Batch No</th>
                        <th>Expiry</th>
                        <th>Quantity</th>

                        <th>Location</th>



                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($current_stock)) {


                        $count = 1;
                        while ($row = $current_stock->fetch_assoc()) {
                            echo '<tr>
                                  <td scope="row">' . $count++ . '</td>
                               
                                  ';
                            echo '
                            <td>' . $row['category'] . '</td>
                            <td>' . $row['pro_name'] . '</td>
                                 <td>' . $row['batch_no'] . '</td>
                                 <td>' . $row['expiry_date'] . '</td>
                                 <td>' . $row['quantity'] . '</td>
                              
                                    <td><div style="  width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;">' . $row['addres'] . '</div></td>';


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

<form method="POST">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Clear Stock</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to clear the stock?</h5>
                    <h6 class="text-danger">* This action can not be undo.</h6>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="btnClearStock">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
include("../layouts/footer.php");
?>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        $("#loc_id").select2();
        $("#product_id").select2();

    });

    $("#loc_id").val(<?= $_GET['loc_id'] ?>)
    $("#product_id").val(<?= $_GET['product_id'] ?>)
    $("#brand_id").val(<?= $_GET['brand_id'] ?>)

    $("#loc_id").val(<?= $_GET['loc_id'] ?>);
    $(document).ready(function() {
        $('#customersTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>