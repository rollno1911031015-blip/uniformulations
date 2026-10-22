<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn ($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';
    if (empty($_POST['loc_id']))
        $error = 'Location required.';

    if (isset($_POST['delivery_address'])) {
        $stmt = $mysqli->prepare("SELECT * from   lead_address  where id=?");
        $stmt->bind_param("i", $_POST['delivery_address']);
        $stmt->execute();
        $delivery_address = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }




    if ($error == '') {
        try {


            $stmt = $mysqli->prepare("INSERT into  stock_outward_mst(loc_id,lead_id,user_id,description,invoice_date,order_id,gr_no,gr_date,transport_name,no_of_curtains,address,state,city,pincode,country) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
            $stmt->bind_param("iiississsisssss", $_POST['loc_id'], $_POST['customer_id'], $user['id'], $_POST['description'], $_POST['invoice_date'], $_POST['order_id'], $_POST['gr_no'], $_POST['gr_date'], $_POST['transport_name'], $_POST['no_of_curtains'], $delivery_address['address'], $delivery_address['state'], $delivery_address['city'], $delivery_address['pincode'], $delivery_address['country']);
            $stmt->execute();
            $stock_out_id = $mysqli->insert_id;
            $stmt->close();

            $i = 0;

            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into stock_outward_det	(stock_outward_mst_id,product_id,qty,carton_no,gross_weight,batch_no,expiry_date) values(?,?,?,?,?,?,?);");
                $stmt->bind_param("iiisdss", $stock_out_id, $item['product_id'], $item['out_qty'], $item['carton_no'], $item['gross_weight'],$item['batch_no'],$item['expiry_date']);
                $stmt->execute();
                $stmt->close();

                $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=? and id=?");
                // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
                $cstock->bind_param("diii", $item['out_qty'], $_POST['loc_id'],  $item['product_id'],$item['id']);
                $cstock->execute();
                $cstock->close();



                $order_det = $mysqli->prepare("UPDATE order_det set out_qty=out_qty+? where id=?");
                // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
                $order_det->bind_param("ii", $item['out_qty'],  $item['order_det_id']);
                $order_det->execute();
                $order_det->close();

                $stmt = $mysqli->prepare("SELECT * from  order_det  where id=?");
                $stmt->bind_param("i", $item['order_det_id']);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($res['out_qty'] != $res['qty']) {
                    $i = 1;
                }
            }
            $status = 'delivered';
            if ($i == 1) {
                $status = 'pending';
            }

            $order_det = $mysqli->prepare("UPDATE  order_mst set status=?  where id=?");
            $order_det->bind_param("si", $status,  $_POST['order_id']);
            $order_det->execute();
            $order_det->close();




            alert('Stock updated successfully.', 'success');
            redirect('outward-report?id=' . $stock_out_id . '');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('outward-stock');
        }
    } else {
        echo $error;
        alert($error, 'error', 'ERROR');
        redirect('outward-stock');
    }
}


?>

