<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';
    if (empty($_POST['loc_id']))
        $error = 'Location required.';





    if ($error == '') {
        try {

            $stmt = $mysqli->prepare("SELECT * from po_mst  where id=?;");
            $stmt->bind_param("i", $_POST['po']);

            $stmt->execute();
            $po_mst = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $ids = implode(', ', $_POST['po']);

            $stmt = $mysqli->prepare("UPDATE po_mst set status='Received PO' where id in (" . $ids . ");");
            //  $stmt->bind_param("i", $_POST['po']);
            $stmt->execute();


            if ($po_mst) {




                $stmt = $mysqli->prepare("INSERT into stock_inward_mst(vendor_id,loc_id,invoice,user_id,description,invoice_date,r_material_date,purchase_order_id,mode_of_transport,gr_no,no_of_boxes,supplier_invoice) values(?,?,?,?,?,?,?,?,?,?,?,?);");
                $stmt->bind_param("iisisssissss", $po_mst['vendor_id'],  $_POST['loc_id'], $_POST['invoice'], $user['id'], $_POST['description'], $_POST['invoice_date'], $_POST['material_date'], $_POST['po'], $_POST['mode_of_transport'], $_POST['gr_no'], $_POST['no_of_boxes'], $_POST['supplier_invoice']);
                $stmt->execute();
                $stock_id = $mysqli->insert_id;
                $stmt->close();


                foreach ($p as $item) {
                    if (empty($item['id'])) continue;
                    $stmt = $mysqli->prepare("INSERT into stock_inward_det(stock_mst_id,product_id,qty,price,received_qty,box_qty,purchase_rate,expiry,batch_no) values(?,?,?,?,?,?,?,?,?);");
                    $stmt->bind_param("iiidiidss",  $stock_id, $item['product_id'], $item['qty'], $item['price'], $item['received_qty'], $item['box_qty'], $item['purchase_rate'], $item['expiry'], $item['batch_no']);
                    $stmt->execute();
                    $stmt->close();


                    $stmt = $mysqli->prepare("SELECT * from  current_stock where loc_id=? and prod_id=? and batch_no=?  ;");
                    $stmt->bind_param('iis', $_POST['loc_id'], $item['product_id'], $item['batch_no']);
                    $stmt->execute();
                    $res = $stmt->get_result()->fetch_assoc();

                    if (empty($res)) {
                        $cstock = $mysqli->prepare("INSERT INTO current_stock(loc_id,prod_id,current_stock,batch_no,expiry_date,purchase_rate) VALUES (?,?,?,?,?,?) ;");
                        $cstock->bind_param("iiissd", $_POST['loc_id'], $item['product_id'],   $item['qty'], $item['batch_no'], $item['expiry'], $item['purchase_rate']);
                        $cstock->execute();
                        $cstock->close();
                    } else {
                        $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock+?,purchase_rate=?,expiry_date=? where loc_id =? and prod_id=? and batch_no=?");
                        $cstock->bind_param("idsiss", $item['qty'], $item['purchase_rate'], $item['expiry'], $_POST['loc_id'], $item['product_id'], $item['batch_no']);
                        $cstock->execute();
                        $cstock->close();
                    }
                }
            } else {
                alert('PO not found.', 'error');
                redirect('inward-report?id=' . $stock_id . '');
            }
            alert('Stock added successfully.', 'success');
            redirect('inward-report?id=' . $stock_id . '');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect("inward-stock");
        }
    } else {
        echo $error;
        alert($error, 'error', 'ERROR');
        redirect("inward-stock");
    }
}


?>


