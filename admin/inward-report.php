<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT a.*,d.name as loc,d.name as address,e.name as user_name,f.con_person_name as vendor,f.gst,f.contact_no,g.company_name,concat(f.address,' ',f.city,' ',f.state,' ',f.pincode) as vaddress from   stock_inward_mst a  JOIN locations d on a.loc_id=d.id JOIN admin_users e on a.user_id=e.id JOIN vendor f on a.vendor_id=f.id left join company g on f.company_id=g.id where a.id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!empty($_POST['id']) && !empty($_POST['qty']) && !empty($_POST['prod_id'])) {

    $stmt = $mysqli->prepare("SELECT  * from users where t_password=?");
    $stmt->bind_param('s', $_POST['t_password']);
    $stmt->execute();
    $t_password = $stmt->get_result()->fetch_assoc();
    if ($t_password) {

        $stmt = $mysqli->prepare("SELECT  sum(qty) as qty from stock_outward_det where product_id=?");
        $stmt->bind_param('i', $_POST['prod_id']);
        $stmt->execute();
        $stock_outward_det = $stmt->get_result()->fetch_assoc();



        // if ($_POST['qty'] < $stock_outward_det['qty']) {
        //     alert('Qty Should be more then.' . $stock_outward_det['qty'] . '', 'error');
        //     redirect('inward-report.php?id=' . $_GET['id'] . '');
        // }


        $stmt = $mysqli->prepare("SELECT  * from current_stock where prod_id=? and loc_id=?");
        $stmt->bind_param('ii', $_POST['prod_id'], $res['loc_id']);
        $stmt->execute();
        $current_stock = $stmt->get_result()->fetch_assoc();


        $stmt = $mysqli->prepare("SELECT  * from stock_inward_det where id=?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $stock_inward_det = $stmt->get_result()->fetch_assoc();

        $qty = 0;
        $qty = $stock_inward_det['qty'] - $_POST['qty'];

        $cstock = $current_stock['current_stock'] - $qty;

        if ($cstock < 0) {
            alert('Current Stock is zero. ', 'error');
            redirect('inward-report?id=' . $_GET['id'] . '');
        }



        $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=?");
        $cstock->bind_param("iii", $qty, $res['loc_id'],  $_POST['prod_id']);
        $cstock->execute();
        $cstock->close();

        $particulars = "Edit Inward Stock";
        $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
        $stmt->bind_param("iiiis",  $_POST['prod_id'], $qty, $res['id'], $t_password['id'], $particulars);
        $stmt->execute();
        $stmt->close();

        $cstock = $mysqli->prepare("UPDATE stock_inward_det set qty=? where id=?");
        $cstock->bind_param("ii", $_POST['qty'], $_POST['id']);
        $cstock->execute();
        $cstock->close();
        alert('Update Successfully.', 'success');
        redirect('inward-report?id=' . $_GET['id'] . '');
    } else {
        alert('Incorrect password.', 'error');
        redirect('inward-report?id=' . $_GET['id'] . '');
    }
}


if (isset($_POST['Btn_delete'])) {

    $stmt = $mysqli->prepare("SELECT  * from users where t_password=?");
    $stmt->bind_param('s', $_POST['t_password']);
    $stmt->execute();
    $t_password = $stmt->get_result()->fetch_assoc();
    if ($t_password) {


        $stmt = $mysqli->prepare("SELECT  * from stock_inward_det where id=?");
        $stmt->bind_param('i', $_POST['d_id']);
        $stmt->execute();
        $stock_inward_det = $stmt->get_result()->fetch_assoc();

        $stmt = $mysqli->prepare("SELECT  * from stock_inward_mst where id=?");
        $stmt->bind_param('i', $res['id']);
        $stmt->execute();
        $stock_inward_mst = $stmt->get_result()->fetch_assoc();


        $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=?");
        // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
        $cstock->bind_param("iii", $stock_inward_det['qty'], $stock_inward_mst['loc_id'],  $stock_inward_det['product_id']);
        $cstock->execute();
        $cstock->close();

        $particulars = "Delete Inward";
        $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
        $stmt->bind_param("iiiis",  $stock_inward_det['product_id'], $stock_inward_det['qty'], $stock_inward_mst['id'], $t_password['id'], $particulars);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("DELETE from stock_inward_det where id=?");
        $stmt->bind_param('i', $_POST['d_id']);
        $stmt->execute();

        alert('Delete Successfully.', 'success');
        redirect('inward-report?id=' . $_GET['id'] . '');
    } else {
        alert('Incorrect password.', 'error');
        redirect('inward-report?id=' . $_GET['id'] . '');
    }
}


if (isset($_POST['btnAddProd'])) {


    if (intval($_POST['qty']) <= 0) {
        alert('Enter qty more then zero.', 'error');
        redirect('inward-report?id=' . $_GET['id'] . '');
    }
    $stmt = $mysqli->prepare("SELECT  * from users where t_password=?");
    $stmt->bind_param('s', $_POST['t_password']);
    $stmt->execute();
    $t_password = $stmt->get_result()->fetch_assoc();
    if ($t_password) {

        $stmt = $mysqli->prepare("SELECT * from   stock_inward_det where product_id=? and stock_mst_id=?  ;");
        $stmt->bind_param('ii', $_POST['iprod_id'], $res['id']);
        $stmt->execute();
        $stock_inward_det = $stmt->get_result()->fetch_assoc();
        if (!empty($stock_inward_det)) {
            alert('Product Already Added.', 'error');
            redirect('inward-report?id=' . $_GET['id'] . '');
        }


        $stmt = $mysqli->prepare("INSERT into stock_inward_det(stock_mst_id,product_id,qty) values(?,?,?);");
        $stmt->bind_param("iii",  $res['id'], $_POST['iprod_id'], $_POST['qty']);
        $stmt->execute();
        $stmt->close();


        $stmt = $mysqli->prepare("SELECT * from  current_stock where loc_id=? and prod_id=?  ;");
        $stmt->bind_param('ii', $res['loc_id'], $_POST['iprod_id']);
        $stmt->execute();
        $current_stock = $stmt->get_result()->fetch_assoc();

        if (empty($current_stock)) {
            $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock+?;");
            $cstock->bind_param("iiii", $res['loc_id'], $_POST['iprod_id'], $_POST['qty'], $_POST['qty']);
            $cstock->execute();
            $cstock->close();
        } else {
            $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock+? where loc_id =? and prod_id=?");
            $cstock->bind_param("iii", $_POST['qty'], $res['loc_id'], $_POST['iprod_id']);
            $cstock->execute();
            $cstock->close();
        }

        $particulars = "Add Inward Stock";
        $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
        $stmt->bind_param("iiiis",  $_POST['iprod_id'], $_POST['qty'], $res['id'], $t_password['id'], $particulars);
        $stmt->execute();
        $stmt->close();

        alert('Successfully Saved.', 'success');
        redirect('inward-report?id=' . $_GET['id'] . '');
    } else {
        alert('Incorrect password.', 'error');
        redirect('inward-report?id=' . $_GET['id'] . '');
    }
}

$per = permissions(4, $mysqli, $user);

?>

<style>
    @media print {
        .noPrint {
            display: none;
            margin-top: 0px;
        }
    }

    body {
        margin-top: 10px;
        color: #484b51;
    }

    .text-secondary-d1 {
        color: #728299 !important;
    }

    .page-header {
        margin: 0 0 1rem;
        padding-bottom: 1rem;
        padding-top: .5rem;
        border-bottom: 1px dotted #e2e2e2;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -ms-flex-align: center;
        align-items: center;
    }

    .page-title {
        padding: 0;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 300;
    }

    .brc-default-l1 {
        border-color: #dce9f0 !important;
    }

    .ml-n1,
    .mx-n1 {
        margin-left: -.25rem !important;
    }

    .mr-n1,
    .mx-n1 {
        margin-right: -.25rem !important;
    }

    .mb-4,
    .my-4 {
        margin-bottom: 1.5rem !important;
    }

    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, .1);
    }

    .text-grey-m2 {
        color: #888a8d !important;
    }

    .text-success-m2 {
        color: #86bd68 !important;
    }

    .font-bolder,
    .text-600 {
        font-weight: 600 !important;
    }

    .text-110 {
        font-size: 100% !important;
    }

    .text-blue {
        color: #478fcc !important;
    }

    .pb-25,
    .py-25 {
        padding-bottom: .75rem !important;
    }

    .pt-25,
    .py-25 {
        padding-top: .75rem !important;
    }

    .bgc-default-tp1 {
        background-color: rgba(121, 169, 197, .92) !important;
    }

    .bgc-default-l4,
    .bgc-h-default-l4:hover {
        background-color: #f3f8fa !important;
    }

    .page-header .page-tools {
        -ms-flex-item-align: end;
        align-self: flex-end;
    }

    .btn-light {
        color: #757984;
        background-color: #f5f6f9;
        border-color: #dddfe4;
    }

    .w-2 {
        width: 1rem;
    }

    .text-120 {
        font-size: 110% !important;
    }

    .text-primary-m1 {
        color: #4087d4 !important;
    }

    .text-danger-m1 {
        color: #dd4949 !important;
    }

    .text-blue-m2 {
        color: #68a3d5 !important;
    }

    .text-150 {
        font-size: 150% !important;
    }

    .text-60 {
        font-size: 60% !important;
    }

    .text-grey-m1 {
        color: #7b7d81 !important;
    }

    .align-bottom {
        vertical-align: bottom !important;
    }

    #itemlist td {
        padding: 6px 15px;
    }