<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Outward Stock</h4>


        </div>
        <div class="card-body">
            <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">
                <div class="col-md-12 table-responsive">


                    <div class="row" style="display: flex ;">



                        <div class="form-group col-md-3">
                            <label for="">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control ids" required>

                                <option value="">--Select Customer--</option>

                                <?php

                                $stmt = $mysqli->prepare("SELECT * from  lead  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>" <?php if (!empty($_GET['customer_id']) && $_GET['customer_id'] == $row['id']) echo "selected" ?>><?= $row['name'] . ' ' . $row['address'] ?></option>
                                <?php
                                }
                                ?>
                            </select>

                        </div>

                        <div class="form-group col-md-3">
                            <label for="">Order</label>
                            <select name="order_id" id="order_id" class="form-control ids" required>

                                <option value="">--Select Order--</option>

                            </select>

                        </div>


                        <div class="form-group col-md-3">
                            <label for="">Location</label>
                            <select name="loc_id" id="loc_id" class="form-control ids" required>

                                <option value="">--Select Locations--</option>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option id="" value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '">' . $row['name'] . ' ' . $row['address'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>




                        <div class="form-group col-md-3 ">
                            <label for="">Invoice Date</label>
                            <input type="date" class="form-control" name="invoice_date" id="invoice_date">

                        </div>

                        <div class="form-group col-md-3 mt-3">
                            <label for="">GR Number</label>
                            <input type="text" class="form-control" name="gr_no" id="gr_no">

                        </div>

                        <div class="form-group col-md-3 mt-3">
                            <label for="">GR Date</label>
                            <input type="date" class="form-control" name="gr_date" id="gr_date">

                        </div>
                        <div class="form-group col-md-3 mt-3">
                            <label for="">Transport Name</label>
                            <select class="form-control" name="transport_name" id="transport_name">
                                <option value="">Select</option>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  mode_of_transport  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option id="" value="' . $row['name'] . '" >' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>

                        <div class="form-group col-md-3 mt-3">
                            <label for="">No. of Cartons</label>
                            <input type="number" class="form-control" name="no_of_curtains" id="no_of_curtains">

                        </div>

                        <div class="form-group col-md-3 mt-3">
                            <label for="">Delivery Address</label>
                            <select name="delivery_address" id="delivery_address" class="form-control ids" required>
                                <option value="">--Select Delivery Address--</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                    </div>

                    <hr>
                    <!-- <table class="table table-bordered">
                        <tr>
                            <td>
                                <select class="form-control" id="brand" name="brand">
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
                                <select class="form-control" id="category" name="category">
                                    <option value="">Select Category</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from categories");
                                    //$stmt->bind_param('i', $_GET['loc_id']);
                                    $stmt->execute();
                                    $categories = $stmt->get_result();
                                    while ($row = $categories->fetch_assoc()) {
                                        echo '<option value="' . $row['name'] . '" data-id="' . $row['id'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>



                                </select>

                            </td>
                            <td>
                                <select id="subcategory" name="subcategory" class="form-control">
                                    <option value="">Select subcategory</option>
                                </select>

                            </td>
                            <td>
                                <select name="prod_id" id="prod_id" class="form-control ids" required>
                                    <option value="" id="selectprd">--Select Product--</option>
                                </select>
                                <span style="color: red;"> *</span>
                            </td>
                            <td>

                            </td>
                            <td>
                                <input type="hidden" id="valchk">
                                <div id="chk">
                                    Current Stock

                                </div>
                            </td>
                            <td>
                                <input type="number" name="qty" id="qty" step="0.01" placeholder="Enter Quantity" class="form-control" required>

                            </td>

                            <td>
                                <button type="button" onclick="addItem()" class="btn btn-sm btn-outline-success">ADD</button>
                            </td>
                        </tr>
                    </table> -->
                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th>Qty</th>
                                <th>Current Stock</th>
                                <th>Out Qty</th>
                              
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productList">

                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="prod_list" id="prod_list" value="">
                <div class="text-center col-md-12 mt-4">
                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>
        </div>
    </div>
</div>




<?php include("../layouts/footer.php"); ?>

<script>
    $("#customer_id").select2();
    var products = new Array();
    var order_id = (<?php if (!empty($_GET['order_id'])) echo $_GET['order_id'];
                    else echo 0 ?>)

    function GetOrder(id) {
        $.ajax({
            url: 'ajax/get-order.php',
            method: 'POST',
            dataType: 'html',
            data: {
                customer_id: id
            },
            //data:$('form').serialize(),
            success: function(data) {

                $("#order_id").html(data);
                $("#order_id").val(order_id)
            }
        });

    }


    function GetAddress(id) {
        $.ajax({
            url: 'ajax/get-customer-address.php',
            method: 'POST',
            dataType: 'html',
            data: {
                customer_id: id
            },
            //data:$('form').serialize(),
            success: function(data) {
                $("#delivery_address").html(data);

            }
        });

    }

    function GetOrderItem(order_id, loc_id) {
        var customer_id=$("#customer_id").val()
        $.ajax({
            url: 'ajax/get-order-item.php',
            method: 'POST',
            dataType: 'html',
            data: {
                order_id: order_id,
                loc_id: loc_id
            },
            //data:$('form').serialize(),
            success: function(data) {
                var row = "";
                data = JSON.parse(data);
               
                data.forEach(element => {

                    row += `<tr class="prod${element.cs_id}">
                  
                    <td>${element.product_name}</td>
                    <td>${element.batch_no}</td>
                    <td>${element.expiry_date}</td>
                    <td>${element.qty}</td>
                    <td>${element.current_stock}</td> 
                    <td><input type="number" class="out_qty form-control" style="width:80px" value="${element.qty}" data-id="${element.cs_id}" data-qty="${element.qty}" data-loc_id="${loc_id}" data-current_stock="${element.current_stock}" data-customer_id="${customer_id }"  data-order_det_id="${element.id}"></td>
                    <td><button onclick="removeItem(${element.cs_id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash"></i> </button></td></tr>`;


                    var prod = {
                        id: element.cs_id,
                        product_id: element.product_id,
                        qty: element.qty,
                        locid: loc_id,
                        lead_id: customer_id,
                        current_stock: element.current_stock,
                        out_qty: element.qty,
                        order_det_id: element.id,
                        carton_no: 1,
                        gross_weight: 0,
                        expiry_date: element.expiry_date,
                        batch_no: element.batch_no,
                    }
                    
                    products[element.cs_id] = prod;
                });
                console.log(products)
              
                $('#productList').html(row);


            }
        });
    }

    $(document).ready(function() {
        $("#customer_id").on('change', function() {

            var customer_id = $(this).val();
            GetOrder(customer_id);
            GetAddress(customer_id);

        });

        var customer_id = $("#customer_id").val();
        if (customer_id != false) {
            GetOrder(customer_id);
            GetAddress(customer_id);

        }


        $("#loc_id").on('change', function() {

            let order_id = $('#order_id').val()
            var customer_id = $('#customer_id').val()
            let loc_id = $(this).val()
            if (order_id == false) {
                toastr.error("Select Order");
             
                return;
            }

            GetOrderItem(order_id, loc_id);

        });
        $("#order_id").on("change", function() {
            let order_id = $(this).val()
            var customer_id = $('#customer_id').val()
            let loc_id = $("#loc_id").val()
            if (loc_id == false) {
                toastr.error("Select Location");
               
                return;
            }

            GetOrderItem(order_id, loc_id);
        })

        $(document).on("keyup", ".out_qty", function() {


            var id = $(this).data("id");
            var out_qty = $(this).val();
            var qty = $(this).data("qty");
            var loc_id = $(this).data("loc_id");
            var current_stock = $(this).data("current_stock");
            var customer_id = $(this).data("customer_id");
            var order_det_id = $(this).data("order_det_id");


            if (current_stock < out_qty) {
                toastr.error("Out qty can not be more then qty");
                $(this).val(current_stock);
                return;

            }
            if (out_qty > qty) {
                toastr.error("Out qty can not be more then qty");
                $(this).val(qty);
                return;

            }


            // var prod = {
            //     id: id,
            //     qty: qty,
            //     locid: loc_id,
            //     lead_id: customer_id,
            //     current_stock: current_stock,
            //     out_qty: out_qty,
            //     order_det_id: order_det_id,
            // }
            products[id].out_qty = out_qty;
            console.log(products);

        });
        $(document).on("keyup", ".carton_no", function() {
            var id = $(this).data("id");
            var carton_no = $(this).val();
            products[id].carton_no = carton_no;

        })

        $(document).on("keyup", ".gross_weight", function() {
            var id = $(this).data("id");
            var gross_weight = $(this).val();
            products[id].gross_weight = gross_weight;

        })





        $("#prod_id").on('change', function() {
            var c_stock = $('#prod_id option:selected').attr('data-stock')
            $("#chk").html("Current Stock " + c_stock);
            $("#valchk").val(c_stock);

        });

    });





    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        $("#lead_id").select2();

    });


    function chkqty() {
        var prod = $('#prod_id').val()
        var locid = $('#loc_id').val()

    }




    function addItem() {
        var id = $('#prod_id').val()
        var stock = parseInt($('#valchk').val())
        var prod_name = $('#prod_id option:selected').text()
        var qty = parseInt($('#qty').val())
        var locations = $('#loc_id option:selected').attr('data-loc')
        var locid = $('#loc_id').val()
        var lead_id = $("#lead_id").val();

        if ((stock == 0) && (stock == null)) {
            alert('Stock is 0.');
            return;
        }
        if (qty > stock) {
            alert('Quantity More then Stock.');
            return;
        }
        if (qty <= 0) {
            alert('Qty should be more than zero.');
            return;
        }
        if (id <= 0) {
            alert('Select product.');
            return;
        }
        var ex_p = products.filter((item, index) => item.id == id)
        console.log(products[id])
        if (products[id] != undefined) {
            alert('This product already added.');
            return;
        }
        var row = '<tr class="prod' + id + '"><td>' + prod_name + '</td>' + '<td>' + qty + '</td>' + '<td>' + locations +
            ' <button onclick="removeItem(' + id +
            ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button></td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,
            locid: locid,
            lead_id: lead_id
        }
        products[id] = prod;

        $('#prod_id').val($("option:first").val(''));
        $('#qty').val('')
        $('#chk').html("Current Stock")
    }

    function removeItem(id) {
        $('#productList').find('.prod' + id).remove();
        const index = products.indexOf(id)
        if (index > -1) products.splice(index, 1);
        delete products[id]
        console.log(products)
    }

    function saveBill() {
        $('#prod_list').val(JSON.stringify(products));

        var pass = $('#paswrd').val();
        if (pass == false) {
            alert('Enter Password');
            return;
        } else {
            $('#frmMain').submit()
        }

    }


    // $("#category").on("change", function() {
    //     let name = $(this).find(':selected').data('id');
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
    //     var locid = $('#loc_id').val()
    //     if (locid == false) {
    //         alert("Please Select Location");
    //         return
    //     }
    //     $.ajax({
    //         method: 'POST',
    //         url: 'ajax/get-current-stock.php',
    //         data: {
    //             subcategory: subcategory,
    //             nlocid: locid
    //         },
    //         success: function(data) {

    //             $("#prod_id").html(data);

    //         }
    //     });
    // });
</script>