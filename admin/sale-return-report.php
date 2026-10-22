<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT a.*,e.name as user_name,f.name as customer,z.company_name as company_name,f.state,f.address,f.mobile,f.city,f.pincode,f.country,f.gst from   sale_return_mst a   JOIN admin_users e on a.user_id=e.id JOIN lead f on a.lead_id=f.id JOIN company z on f.company_id=z.id where a.id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();


if (!empty($_POST['product']) && !empty($_POST['p_qty']) && !empty($_POST['t_password'])) {

    $stmt = $mysqli->prepare("SELECT  * from sale_return_det where product_id=? and sale_return_mst_id=?");
    $stmt->bind_param('ii', $_POST['product'], $res['id']);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_assoc();
    if ($products) {
        alert("This Product Already Added", 'error');
        redirect('sale-return-report.php?id=' . $_GET['id'] . '');
    } else {

        $stmt = $mysqli->prepare("SELECT  * from admin_users where t_password=?");
        $stmt->bind_param('s', $_POST['t_password']);
        $stmt->execute();
        $t_password = $stmt->get_result()->fetch_assoc();
        if ($t_password) {

            $stmt = $mysqli->prepare("SELECT * from  stock_outward_mst where id=?  ;");
            $stmt->bind_param('i', $res['challan_id']);
            $stmt->execute();
            $inv = $stmt->get_result()->fetch_assoc();

            $stmt = $mysqli->prepare("INSERT into sale_return_det(sale_return_mst_id,product_id,qty) values(?,?,?);");
            $stmt->bind_param("iii",  $res['id'], $_POST['product'], $_POST['p_qty']);
            $stmt->execute();
            $stmt->close();

            $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock+? where loc_id=? and  prod_id=?");
            // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
            $cstock->bind_param("iii", $_POST['p_qty'], $inv['loc_id'], $_POST['product']);
            $cstock->execute();
            $cstock->close();

            $particulars = "Sale Return";
            $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
            $stmt->bind_param("iiiis",  $_POST['product'], $_POST['p_qty'], $res['challan_id'], $t_password['id'], $particulars);
            $stmt->execute();
            $stmt->close();
            alert('Return successfully.', 'success');
            redirect('sale-return-report?id=' . $_GET['id'] . '');
        } else {
            alert('Incorrect password.', 'error');
            redirect('sale-return-report?id=' . $_GET['id'] . '');
        }
    }
}

if (isset($_POST['Btn_delete'])) {

    $stmt = $mysqli->prepare("SELECT  * from admin_users where t_password=?");
    $stmt->bind_param('s', $_POST['t_password']);
    $stmt->execute();
    $t_password = $stmt->get_result()->fetch_assoc();
    if ($t_password) {



        $stmt = $mysqli->prepare("SELECT  a.*,b.challan_id as challan_id  from sale_return_det a JOIN  sale_return_mst b on a.sale_return_mst_id=b.id where a.id=?");
        $stmt->bind_param('i', $_POST['d_id']);
        $stmt->execute();
        $sale_return_det = $stmt->get_result()->fetch_assoc();

        $stmt = $mysqli->prepare("SELECT  * from stock_outward_mst where id=?");
        $stmt->bind_param('i', $sale_return_det['challan_id']);
        $stmt->execute();
        $stock_inward_mst = $stmt->get_result()->fetch_assoc();


        $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=?");
        // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
        $cstock->bind_param("iii", $sale_return_det['qty'], $stock_inward_mst['loc_id'],  $sale_return_det['product_id']);
        $cstock->execute();
        $cstock->close();

        $particulars = "Delete Sale Return Product";
        $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
        $stmt->bind_param("iiiis",  $sale_return_det['product_id'], $sale_return_det['qty'], $sale_return_det['challan_id'], $t_password['id'], $particulars);
        $stmt->execute();
        $stmt->close();


        $stmt = $mysqli->prepare("DELETE from sale_return_det where id=?");
        $stmt->bind_param('i', $_POST['d_id']);
        $stmt->execute();

        alert('Delete Successfully.', 'success');
        redirect('sale-return-report?id=' . $_GET['id'] . '');
    } else {
        alert('Incorrect password.', 'error');
        redirect('sale-return-report?id=' . $_GET['id'] . '');
    }
}

