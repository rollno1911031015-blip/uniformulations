<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';

    if (empty($_POST['customer_id']))
        $error = 'Customer required.';



    if (empty($_POST['order_status']))
        $error = 'Select Quote/Order required.';

    // $stmt = $mysqli->prepare("SELECT * from  voucher_series where id=?;");
    // $stmt->bind_param("i", $_POST['vehicle_no']);
    // $stmt->execute();
    // $res = $stmt->get_result()->fetch_assoc();
    // $voucher_series = $res['name'] . '_' . $res['no'];



    if ($error == '') {
        try {

            $stmt = $mysqli->prepare("INSERT into order_mst(customer_id,description,user_id,order_date,delivery_date,order_id,mode_of_transport,order_status) values(?,?,?,?,?,?,?,?);");
            $stmt->bind_param("isisssss", $_POST['customer_id'],   $_POST['description'], $user['id'], $_POST['order_date'], $_POST['delivery_date'], $_POST['order_id'], $_POST['mode_of_transport'], $_POST['order_status']);
            $stmt->execute();
            $order_id = $mysqli->insert_id;
            $stmt->close();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;


                $stmt = $mysqli->prepare("SELECT * from  products where id=?;");
                $stmt->bind_param("i", $item['prod_id']);
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
                    $stmt->bind_param("iiiddsddsds",  $order_id, $item['prod_id'], $item['qty'], $item['price'], $item['discount'], $item['gst'], $gst_amt, $gst_per, $item['expiry_date'], $item['purchase_rate'], $item['batch_no']);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // $stmt = $mysqli->prepare("UPDATE  voucher_series set no=no+1 where id=?;");
            // $stmt->bind_param("i", $_POST['vehicle_no']);
            // $stmt->execute();

            alert('Save successfully.', 'success');
            redirect('orders?status=processing');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('orders?status=processing');
        }
    } else {

        alert($error, 'error', 'ERROR');
        redirect('orders?status=processing');
    }
}




$res1 = [];
$ids = explode(", ", $user['division']);
foreach ($ids as $key => $value) {
    $stmt1 = $mysqli->prepare("SELECT a.*,b.company_name FROM lead a  join company b on a.company_id=b.id WHERE FIND_IN_SET(?, a.division) and status=4");
    $stmt1->bind_param("i", $value);
    $stmt1->execute();
    $result = $stmt1->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $res1[] = $row; // Append each row to the result array
        }
    }
}





$json = json_encode($res1);
$array = json_decode($json, true);

$array = array_filter($array);

// Step 3: Optionally remove duplicates (based on 'id' or another unique field)
$uniqueArray = [];
foreach ($array as $item) {
    if (!isset($uniqueArray[$item['id']])) {
        $uniqueArray[$item['id']] = $item;
    }
}

// Step 4: Re-encode to JSON
$cleanedJson = (array_values($uniqueArray));



