<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn ($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';




    if ($error == '') {
        try {

            if (isset($_POST['prev_challan']) && $_POST['prev_challan'] > 0) {
                $loc_id = $_POST['loc_id'];
                $prev_challan = 1;
                $_POST['challan_id'] = $_POST['challan_no'];
            } else {
                $stmt = $mysqli->prepare("SELECT * from  stock_outward_mst where id=?  ;");
                $stmt->bind_param('i', $_POST['challan_id']);
                $stmt->execute();
                $challan = $stmt->get_result()->fetch_assoc();
                $loc_id = $challan['loc_id'];
                $prev_challan = 0;
            }



            $stmt = $mysqli->prepare("INSERT into  sale_return_mst(lead_id,challan_id,description,user_id,invoice_date,prev_challan) values(?,?,?,?,?,?);");
            $stmt->bind_param("iisisi", $_POST['lead'], $_POST['challan_id'], $_POST['description'], $user['id'], $_POST['invoice_date'], $prev_challan);
            $stmt->execute();
            $purchase_return_mst = $mysqli->insert_id;
            $stmt->close();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into sale_return_det(sale_return_mst_id,product_id,qty,challan_id) values(?,?,?,?);");
                $stmt->bind_param("iiii",  $purchase_return_mst, $item['id'], $item['qty'], $item['challan_id']);
                $stmt->execute();
                $stmt->close();

                $stmt = $mysqli->prepare("SELECT * from  current_stock where loc_id=? and prod_id=?  ;");
                $stmt->bind_param('ii', $loc_id, $item['id']);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                if (empty($res)) {
                    $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?);");
                    $cstock->bind_param("iiii", $loc_id, $item['id'], $item['qty']);
                    $cstock->execute();
                    $cstock->close();
                } else {
                    $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock+? where loc_id =? and prod_id=?");
                    $cstock->bind_param("iii", $item['qty'], $loc_id, $item['id']);
                    $cstock->execute();
                    $cstock->close();
                }
                $cstock = $mysqli->prepare("UPDATE stock_outward_det set return_qty=return_qty+? where stock_outward_mst_id =? and product_id=?");
                $cstock->bind_param("iii", $item['qty'],  $_POST['challan_id'], $item['id']);
                $cstock->execute();
                $cstock->close();
            }

            alert('Return successfully.', 'success');
            redirect('sale-return');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('sale-return');
        }
    } else {
        echo $error;
        alert($error, 'error', 'ERROR');
        redirect('sale-return');
    }
}
$per = permissions(9, $mysqli, $user);


?>





<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Sale Return</h4>
            <form method="get">
                <div class="row">
                    <div class="col-md-3">
                        <input type="date" name="fromDt" value="<?= $_GET['fromDt'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="toDt" value="<?= $_GET['toDt'] ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" name="" class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>

        </div>
        <div class="card-body table-responsive">
            <!-- Button trigger modal -->
             <?php 
             if($per['edit']==1){
                ?>
 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Sale Return
            </button>
                <?php
             }
             ?>
           


            <div class="table-responsive mt-3">
                <table class="table ">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Customer Employee</th>
                            <th scope="col">User type</th>
                            <th scope="col">Challan No.</th>
                            <th scope="col">user</th>
                            <th scope="col">Description</th>
                            <th scope="col">Invoice Date</th>
                            <th scope="col">Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $stmt = $mysqli->prepare("SELECT  a.*,b.name as name, b.user_type,c.name as user from sale_return_mst a JOIN lead b on a.lead_id=b.id JOIN admin_users c on a.user_id=c.id  and date(a.invoice_date)>=? and date(a.invoice_date)<=?");
                        $stmt->bind_param('ss', $_GET['fromDt'], $_GET['toDt']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $sno = 1;
                        while ($row = $res->fetch_assoc()) {
                            if ($row['prev_challan'] == 1) $row['challan_id'] = $row['challan_id'] . ' <span class="badge badge-warning">old</span>';

                            echo '<tr>
                            <td>' . $sno++ . '</td>
                            <td>' . $row['name'] . '</td>
                            <td>' . $row['user_type'] . '</td>
                            <td>' . $row['challan_id'] .  '</td>
                            <td>' . $row['user'] . '</td>
                    
                            <td>' . $row['description'] . '</td>
                            <td>' . $row['invoice_date'] . '</td>
            <td><button class="btn btn-primary btn-sm" onclick="showproduct(' . $row['id'] . ')"><i class="fa fa-eye"></i></button>
            <a class="btn btn-success btn-sm" href="sale-return-report?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a></td>
        </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>



        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal">
    <div class="modal-dialog">
        <form method="POST" id="frmMain">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sale Return</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="prod_list" id="prod_list" value="">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <input type="checkbox" name="previous_sale" value="1" id="previous_sale">
                            Direct Sale Return without challan (Previous Sale)
                        </div>
                        <div class="col-md-12 mt-3">

                            <label for="validationCustom01" class="form-label">Customer/Employee</label>
                            <select name="lead" id="lead" class="form-control">
                                <option value="">Select Customer/Employee</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT a.*,b.company_name from  lead a JOIN company b on a.company_id=b.id  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option id="" value="' . $row['id'] . '"  >' . $row['name'] . ' (' . $row['user_type'] . ' / ' . $row['company_name'] . ')</option>';
                                }
                                ?>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Select Product</label>
                            <select name="product" id="product" class="form-control">
                                <option value="">Select Product</option>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  products  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option id="" value="' . $row['id'] . '"  >' . $row['name'] . ' </option>';
                                }
                                ?>

                            </select>
                        </div>


                        <div class="col-md-6 mt-3">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3 challan1" style="display:none">
                            <label>Challan/Invoice No</label>
                            <input type="text" name="challan_no" id="challan_no" class="form-control">
                        </div>

                        <div class="col-md-6 mt-3 challan">
                            <label>Select Challan No.</label>
                            <select name="challan_id" id="challan_id" class="form-control">
                                <option value="0">Select</option>

                            </select>
                        </div>


                        <div class="col-md-3 mt-3">
                            <label>Enter Qty</label>
                            <input name="qty" id="qty" class="form-control" placeholder="Enter quantity">

                        </div>
                        <div class="col-md-2 mt-3">
                            <label for="">Add</label>
                            <button type="button" class="btn btn-success" onclick="addItem();">Add</button>
                        </div>
                        <div class="col-md-12 mt-3">
                            <table class="table">
                                <thead>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Action</th>

                                </thead>
                                <tbody id="productList">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveBill();">Save changes</button>
                </div>

            </div>


        </form>
    </div>