if (!empty($_POST['id']) && !empty($_POST['qty'])) {

    $stmt = $mysqli->prepare("SELECT  * from admin_users where t_password=?");
    $stmt->bind_param('s', $_POST['t_password']);
    $stmt->execute();
    $t_password = $stmt->get_result()->fetch_assoc();
    if ($t_password) {

        $stmt = $mysqli->prepare("SELECT  a.*,b.challan_id as challan_id,c.qty as mst_qty  from sale_return_det a JOIN  sale_return_mst b on a.sale_return_mst_id=b.id JOIN stock_outward_det c on a.product_id=c.product_id and b.challan_id=c.stock_outward_mst_id where a.id=?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $sale_return_det = $stmt->get_result()->fetch_assoc();
        if ($_POST['qty'] > $sale_return_det['mst_qty']) {
            alert('Qty Should be less.' . $sale_return_det['mst_qty'] . '', 'error');
            redirect('sale-return-report.php?id=' . $_GET['id'] . '');
        }
        $qty = 0;
        $qty = $sale_return_det['qty'] - $_POST['qty'];

        $stmt = $mysqli->prepare("SELECT  * from stock_outward_mst where id=?");
        $stmt->bind_param('i', $sale_return_det['challan_id']);
        $stmt->execute();
        $stock_inward_mst = $stmt->get_result()->fetch_assoc();

        $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=?");
        $cstock->bind_param("iii", $qty, $stock_inward_mst['loc_id'],  $sale_return_det['product_id']);
        $cstock->execute();
        $cstock->close();

        $particulars = "Edit Sale Return Product";
        $stmt = $mysqli->prepare("INSERT into challan_logs(product_id,qty,challan_id,user_id,particulars) values(?,?,?,?,?);");
        $stmt->bind_param("iiiis",  $sale_return_det['product_id'], $qty, $sale_return_det['challan_id'], $t_password['id'], $particulars);
        $stmt->execute();
        $stmt->close();

        $cstock = $mysqli->prepare("UPDATE sale_return_det set qty=? where id=?");
        $cstock->bind_param("ii", $_POST['qty'], $_POST['id']);
        $cstock->execute();
        $cstock->close();
        alert('Update Successfully.', 'success');
        redirect('sale-return-report?id=' . $_GET['id'] . '');
    } else {
        alert('Incorrect password.', 'error');
        redirect('sale-return-report?id=' . $_GET['id'] . '');
    }
}


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

        document.getElementById("btn_success").style.display = "none";
        $(".EDIT").css("display", "none");
        $(".Delete").css("display", "none");

        var printContents = document.getElementById('printorder').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>


<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Sale Return Report</h4>
            <div class="pull-right">
                <button class="noPrint btn bg-white btn-light mx-1px text-95" onclick="printcontent()" data-title="Print">
                    <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                    Print
                </button>
                <button type="button" class="btn btn-secondary btn-sm EditView" data-id="<?= $res['id'] ?>"><i class="fa fa-eye"></i></button>
            </div>
        </div>
        <div class="card-body table-responsive" id="printorder">


            <div style="display: flex; justify-content: space-between;">
                <div>
                    GSTIN : GST46446565465
                </div>
                <div>
                    OUTWARD CHALLAN

                </div>
                <div>
                    <div style="text-align: right;">
                        Mobile : 9999999999
                    </div>
                </div>
            </div>
            <div class="text-center d-flex justify-content-center">
                <div>
                <div class="text-center">
                    <img src="../public/logo.png" alt="" width="320px">

                </div>
                </div>
                
            </div>
            <hr style="border: solid ;">
            <div class="row">
                <div class="col-md-7" style="border: solid;">
                    <div>
                        To
                    </div>

                    <div class="mx-5">
                        <?= $res['customer'] ?>
                        <br>

                        <?= $res['company_name'] ?>
                        <br>


                        <?= $res['address'] . ', ' . $res['city'] . ', ' . $res['state'] . ', ' . $res['pincode'] . ', ' . $res['country'] ?>
                        <br>

                        GSTIN : <?= $res['gst'] ?>
                        <br>

                        Mobile No. : <?= $res['mobile'] ?>
                    </div>
                </div>
                <div class="col-md" style="border: solid;">
                    Challan No: <?= $res['id'] ?><br><br><br>
                    Invoice Date : <?= $res['invoice_date'] ?> <br>
                    Created Date : <?= $res['created_at'] ?>

                </div>
                <div></div>

            </div>


            <div class="row mt-3">
                <div class="col-md-12">
                    <h4>Products</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Product Name</th>

                                    <th scope="col">Qty</th>
                               


                                    <th>
                                        <?php if ($res['prev_challan'] == 0) { ?>
                                            <button type="button" id="btn_success" class="btn btn-success btn-sm Add_Product"><i class="fa fa-plus"></i> </button>
                                        <?php } ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,b.price as price,b.uom,b.category from  sale_return_det a JOIN products b on a.product_id=b.id where a.sale_return_mst_id=?");
                                $stmt->bind_param('i', $res['id']);
                                $stmt->execute();
                                $products = $stmt->get_result();
                                while ($row = $products->fetch_assoc()) {
                                    echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        <td>' . $row['category'] . '</td>
                                                        <td>' . $row['product'] . '</td>
                                                    
                                                        <td>' . $row['qty'] . ' '.$row['uom'].'</td>
                                                      <td>';

                                    if ($res['prev_challan'] == 0)
                                        echo  '
                                                        <button type="button" class="btn btn-info btn-sm EDIT"     data-id="' . $row['id'] . '"
                                                        data-qty="' . $row['qty'] . '"><i class="fa fa-pencil"
                                                    
                                                        ></i></button>';

                                    echo    '<button type="button" class="btn btn-danger btn-sm Delete" data-id="' . $row['id'] . '">
                                                        <i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>


                </div>


            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Terms : </strong>
                    <ol>
                        <li>Goods supplied are as per order will not be taken back</li>
                        <li>Our responsibility cases when goods leave our godown</li>
                        <li>Received the goods order and conditions</li>
                    </ol>

                </div>
                <div class="col-md-6 text-right">
                    <strong></strong>

                </div>
            </div>
            <div class="d-flex" style="justify-content: space-between;">
                <div>
                    E.& O.E
                </div>
                <div>
                    Checked By
                </div>
                <div>
                    <strong>
                        Authorized Signatory
                    </strong>
                </div>
            </div>



        </div>
    </div>
</div>



<div class="modal fade" id="EDIT_Challan_Product" >
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



<div class="modal fade" id="Add_return_product" >
    <div class="modal-dialog">
        <form method="POST" id="frmMain">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sale Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Select Product</label>
                        <select class="form-control" name="product" id="product">
                            <option value="">Select Product</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT a.*,b.product_id,c.name as product_name,b.qty from stock_outward_mst a JOIN  stock_outward_det b on a.id=b.stock_outward_mst_id join products c on b.product_id=c.id where a.id=?");
                            $stmt->bind_param('i', $res['challan_id']);
                            $stmt->execute();
                            $products = $stmt->get_result();
                            while ($row = $products->fetch_assoc()) {
                                echo '<option value="' . $row['product_id'] . '" data-qty="' . $row['qty'] . '">' . $row['product_name'] . ' QTY : ' . $row['qty'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="validationCustom03" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="p_qty" name="p_qty" required>

                    </div>

                    <div class="col-md-12 mt-3">
                        <input type="text" name="t_password" id="t_password" placeholder="Enter Password" class="form-control" required>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_btn" onclick="saveBill();">Save
                        changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="Delete_Challan_Product" >
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


<div class="modal fade" id="EditViewModal" >
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
    $(document).on("click", ".EditView", function() {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-sale-return-log.php',
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