?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Generate Order</h4>
        </div>

    </div>
    <div class="card-body">
        <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">
            <div class=" table-responsive">
                <div class="row mt-3">
                    <div class="form-group col-md-4">
                        <label>Select Division</label>
                        <select name="division_id" id="division_id" class="form-control" required>
                            <option value="">--Select Division--</option>
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
                    </div>


                    <div class="form-group col-md-4">
                        <label>Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">--Select Customer--</option>
                            <?php
                            foreach ($cleanedJson as $key => $value) {
                                echo '<option value="' . $value['id'] . '">' . $value['company_name'] . ' (' . $value['name'] . ') / (' . $value['mobile'] . ')</option>';
                            }
                            ?>
                        </select>
                    </div>



                    <div class="form-group col-md-4">
                        <label for="">Order Date</label>
                        <input type="date" name="order_date" id="order_date" class="form-control">

                    </div>

                    <div class="form-group col-md-4 mt-3">
                        <label for="">Expected Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                    </div>

                    <div class="form-group col-md-4 mt-3 d-none">
                        <label for="">Order No</label>
                        <input type="" name="order_id" id="order_id" class="form-control">
                    </div>

                    <div class="form-group col-md-4 mt-3">
                        <label for="">Mode of Transport</label>
                        <select name="mode_of_transport" id="mode_of_transport" class="form-control">
                            <option value="">Select</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT * from mode_of_transport");
                            //$stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $categories = $stmt->get_result();
                            while ($row = $categories->fetch_assoc()) {
                                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group col-md-4 mt-3 d-none">
                        <label for="">Voucher Series</label>
                        <select name="vehicle_no" id="vehicle_no" class="form-control">
                            <option value="">--Select--</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT * from voucher_series");
                            //$stmt->bind_param('i', $_GET['loc_id']);
                            $stmt->execute();
                            $categories = $stmt->get_result();
                            while ($row = $categories->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>


                    <div class="form-group col-md-4 mt-3">
                        <label for="">Quote/Order</label>
                        <select name="order_status" id="order_status" class="form-control" required>
                            <option value="">--Select--</option>
                            <option value="PI">Quote</option>
                            <option value="Order">Order</option>

                        </select>
                    </div>





                    <div class="col-md-12 mt-2">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                    </div>
                </div>
                <hr>

                <div class="row m-4">
                    <div class="col-md-3">
                        <div class="">
                            <input type="file" id="file" class="form-control">

                        </div>



                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info" type="button" id="BtnUpload"> Upload</button>

                    </div>
                    <div class="col-md">
                        <a class="btn btn-dark btn-sm" href="import-order-list.csv" download="import-order-list.csv">Download Sample File</a>
                    </div>

                </div>
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
                            <label for="">Old Price</label>
                            <input type="number" id="old_price" placeholder="Old Price" disabled class="form-control" required>
                        </td>
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
                            <th>Old Price</th>
                            <th>Discount</th>
                            <th>GST</th>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="saveItem()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="ViewBatch">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    View Batch
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
                            <th>#</th>
                            <th>Batch No</th>
                            <th>Expiry</th>
                            <th>Current Stock</th>
                            <th>Purchase Rate</th>
                            <th>MRP</th>
                            <th>Sale Rate</th>
                        </tr>
                    </thead>
                    <tbody id="viewBatchList">

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
                <button type="button" data-bs-dismiss="modal" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>


<?php include("../layouts/footer.php"); ?>


<script>
    $(document).ready(function() {

        $("#prod_id").select2();
    });
    $("#customer_id").select2();
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
        var customer_id = $("#customer_id").val();
        if (customer_id <= 0) {
            alert("Select customer")
            $(this).val("")
            return;

        }
        $.ajax({
            method: 'POST',
            url: 'ajax/get-current-stock-order.php',
            data: {
                id: $(this).val(),
                customer_id: customer_id
            },
            success: function(data) {

                data = JSON.parse(data);
                var html = "";
                var table = "";

                html += "<option value=''>Select Batch</option>";
                data.forEach(element => {
                    $("#old_price").val(element.old_price)
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

    $("#price").on("keyup", function() {

        discount = $("#discount").val();
        qty = $("#qty").val();
        price = $(this).val();
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
        var prod_id = $("#prod_id").val()
        var prod_name = $('#prod_id option:selected').text()
        var qty = parseInt($('#qty').val())
        var price = $('#price').val()
        var discount = $('#discount').val()
        var gst = $('#gst').val()
        var expiry_date = $('#expiry_date').val()
        var purchase_rate = $('#purchase_rate').val()
        var batch_no = $("#batch_no").find(':selected').data('batch_no')
        var old_price = $('#old_price').val()

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

        var old_price = $("#old_price").val()
        row = `<tr class="prod${id}">
                <td>${sno}</td>
                 <td> ${prod_name}</td>
                 <td> ${batch_no}</td>
                 <td> ${expiry_date}</td>
                 <td class="qty${id}">${qty}</td>
                 <td>${price}</td>
                 <td>${old_price}</td>
                 <td>${discount}</td>
                 <td>${gst}</td>
                 <td>  
                 <button onclick="removeItem(${id})" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button>
                  <button onclick="editItem(${id} ,${qty})" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>`;
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
            prod_id: prod_id

        }
        products[id] = prod;

        $('#batch_no').val('')
        $('#prod_id').val('')
        $('#qty').val(1)
    }




    $("#BtnUpload").on("click", function() {
        $("#BtnUpload").attr("disabled", "disabled");
        let fileInput = document.getElementById('file');

        let file = fileInput.files[0];

        if (file) {
            // Create a new FormData object
            let formData = new FormData();
            formData.append('file', file);
            // Make an AJAX request
            $.ajax({
                method: 'POST',
                url: 'ajax/upload-order-list.php',
                data: formData,
                processData: false, // Important! Prevent jQuery from processing the data
                contentType: false, // Important! Set contentType to false so jQuery doesn't add a content type header
                success: function(data) {
                    try {
                        data = JSON.parse(data);
                        if (data.error) {
                            alert(data.error); // Display the error message in an alert dialog
                        } else {
                            var sno = 1;
                            // Process the success response
                            data.forEach(element => {
                                row = '<tr class="prod' + element.id + '"><td>' + (sno++) + '</td><td>' + element.name + '</td><td>' + element.batch_no + '</td><td>' + element.expiry_date + '</td><td class="qty' + element.id + '">' + element.qty +
                                    '<td>' + element.price + '</td><td>' + element.discount + '</td><td>' + element.gst + '</td> <td>  <button onclick="removeItem(' + element.id +
                                    ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
                                    element.id + ',' + element.qty +
                                    ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
                                $('#productList').append(row);
                                var prod = {
                                    id: element.id,
                                    qty: element.qty,
                                    price: element.price,
                                    discount: element.discount,
                                    gst: "IGST",
                                    expiry_date: element.expiry_date,
                                    purchase_rate: element.purchase_rate,
                                    batch_no: element.batch_no,
                                    prod_id: element.prod_id,


                                }
                                products[element.id] = prod;
                            });
                        }
                        console.log(products);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error uploading file:', error);
                }
            });
        } else {
            alert("No file selected.");
        }
    });

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

                $("#prod_id").html(data);

            }
        });
    });


    $("#division_id").on("change", function() {
        var id = $(this).val()
        $.ajax({
            method: 'POST',
            url: 'ajax/get-customer-by-division.php',
            data: {
                id: id
            },
            success: function(data) {
                $("#customer_id").html(data)



            }
        });
    })
</script>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable


    });
</script>