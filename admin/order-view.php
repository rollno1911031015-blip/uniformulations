<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT a.*,b.name,b.mobile,b.address,b.state,b.city,b.pincode,b.gst,c.company_name from order_mst a join lead b on a.customer_id=b.id join company c on b.company_id=c.id where a.id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (isset($_POST['Btn_delete'])) {





    $stmt = $mysqli->prepare("DELETE from order_det where id=?");
    $stmt->bind_param('i', $_POST['d_id']);
    $stmt->execute();

    alert('Delete Successfully.', 'success');
    redirect('?id=' . $_GET['id'] . '');
}

if (isset($_POST['product_list'])) {

    $error = '';
    $p = json_decode($_POST['product_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';




    foreach ($p as $item) {
        if (empty($item['id'])) continue;


        $stmt = $mysqli->prepare("SELECT * from  products where id=?;");
        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        if ($product) {
            $gst_per = "";
            $gst_amt = 0;
            if ($item['gst'] == "IGST") {
                $gst_per = $product['igst'];
                $gst_amt = $item['price'] / 100 * $gst_per;
            } else {
                $gst_per = $product['cgst'];
                $gst_amt = $item['price'] / 100 * $gst_per;
            }

            $stmt = $mysqli->prepare("INSERT into  order_det(mst_id,product_id,qty,price,discount,gst_type,gst,gst_per,expiry_date,purchase_rate,batch_no) values(?,?,?,?,?,?,?,?,?,?,?);");
            $stmt->bind_param("iiiddsddsds",  $_GET['id'], $item['id'], $item['qty'], $item['price'], $item['discount'], $item['gst'], $gst_amt, $gst_per, $item['expiry_date'], $item['purchase_rate'], $item['batch_no']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$per = permissions(9, $mysqli, $user);

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
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Order View</h4>
        </div>

    </div>
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
                        <?php
                        if ($per['edit'] == 1) {


                            if (empty($_GET['edit'])) {
                        ?>
                                <a class="btn btn-dark float-end btn-sm" href="?id=<?= $_GET['id'] ?>&edit=editable"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            <?php
                            } else {
                            ?>
                                <a class="btn btn-dark float-end btn-sm" href="?id=<?= $_GET['id'] ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <?php

                            }
                        }
                        ?>

                    </div>
                </div>

                <div class="card-body" id="printorder">




                    <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                        <div>
                            <h3>DM PHARMA (GLOBAL) PRIVATE LIMITED</h3>
                            <p>Office : SCO-176, 2ND Floor, Sector-38c Chandigarh
                                <br>
                                Godown Khewat no 313, Khatuni No 441 Khasra No 30/27/1, Village Maloya CHD <br>
                                Phone : 7717304942

                            </p>


                        </div>


                        <div>
                            <div style="text-align: right;">
                                GST No : 04AJCD0060D1ZC <br>
                                State Code : 04
                                <br>
                                LF20B2024CH000148 & WLF21B2024CH000149<BR>
                                FASSAI No : 13022999000046 <br>
                                E-Mail : paxfomation@dmpharma.org
                            </div>
                        </div>
                    </div>


                    <div style="display: flex; justify-content: space-between;">
                        <div class="" style="border: solid 1px; width: 50%; padding: 8px;">
                            To <br>
                            
                            M/s <?= $res['company_name'] ?><br>
                             
                            <?= $res['address'] ?>, <?= $res['city'] ?>, <?= $res['state'] ?>, <?= $res['pincode'] ?><br>

                            Contact: <?= $res['mobile'] ?>
                        </div>
                        <div class="" style="border: solid 1px; width: 50%; padding: 8px;">
                            Bill No: <?= $res['id'] ?><br>


                            Dated : <?= $res['created_at'] ?> <br>
                            GST: <?= $res['gst'] ?><br>

                        </div>
                        <div></div>

                    </div>
                    <div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Particulars</th>
                                    <th scope="col">HSN Code</th>

                                    <th scope="col">Pack</th>
                                    <th scope="col">Batch No</th>
                                    <th scope="col">Exp.</th>


                                    <th scope="col">MRP</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Rate</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">IGST % </th>
                                    <th scope="col">GST </th>
                                    <th scope="col">Dis % </th>
                                    <?php
                                    if (($res['status'] == "pending"))
                                        echo '<th scope="col">Pending Qty</th>';
                                    ?>




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
                                $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,a.price as price,b.uom,b.brand,b.hsn_code,b.packing,b.igst,b.price as mrp from order_det a JOIN products b on a.product_id=b.id where a.mst_id=?");
                                $stmt->bind_param('i', $res['id']);
                                $stmt->execute();
                                $products = $stmt->get_result();
                                $gst_total=0;
                                $sub_total=0;
                                while ($row = $products->fetch_assoc()) {
                                    $gst_amt=0;
                                    $gst_amt =  ($row['price'] * $row['qty'])*$row['gst_per']/100;
                                    $gst_total += $gst_amt;
                                    $sub_total += $row['price'] * $row['qty'];
                                    echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        
                                                        <td>' . $row['product'] . '</td>
                                                        <td>' . $row['hsn_code'] . '</td>
                                                        <td>' . $row['packing'] . '</td>
                                                        <td>' . $row['batch_no'] . '</td>
                                                        <td>' . $row['expiry_date'] . '</td>
                                                       
                                                        ';

                                    if ($res['status'] == "pending") {
                                        echo '<td>' . $row['qty'] - $row['out_qty'] . '</td>';
                                    }



                                    echo  '<td>' . $row['mrp'] . '</td>
                                                        <td>' . $row['qty'] . '</td>
                                                       
                                                        <td>' . ($row['price']) - (($row['price']) / 100 * $row['discount']) . '</td>
                                                        
                                                         <td>' . ($row['price'] * $row['qty']) - (($row['price'] * $row['qty']) / 100 * $row['discount']) . '</td>
                                                           <td>' . $row['gst_per'] . '</td>
                                                           <td>' . $gst_amt . '</td>
                                                           <td>' . $row['discount'] . '</td>
                                                        ';

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
                            <tbody>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>Sub Total</th>
                                    <th><?=$sub_total?></th>
                                </tr>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>GST</th>
                                    <th><?=$gst_total?></th>
                                </tr>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>Grand Total</th>
                                    <th><?=$gst_total+$sub_total?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Gross Amt.</th>
                                    <th>Scm Amt.</th>
                                    <th>Disc. Amt.</th>
                                    <th>Taxable Amt.</th>
                                    <th>GST %</th>
                                    <th>CGST Amt.</th>
                                    <th>SGST Amt.</th>
                                    <th>IGST Amt.</th>
                                    <th>Net Amt.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>

                        </table>

                    </div> -->
                    <div class="d-flex mt-4 justify-content-between">
                        <div>
                            <p><b><u><i>Terms & Conditions</i></u></b></p>
                            <ol style="list-style:number;">
                                <li>All disputes are subject to chandigarh jurisdiction.</li>
                                <li>We declare that this invoice shows the actual price of the goods described and that all particulars are true.</li>
                                <li>No responsibility for damage/pilferage in transit. </li>
                                <li>Goods once sold will be not taken back.</li>
                                <li>Interest @18% will charged after due date.</li>

                            </ol>
                        </div>
                        <div>
                            <h6 class="float-end">For DM PHARMA (GLOBAL) PRIVATE LIMITED</h6>

                            <p class="mt-5">Authorized Signatory</p>
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
                    <input id="eprod_id" name="prod_id" type="hidden">
                    <div class="">
                        <input type="number" id="eqty" name="qty" required class="form-control"> <br>
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
    <div class="modal-dialog modal-xl">
        <form method="POST" id="frmMain">
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
                                <label>Product</label> <br>
                                <select name="prod_id" id="prod_id" class="form-control" required>
                                    <option value="">--Select Product--</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from products where is_active=1");
                                    //$stmt->bind_param('i', $_GET['loc_id']);
                                    $stmt->execute();
                                    $categories = $stmt->get_result();
                                    while ($row = $categories->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" data-price="' . $row['price'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>


                                </select>

                            </td>
                            <td>
                                <label>Batch No</label>
                                <select name="batch_no" id="batch_no" class="form-control" required>
                                    <option value="">--Select Batch No--</option>



                                </select>

                            </td>
                            <td>
                                <label for="">Expiry</label>
                                <input type="" step="0.01" name="expiry_date" id="expiry_date" placeholder="" class="form-control" required disabled>
                            </td>
                            <td>
                                <label for="">Purchase Rate</label>
                                <input type="number" step="0.01" name="purchase_rate" id="purchase_rate" placeholder="" class="form-control" required disabled>
                            </td>
                            <td>
                                <label for="">Price</label>
                                <input type="number" step="0.01" name="price" id="price" placeholder="Enter Price" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="">Qty</label>
                                <input type="number" name="qty" value="1" id="qty" placeholder="Enter Quantity" class="form-control" required>
                            </td>
                            <td>
                                <label for="">Discount</label>
                                <input type="number" step="0.01" name="discount" id="discount" placeholder="Enter Discount" class="form-control" required>
                            </td>

                            <td>
                                <label for="">Select GST</label>
                                <select name="gst" id="gst" class="form-control" required>
                                    <option value="IGST">IGST</option>
                                    <option value="CGST/SGST">CGST/SGST</option>
                                </select>
                            </td>
                            <td>
                                <label for="">Total</label>
                                <input disabled id="total" class="form-control" required>
                            </td>


                            <td>

                                <button type="button" onclick="addItem()" class="btn btn-sm btn-outline-success">ADD</button>
                            </td>
                        </tr>
                    </table>
                    <table class="table">
                        <thead style="color: black;">

                            <tr>
                                <th>S.No</th>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Expiry Date</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>GST</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productList">

                        </tbody>
                    </table>

                </div>
                <input type="hidden" name="product_list" id="product_list" value="">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>
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



    var price = 0;
    var qty = 0;
    var discount = 0;
    var total = 0;
    var purchase_rate = 0;
    $("#prod_id").on("change", function() {
        price = $(this).find(':selected').data('price')
        discount = $("#discount").val()
        qty = $("#qty").val()
        qty = $("#qty").val()


        $("#price").val(price)
        $("#total").val(price * qty)


        $.ajax({
            method: 'POST',
            url: 'ajax/get-current-stock-order.php',
            data: {
                id: $(this).val()
            },
            success: function(data) {

                data = JSON.parse(data);
                var html = "";
                var table = "";

                html += "<option value=''>Select Batch</option>";
                data.forEach(element => {
                    html += `<option data-stock="${element.current_stock}" data-batch_no="${element.batch_no}" data-batch_no_id="${element.id}" data-expiry_date="${element.expiry_date}"  data-purchase_rate="${element.purchase_rate}" data-sale_rate="${sale_rate}" value="${element.id}">${element.batch_no} (Stock : ${element.current_stock})</option>`;

                    table += `<tr>
                                <td><input type="radio" name="checkBatchNo" class="checkBatchNo" value="${element.id}" data-expiry_date="${element.expiry_date}"  data-purchase_rate="${element.purchase_rate}"> </td> 
                                <td>${element.batch_no} </td> 
                                <td>${element.expiry_date} </td> 
                                <td>${element.current_stock} </td> 
                                <td>${element.purchase_rate} </td> 
                                <td>${price} </td> 
                                <td>${element.sale_rate} </td> 
                             </tr>`;

                });

                $("#batch_no").html(html);

                $("#viewBatchList").html(table);
                $("#ViewBatch").modal("show")

            }
        });


        CalculateDiscount(price, qty, discount);
    });

    $("#batch_no").on("change", function() {

        purchase_rate = $(this).find(':selected').data('purchase_rate')
        $("#purchase_rate").val(purchase_rate)

        $("#expiry_date").val($(this).find(':selected').data('expiry_date'))
    });

    $(document).on("click", ".checkBatchNo", function() {
        purchase_rate = $(this).data('purchase_rate')
        $("#purchase_rate").val(purchase_rate)
        $("#expiry_date").val($(this).data('expiry_date'))
        $("#batch_no").val($(this).val())
    })

    $("#discount").on("keyup", function() {

        qty = $("#qty").val();
        discount = $(this).val();
        CalculateDiscount(price, qty, discount);
    })


    $("#qty").on("keyup", function() {

        discount = $("#discount").val();
        qty = $(this).val();
        CalculateDiscount(price, qty, discount);
    })

    function CalculateDiscount(price, qty, discount) {
        var discount = (price * qty) / 100 * discount;
        total = (price * qty) - discount;
        $("#total").val(total)

    }


    $("#discount").on("blur keydown", function(event) {
        if (event.key === "Enter") {
            addItem();
            $("#prod_id").focus();
        }
    });

    var products = new Array();
    var row;
    var sno = 1;

    function addItem() {
        var id = $("#batch_no").find(':selected').data('batch_no_id')
        var prod_name = $('#prod_id option:selected').text()
        var qty = parseInt($('#qty').val())
        var price = $('#price').val()
        var discount = $('#discount').val()
        var gst = $('#gst').val()
        var expiry_date = $('#expiry_date').val()
        var purchase_rate = $('#purchase_rate').val()
        var batch_no = $("#batch_no").find(':selected').data('batch_no')


        if (qty <= 0) {
            alert('Qty should be more than zero.');
            return;
        }
        if (id <= 0) {
            alert('Select product.');
            return;
        }


        var ex_p = products.filter((item, index) => item.id == id)

        if (products[id] != undefined) {
            alert('This product already added.');
            return;
        }

        row = `<tr class="prod${id}">
                <td>${sno}</td>
                 <td> ${prod_name}</td>
                 <td> ${batch_no}</td>
                 <td> ${expiry_date}</td>
                 <td class="qty${id}">${qty}</td>
                 <td>${price}</td>
                 <td>${discount}</td>
                 <td>${gst}</td>
                 <td>  
                 <button onclick="removeItem(${id})" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button>
                   </td></tr>`;
        $('#productList').append(row);

        var prod = {
            id: id,
            qty: qty,
            price: price,
            discount: discount,
            gst: gst,
            expiry_date: expiry_date,
            purchase_rate: purchase_rate,
            batch_no: batch_no,

        }
        products[id] = prod;

        $('#batch_no').val('')
        $('#prod_id').val('')
        $('#qty').val(1)
    }

    function removeItem(id) {
        id = parseInt(id);

        $('#productList').find('.prod' + id).remove();
        const index = products.indexOf(id)
        if (index > -1) products.splice(index, 1);
        delete products[id]
        console.log(products)
    }

    function editItem(id, qty) {
        $("#edititemid").val(id)
        $("#edititemqty").val(qty)
        $("#modalId").modal("show");

    }

    function saveItem() {
        id = $("#edititemid").val()
        qty = $("#edititemqty").val()


        products[id].qty = qty;


        $('#productList').find('.qty' + id).html(qty);
        $("#modalId").modal("hide");
    }

    function saveBill() {
        $('#product_list').val(JSON.stringify(products));
        $('#frmMain').submit()
    }
</script>