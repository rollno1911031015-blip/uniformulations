<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT a.*,b.name,b.mobile,b.address,b.state,b.city,b.pincode,b.gst from repair_mst a join lead b on a.customer_id=b.id where a.id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();


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

    <div class="card-body">

        <div class="row">
            <div class="card col-lg-12">
                <div class="card-header">
                    <h4 class="pull-left">Order View</h4>
                    <div class="pull-right">
                        <button class="noPrint btn bg-white btn-light mx-1px text-95" onclick="printcontent()" data-title="Print">
                            <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                            Print
                        </button>

                    </div>
                </div>

                <div class="card-body" id="printorder">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            GSTIN : GST <br>
                            ORDER CHALLAN
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
                            To <br>

                            M/s <?= $res['name'] ?><br>
                            <?= $res['address'] ?>, <?= $res['city'] ?>, <?= $res['state'] ?>, <?= $res['pincode'] ?><br>
                            GST: <?= $res['gst'] ?><br>
                            Contact: <?= $res['mobile'] ?>
                        </div>
                        <div class="col-md" style="border: solid;">
                            Challan No: <?= $res['id'] ?><br>


                            Created Date : <?= $res['created_at'] ?>

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

                                    <th scope="col" class="buttons">UOM</th>


                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,b.price as price,b.uom,b.brand from repair_det a JOIN products b on a.product_id=b.id where a.mst_id=?");
                                $stmt->bind_param('i', $res['id']);
                                $stmt->execute();
                                $products = $stmt->get_result();
                                while ($row = $products->fetch_assoc()) {
                                    echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        <td>' . $row['brand'] . '</td>
                                                        <td>' . $row['product'] . '</td>
                                                       
                                                        <td>' . $row['qty'] . '</td>
                                                       
                                                        <td class="buttons">' . $row['uom'] . '</td>
                                                      
                                                      
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