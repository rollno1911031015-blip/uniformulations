<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';

    if (empty($_POST['vendor_id']))
        $error = 'Vendor required.';


    if ($error == '') {
        try {

            $stmt = $mysqli->prepare("INSERT into po_mst(vendor_id,description,user_id,status,loc_id,delivery_date,transport_mode,type) values(?,?,?,'Generate PO',?,?,?,?);");
            $stmt->bind_param("isiisss", $_POST['vendor_id'],   $_POST['description'], $user['id'], $_POST['loc_id'], $_POST['delivery_date'], $_POST['transport_date'], $_POST['type']);
            $stmt->execute();
            $stock_id = $mysqli->insert_id;
            $stmt->close();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into  po_det(po_id,product_id,qty,price,type) values(?,?,?,?,?);");
                $stmt->bind_param("iiids",  $stock_id, $item['id'], $item['qty'], $item['price'], $item['type']);
                $stmt->execute();
                $stmt->close();
            }

            alert('Save successfully.', 'success');
            redirect('generated-po');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('generated-po');
        }
    } else {

        alert($error, 'error', 'ERROR');
        redirect('generated-po');
    }
}

?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Generate PO</h4>
        </div>

    </div>
    <div class="card-body">
        <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">
            <div class=" table-responsive">


                <div class="row">
                    <div class="col-md-4">
                        <div class="">
                            <label>Requirement</label>
                            <select name="requirement_id" id="requirement_id" class="form-control" required>
                                <option value="">--Select Requirement--</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  requirement_mst  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '" >' . $row['name'] .  '</option>';
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <div class="col-md-4 d-none">
                        <div class="">
                            <label>Type</label>
                            <select name="type" class="form-control" required>

                                <option value="FP">FP</option>
                                <option value="RP">RP</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="row mt-3">

                    <div class="form-group col-md-3">
                        <label>Location</label>
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

                    <div class="form-group col-md-3">
                        <label>Select Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control" required>
                            <option value="">--Select Vendor--</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT a.*,b.company_name as name from  vendor a JOIN company b on a.company_id=b.id  ;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '" > ' . $row['name'] .  '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="">Transport Mode</label>
                        <select id="transport_mode" name="transport_mode" class="form-control">
                            <option value="">Select</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT *  from  mode_of_transport  ;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['name'] . '" >' . $row['name'] .  ' </option>';
                            }
                            ?>
                        </select>
                    </div>




                    <div class="col-md-6">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                    </div>
                </div>
                <hr>
                <table class="table table-bordered">
                    <tr>
                        <td style="width:100px;">

                            <label>Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="FP">FP</option>
                                <option value="RP">RP</option>
                            </select>

                        </td>
                        <td>
                            <select name="prod_id" id="prod_id" class="form-control" required>
                                <option value="">--Select Product--</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  products  where is_active=1 ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '" data-price="' . $row['price'] . '" > ' . $row['name'] .  '</option>';
                                }
                                ?>


                            </select>

                        </td>
                        <td>
                            <input type="number" name="qty" id="qty" placeholder="Enter Quantity" class="form-control" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price" id="price" placeholder="Enter Price" class="form-control" required>
                        </td>
                        <td>
                            <input type="number"   id="old_price" placeholder="Old Price" disabled class="form-control" required>
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
                            <th>Type</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Old Price</th>
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
                <input id="edit_item_price" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="saveItem()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>



<?php include("../layouts/footer.php"); ?>




<script>
    // $(document).ready(function() {
    //   //change selectboxes to selectize mode to be searchable
    //   $("#loc_id").select2();

    // });
    $("#prod_id").select2();
    $("#vendor_id").select2();

    $("#prod_id").on("change", function() {
        $("#price").val($(this).find(':selected').data('price'))
    });

    var products = new Array();;
    var row;
    var sno = 1;


    $("#prod_id").on("change", function() {
        var id = $(this).val()
        var vendor_id = $('#vendor_id').val()
        var old_price = 0;
        if (vendor_id <= 0) {
            alert('Select Vendor.');
            $("#prod_id").val("")
            return;
        }
        $.ajax({
            method: 'POST',
            url: 'ajax/get-po-old-price.php',
            data: {
                product_id: id,
                vendor_id: vendor_id
            },
            success: function(data) {
                old_price = data;
                $("#old_price").val(old_price)

            }
        });
    })

    function addItem() {
        var id = $('#prod_id').val()
        var vendor_id = $('#vendor_id').val()
        var prod_name = $('#prod_id option:selected').text()
        var qty = $('#qty').val()
        var price = $('#price').val()
        var type = $('#type').val()



        if (qty <= 0) {
            alert('Qty should be more than zero.');
            return;
        }
        if (id <= 0) {
            alert('Select product.');
            return;
        }
        if (vendor_id <= 0) {
            alert('Select Vendor.');
            return;
        }

        if (price <= 0) {
            alert('Enter Price.');
            return;
        }



        var old_price=$("#old_price").val()

        var ex_p = products.filter((item, index) => item.id == id)
        console.log(products[id])
        if (products[id] != undefined) {
            alert('This product already added.');
            return;
        }
        row = '<tr class="prod' + id + '"><td>' + (sno++) + '</td><td>' + type + '</td><td>' + prod_name + '</td>' + '<td class="qty' + id + '">' + qty + '</td>' +
            '<td class="price' + id + '">' + price + '</td><td>' + old_price + '</td> <td>  <button onclick="removeItem(' + id +
            ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
            id + ',' + qty + ', ' + price +
            ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,
            price: price,
            type: type

        }
        products[id] = prod;

        $('#prod_id').val('')
        $('#qty').val('')
    }

    function removeItem(id) {
        $('#productList').find('.prod' + id).remove();
        const index = products.indexOf(id)
        if (index > -1) products.splice(index, 1);
        delete products[id]
        console.log(products)
    }

    function editItem(id, qty, price) {
        $("#edititemid").val(id)
        $("#edititemqty").val(qty)
        $("#edit_item_price").val(price)
        $("#modalId").modal("show");

    }

    function saveItem() {
        id = $("#edititemid").val()
        qty = $("#edititemqty").val()
        price = $("#edit_item_price").val()
        prod_name = "asdfadsf asdfadsf";
        var prod = {
            id: id,
            qty: qty,
            price: price,

        }
        products[id] = prod;


        $('#productList').find('.qty' + id).html(qty);
        $('#productList').find('.price' + id).html(price);
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

    $("#requirement_id").on("change", function() {

        $.ajax({
            method: 'POST',
            url: 'ajax/get-requirement-product-list.php',
            data: {
                id: $(this).val()
            },
            success: function(data) {

                data = JSON.parse(data);


                data.forEach(element => {
                    var id = element.id;
                    var prod_name = element.prod_name;
                    var qty = element.qty;
                    var price = element.price;
                    var type = "RP"

                    row += '<tr class="prod' + id + '"><td>' + (sno++) + '</td><td>' + type + '</td> <td>' + prod_name + '</td>' + '<td class="qty' + id + '">' + qty + '</td>' +
                        '<td class="price' + id + '">' + price + '</td><td>  <button onclick="removeItem(' + id +
                        ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
                        id + ',' + qty + ', ' + price +
                        ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
                    $('#productList').append(row);
                    //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
                    var prod = {
                        id: id,
                        qty: qty,
                        price: price,
                        type: type

                    }
                    products[id] = prod;

                });
                $('#productList').html(row);




            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        // $("select").select2();

    });
</script>