<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Inward Stock</h4>

        </div>
        <div class="card-body">


            <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">


                <div class="col-3">
                    <label>Vendor </label>

                    <select class="form-control" name="vendor_id" id="vendor_id">
                        <option value="">Select Vendor</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT a.*,b.company_name as name from  vendor a join company b on a.company_id=b.id ;");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-3">
                    <label>PO</label>
                    <select class="form-control" name="po[]" id="po" multiple>
                        <?php
                        $stmt = $mysqli->prepare("SELECT a.*,b.con_person_name from  po_mst a join vendor b on a.vendor_id=b.id  ;");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '" >' . $row['con_person_name'] . ' (' . $row['id'] . ')</option>';
                        }
                        ?>
                    </select>
                </div>


                <div class="col-3">
                    <label>Mode of Transport</label>
                    <select class="form-control" name="mode_of_transport" id="mode_of_transport">
                        <option value="">---Select---</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from  mode_of_transport;");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-3">
                    <label>GR No</label>
                    <input type="text" name="gr_no" id="gr_no" class="form-control">
                </div>

                <div class="col-3">
                    <label>No of Boxes</label>
                    <input type="text" name="no_of_boxes" id="no_of_boxes" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="">Select Location</label>
                    <select name="loc_id" id="loc_id" class="form-control" required>
                        <option value="">--Select Locations--</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '">' . $row['name'] . ' ' . $row['address'] . '</option>';
                        }
                        ?>
                    </select>
                </div>


                <hr>

                <div class=" table-responsive">
                    <div class="row mt-3">


                        <div class="col-md-3 d-none">
                            <label for="">Invoice No.</label>
                            <input type="text" name="invoice" id="invoice" placeholder="Enter invoice" class="form-control" required>

                        </div>

                        <div class="col-md-3">
                            <label for="">Vendor Invoice No.</label>
                            <input type="text" name="supplier_invoice" id="supplier_invoice" placeholder="Enter supplier invoice no" class="form-control" required>

                        </div>
                        <div class="col-md-3">
                            <label for="">Invoice Date</label>
                            <input type="date" class="form-control" name="invoice_date" id="invoice_date" required>

                        </div>
                        <div class="col-md-3">
                            <label>Receive Material Date</label>
                            <input type="date" class="form-control" name="material_date" id="material_date" required>

                        </div>

                        <br>

                        <div class="col-md-6">
                            <label>Description</label>
                            <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                        </div>
                    </div>
                    <hr>
                    <!-- <table class="table table-bordered">
                        <tr>

                            <td>
                                <select class="form-control" id="brand_id" name="brand">
                                    <option value="">Select Brand</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from brand");
                                    //$stmt->bind_param('i', $_GET['loc_id']);
                                    $stmt->execute();
                                    $categories = $stmt->get_result();
                                    while ($row = $categories->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>



                                </select>

                            </td>

                            <td>
                                <select id="category" name="category" class="form-control">
                                    <option value="">Select Category</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from categories");
                                    //$stmt->bind_param('i', $_GET['loc_id']);
                                    $stmt->execute();
                                    $categories = $stmt->get_result();
                                    while ($row = $categories->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
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

                                <button type="button" onclick="addItem()" class="btn btn-sm btn-outline-success">ADD</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="prod_id" id="prod_id" class="form-control" required>
                                    <option value="">--Select Product--</option>

                                </select>

                            </td>
                            <td>
                                <input type="number" name="qty" id="qty" placeholder="Enter Quantity" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="price" id="price" placeholder="Enter Price" class="form-control" required>
                            </td>
                        </tr>
                    </table> -->
                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>Batch No.</th>
                                <th>Qty</th>
                                <th>Received Qty</th>

                                <th>Price (MRP)</th>
                                <th>Purchase Rate</th>
                                <th>Expiry</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody id="productList">

                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="prod_list" id="prod_list" value="">
                <div class="text-center col-md-12 mt-5">
                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>
        </div>
    </div>
</div>



<?php
include("../layouts/footer.php");
?>


<script>
    // $(document).ready(function() {
    //   //change selectboxes to selectize mode to be searchable
    //   $("#loc_id").select2();
    //   $("#prod_id").select2();
    // });
    document.getElementById("loc_id").onchange = function() {
        document.getElementById("loc_id").setAttribute("readonly", "readonly");

    };


    var products = new Array();
    var row;


    $("#po").on("change", function() {
        products = new Array();
        let id = $(this).val();
        row = "";
        $.ajax({
            method: 'POST',
            url: 'ajax/get-po-product-list.php',
            data: {
                id: id,
            },
            success: function(data) {

                data = JSON.parse(data);

                data.forEach(element => {

                    var id = element.id
                    var product_id = element.product_id

                    var qty = element.qty
                    var price = element.price

                    var purchase_rate = 0;
                    var expiry = '0000-00-00';
                    var batch_no = '';

                    var prod = {
                        id: id,
                        qty: qty,
                        price: price,
                        product_id: product_id,
                        received_qty: qty,
                        box_qty: 1,
                        purchase_rate: purchase_rate,
                        expiry: expiry,
                        batch_no: batch_no,

                    }


                    products[id] = prod;


                    row += `<tr class="prod${element.id}">
                            <td> ${element.product_name}</td>
                            <td><input type="text" class="form-control batch_no" data-id="${element.id}"  value="${batch_no}"  style="width: 100px"></td> 
                            <td class="qty${element.id}">${element.qty}</td>
                            <td><input type="number" class="form-control received_qty" data-id="${element.id}"  value="${element.qty}"  style="width: 100px"></td> 
                         
                            <td class="price' + element.id + '">${element.price}</td>

                            <td><input type="number" class="form-control purchase_rate" data-id="${element.id}"  value="${purchase_rate}"  style="width: 100px"></td> 
                            <td><input type="month"  class="form-control expiry" data-id="${element.id}"  value="${expiry}"  style="width: 100px"></td> 



                            <td>  <button onclick="removeItem(${element.id})" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(${element.id}','${element.qty} ',' ${element.price})" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>`;

                });
                $('#productList').html(row);
                console.log(products);




            }
        });
    })





    // function addItem() {
    //     var id = $('#prod_id').val()
    //     var prod_name = $('#prod_id option:selected').text()
    //     var qty = $('#qty').val()
    //     var price = $('#price').val()



    //     if (qty <= 0) {
    //         alert('Qty should be more than zero.');
    //         return;
    //     }
    //     if (id <= 0) {
    //         alert('Select product.');
    //         return;
    //     }
    //     var ex_p = products.filter((item, index) => item.id == id)
    //     console.log(products[id])
    //     if (products[id] != undefined) {
    //         alert('This product already added.');
    //         return;
    //     }
    //     row = '<tr class="prod' + id + '"><td>' + prod_name + '</td>' + '<td class="qty' + id + '">' + qty + '</td><td class="price' + id + '">' + price + '</td>' +
    //         '<td>  <button onclick="removeItem(' + id +
    //         ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
    //         id + ',' + qty + ',' + price + ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
    //     $('#productList').append(row);
    //     //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
    //     var prod = {
    //         id: id,
    //         qty: qty,
    //         price: price,

    //     }
    //     products[id] = prod;
    //     console.log(products);

    //     $('#prod_id').val('')
    //     $('#qty').val('')
    // }

    function removeItem(id) {
        $('#productList').find('.prod' + id).remove();
        const index = products.indexOf(id)
        if (index > -1) products.splice(index, 1);
        delete products[id]

    }

    function editItem(id, qty, price) {

        $("#edititemid").val(id)
        $("#edititemqty").val(qty)
        $("#editprice").val(price)

        $("#modalId").modal("show");

    }

    function saveItem() {
        id = $("#edititemid").val()
        qty = $("#edititemqty").val()
        price = $("#editprice").val()


        products[id].qty = parseInt(qty);
        products[id].price = parseInt(price);


        $('#productList').find('.qty' + id).html(qty);
        $('#productList').find('.price' + id).html(price);
        $("#modalId").modal("hide");
    }



    $(document).on("keyup", ".received_qty", function() {

        newReceivedQty = parseInt($(this).val());
        id = $(this).data("id");
        products[id].received_qty = newReceivedQty;


    })

    $(document).on("keyup", ".purchase_rate", function() {

        purchase_rate = parseInt($(this).val());

        id = $(this).data("id");
        products[id].purchase_rate = purchase_rate;
        console.log(products)

    })
    $(document).on("change", ".expiry", function() {

        expiry = ($(this).val());

        id = $(this).data("id");
        const year = expiry.slice(0, 4); // First 4 characters for the year
        const month = expiry.slice(5, 7);
        products[id].expiry = month + " - " + year;



    })




    $(document).on("keyup", ".batch_no", function() {

        batch_no = ($(this).val());
        id = $(this).data("id");
        products[id].batch_no = batch_no;


    })

    $(document).on("keyup", ".box_qty", function() {

        newBoxQty = parseInt($(this).val());
        id = $(this).data("id");
        products[id].box_qty = newBoxQty;
        console.log(products)

    })

    function saveBill() {
        $('#prod_list').val(JSON.stringify(products));
        $('#frmMain').submit()
    }

    function editInvoice(id) {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-invoice.php',
            data: {
                id: id
            },
            success: function(data) {
                console.log(data)
                if (data != 'error') {
                    data = JSON.parse(data);
                    console.log(data)
                    $('#id').val(data.id);
                    $('#invoice_no').val(data.invoice_no);
                    $('#invoice_date').val(data.invoice_date);
                    $('#invoice_amount').val(data.invoice_amount);
                    $('#editContact').modal('show');
                }


            }
        });
    }


    $("#vendor_id").on("change", function() {
        var id = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-po.php',
            data: {
                vendor_id: id
            },
            success: function(data) {

                $("#po").html(data);
            }
        });
    });

    // $("#category").on("change", function() {
    //     let name = $(this).val();
    //     $.ajax({
    //         method: 'POST',
    //         url: 'ajax/get-subcategory.php',
    //         data: {
    //             name: name
    //         },
    //         success: function(data) {

    //             $("#subcategory").html(data);

    //         }
    //     });
    // });
    // $("#subcategory").on("change", function() {
    //     let subcategory = $(this).val();
    //     $.ajax({
    //         method: 'POST',
    //         url: 'ajax/get-product-list.php',
    //         data: {
    //             subcategory: subcategory
    //         },
    //         success: function(data) {

    //             $("#prod_id").html(data);

    //         }
    //     });
    // });
</script>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Edit Qty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edititemid" class="form-control">
                <input id="edititemqty" class="form-control">
                <input id="editprice" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="saveItem()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        $("#po").select2();

    });
</script>