</style>
<script>
    function printcontent() {
        $(".buttons").hide()
        var printContents = document.getElementById('printorder').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>



<div class="card">

    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header">

                <div class="">
                    <button class="noPrint btn bg-white btn-light mx-1px text-95" onclick="printcontent()" data-title="Print">
                        <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                        Print
                    </button>
                    <button type="button" class="btn btn-info btn-sm EditView" data-id="<?= $res['id'] ?>"><i class="fa fa-eye"></i></button>

                    <?php
                      if ($per['edit']==1) {

                    if (empty($_GET['edit'])) {
                    ?>
                        <a class="btn btn-dark float-end btn-sm" href="?id=<?= $_GET['id'] ?>&edit=editable"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <?php
                    } else {
                    ?>
                        <a class="btn btn-dark float-end btn-sm" href="?id=<?= $_GET['id'] ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    <?php

                    }}
                    ?>

                </div>

            </div>

            <div class="card-body" id="printorder">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        GSTIN : GST <br>
                        INWARD CHALLAN
                    </div>

                    <div class="text-center">

                        <img src="../public/logo.png" alt="" width="320px">

                    </div>
                    <div>
                        <div style="text-align: right;">
                            Mobile : 9999999999
                        </div>
                    </div>
                </div>

                <hr style="border: solid ;">
                <div class="row">
                    <div class="col-md-7" style="border: solid;">
                        From <br>

                        M/s <?= $res['company_name'] ?><br>
                        <?= $res['vaddress'] ?><br>
                        GST: <?= $res['gst'] ?><br>
                        Contact: <?= $res['vendor'] ?> <?= $res['contact_no'] ?>
                    </div>
                    <div class="col-md" style="border: solid;">
                        Inward No: <?= $res['invoice'] ?><br>
                        Invoice Date : <?= $res['invoice_date'] ?> <br>
                        Received Material Date : <?= $res['r_material_date'] ?> <br>
                        Created Date : <?= $res['created_date'] ?>

                    </div>
                    <div></div>

                </div>
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>

                                <th scope="col">Qty</th>
                                <th scope="col">Received Qty</th>
                                <th scope="col">Box Qty</th>

                                <?php
                                if (!empty($_GET['edit'])) {
                                    echo '<th>
                                    <button type="button" id="btn_success" class="btn btn-success btn-sm Add_Product buttons"><i class="fa fa-plus"></i> </button>
                                </th>';
                                }

                                ?>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,b.price as price,b.uom,b.brand from stock_inward_det a JOIN products b on a.product_id=b.id where a.stock_mst_id=?");
                            $stmt->bind_param('i', $res['id']);
                            $stmt->execute();
                            $products = $stmt->get_result();
                            while ($row = $products->fetch_assoc()) {
                                echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        <td>' . $row['brand'] . '</td>
                                                        <td>' . $row['product'] . '</td>
                                                       
                                                        <td>' . $row['qty'] . ' ' . $row['uom'] . '</td>
                                                        <td>' . $row['received_qty'] . ' ' . $row['uom'] . '</td>
                                                        <td>' . $row['box_qty'] . '  </td>';


                                if (!empty($_GET['edit'])) {
                                    echo   '<td class="buttons">
                                                        <button type="button" class="btn btn-info btn-sm EDIT"     
                                                        data-id="' . $row['id'] . '"
                                                        data-qty="' . $row['qty'] . '"
                                                        data-product_id="' . $row['product_id'] . '"
                                                        
                                                        ><i class="fa fa-pencil"
                                                    
                                                        ></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm Delete" data-id="' . $row['id'] . '">
                                                        <i class="fa fa-trash"></i></button>';
                                }




                                echo  '</td>
                                                    </tr>';
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-5">
                    <div>
                        Received By ______________________________
                    </div>
                    <div>
                        Checked By ______________________________
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="EDIT_Challan_Product">
    <div class="modal-dialog">
        <form class=" g-3 needs-validation" novalidate method="POST" id="AddProductForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Edit Product</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <input id="prod_id" name="prod_id" type="hidden">
                    <div class="">
                        <input type="number" id="qty" name="qty" required class="form-control"> <br>
                        <input type="text" name="t_password" id="" class="form-control" placeholder="Enter Password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name="btn_Save" id="btn_Save">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="Delete_Challan_Product">
    <div class="modal-dialog">
        <form class=" g-3 needs-validation" novalidate method="POST" id="">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Delete Product</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="d_id" name="d_id" type="hidden">


                    <h5>Are you sure you want to delete this product?</h5>

                    <input type="text" name="t_password" id="" class="form-control" placeholder="Enter Password">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" name="Btn_delete" id="Btn_delete">Yes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="Add_return_product">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Inward Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <select class="form-control" id="category" name="category">
                                    <option value="">Select Category</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from categories");
                                    //$stmt->bind_param('i', $_GET['loc_id']);
                                    $stmt->execute();
                                    $categories = $stmt->get_result();
                                    while ($row = $categories->fetch_assoc()) {
                                        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>



                                </select>

                            </td>
                            <td>
                                <select id="subcategory" name="subcategory" class="form-control">
                                    <option>Select subcategory</option>
                                </select>

                            </td>
                            <td>
                                <select name="iprod_id" id="iprod_id" class="form-control" required>
                                    <option value="">--Select Product--</option>

                                </select>

                            </td>
                            <td>
                                <input type="number" name="qty" id="qty" placeholder="Enter Quantity" class="form-control" required>
                            </td>

                        </tr>
                    </table>
                    <div class="col-md-12 mt-3">
                        <input type="text" name="t_password" id="t_password" placeholder="Enter Password" class="form-control" required>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="" name="btnAddProd">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="EditViewModal">
    <div class="modal-dialog">
        <form class=" g-3 needs-validation" novalidate method="POST" id="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Edit View</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Particular</th>
                                <th>User</th>
                            </tr>

                        </thead>
                        <tbody id="prod_list">

                        </tbody>
                    </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
        </form>
    </div>
</div>



<?php include("../layouts/footer.php"); ?>





<script>
    $(document).on("click", '.EDIT', function() {
        $("#id").val($(this).data("id"))
        $("#qty").val($(this).data("qty"))
        $("#prod_id").val($(this).data('product_id'))
        $("#EDIT_Challan_Product").modal("show")
    })
    $("#btn_Save").on("click", function() {
        if ($("#qty").val() <= 0) {
            alert("Please Enter Value")
            return;
        }

        $("#AddProductForm").submit();
    })
    $(".Add_Product").on("click", function() {
        $("#Add_return_product").modal("show");
    })
    $("#save_btn").on("click", function() {

        if ($("#product").val() == false) {
            alert("Please select product");
            return;
        }
        if ($("#p_qty").val() <= 0) {
            alert("Please enter qty more then zero");
            return;
        }
        if ($("#t_password").val() == false) {
            alert("Please enter password");
            return;
        }
        if ($("#p_qty").val() > $("#product").find(':selected').data('qty')) {
            alert("Enter Value less then qty.");
            return;
        }

        $("#frmMain").submit();

    })
    $(document).on("click", ".Delete", function() {
        $("#d_id").val($(this).data("id"))
        $("#Delete_Challan_Product").modal("show");

    });
    $("#category").on("change", function() {
        let name = $(this).val();

        $.ajax({
            method: 'POST',
            url: 'ajax/get-subcategory.php',
            data: {
                name: name
            },
            success: function(data) {

                $("#subcategory").html(data);

            }
        });
    });
    $("#subcategory").on("change", function() {
        let subcategory = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-product-list.php',
            data: {
                subcategory: subcategory
            },
            success: function(data) {

                $("#iprod_id").html(data);

            }
        });
    });
    $(document).on("click", ".EditView", function() {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-edit-view-details.php',
            data: {
                challan_id: $(this).data("id")
            },
            success: function(data) {
                var html = "";
                data = JSON.parse(data);
                data.forEach(element => {
                    html += "<tr><td>" + element.product_name + "</td><td>" + element.qty +
                        "</td> <td>" + element.particulars + "</td><td>" + element.user_name +
                        "</td> </tr>";

                });
                $("#prod_list").html(html);
                $("#EditViewModal").modal("show");

            }
        });


    });
</script>