</div>
<div class="modal fade" id="showproduct">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                Products
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Product Name</th>
                                <th>Qty</th>

                            </tr>
                        </thead>
                        <tbody id="showproduct_table">

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
<?php include("../layouts/footer.php"); ?>

<script>
    $(document).ready(function() {
        $("#lead").select2();
    });

    $('#previous_sale').on('click', function() {
        if ($(this).prop('checked')) {
            $('.challan').hide(500);
            $('.challan1').show(500);
            prev_challan = 1;
        } else {
            $('.challan1').hide(500);
            $('.challan').show(500);
            prev_challan = 0;
        }

        $("#lead").val('');
        $("#challan_id").val('');
        $("#product").html('');
        $("#lead").select2();
        products = new Array();
        $('#productList').html('');
    });

    $("#lead").on("change", function() {
        let lead = $(this).val();
        if (prev_challan == 0)
            $.ajax({
                method: 'POST',
                url: 'ajax/get-stock-outward.php',
                data: {
                    id: lead
                },
                success: function(data) {

                    $("#product").html(data);

                }
            });
        else
            $.ajax({
                method: 'POST',
                url: 'ajax/get-stock-outward-products.php',
                data: {
                    id: 0,
                    prev_challan: 1
                },
                success: function(data) {

                    $("#product").html(data);
                    $("#product").select2();
                }
            });
    })

    $("#product").on("change", function() {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-outward-invoice.php',
            data: {
                lead_id: $("#lead").val(),
                product_id: $(this).val(),

            },
            success: function(data) {

                $("#challan_id").html(data);
            }
        });
    })

    // $("#challan_id").on("change", function() {
    //     challan_id = $(this).val();
    //     $.ajax({
    //         method: 'POST',
    //         url: 'ajax/get-stock-outward-products.php',
    //         data: {
    //             id: challan_id,
    //             prev_challan: 0
    //         },
    //         success: function(data) {

    //             $("#product").html(data);
    //             $("#product").select2();
    //         }
    //     });
    // });



    var prev_challan = 0;
    var products = new Array();

    function addItem() {
        var id = $('#product').val()
        var prod_name = $('#product option:selected').text()
        var qty = $('#qty').val()
        var challan_id = $('#challan_id').val()

        var prod_qty = $('#product option:selected').data('qty');
        if (prev_challan == 0 && prod_qty < qty) {
            alert("Qty should be less then or equal to  product qty");
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
        var row = '<tr class="prod' + id + '"><td>' + prod_name + '</td>' + '<td>' + qty + '</td>' +
            '<td>  <button onclick="removeItem(' + id +
            ')" class="btn btn-sm btn-danger ml-3" type="button"><i class="fa fa-trash"></i> </button></td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,
            challan_id: challan_id

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

    function saveBill() {
        if ($("#t_password").val() == false) {
            toastr.error("Password Required");
            return;
        }
        $('#prod_list').val(JSON.stringify(products));
        $('#frmMain').submit()
    }

    function showproduct(id) {
        $.ajax({
            method: 'POST',
            url: 'ajax/sale-return-product.php',
            data: {
                id: id
            },
            success: function(data) {
                data = JSON.parse(data);
                sno = 1;
                row = "";
                data.forEach(element => {
                    row += '<tr><td>' + sno++ + '</td><td>' + element['name'] + '</td><td>' + element[
                            'qty'] +
                        '</td></tr>';

                });
                $("#showproduct_table").html(row);
                $("#showproduct").modal("show");


            }
        });
    }
</script>