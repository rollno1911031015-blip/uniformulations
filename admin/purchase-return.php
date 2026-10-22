<?php
include("../layouts/header.php");
if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';






    if ($error == '') {
        try {





            $stmt = $mysqli->prepare("INSERT into purchase_return_mst(user_id,description,invoice_date,vendor_id) values(?,?,?,?);");
            $stmt->bind_param("issi", $user['id'], $_POST['description'], $_POST['invoice_date'],$_POST['vendor_id']);
            $stmt->execute();
            $purchase_return_mst = $mysqli->insert_id;
            $stmt->close();

            $stmt = $mysqli->prepare("SELECT * from  stock_inward_mst where id=?  ;");
            $stmt->bind_param('i', $_POST['invoice_id']);
            $stmt->execute();
            $inv = $stmt->get_result()->fetch_assoc();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into purchase_return_det(p_return_id,product_id,qty,invoice_id,batch_no) values(?,?,?,?,?);");
                $stmt->bind_param("iiiis",  $purchase_return_mst, $item['id'], $item['qty'], $item['invoice_id'], $item['batch_no']);
                $stmt->execute();
                $stmt->close();

                $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where batch_no=? and  prod_id=?");
                // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
                $cstock->bind_param("isi", $item['qty'], $item['batch_no'],  $item['id']);
                $cstock->execute();
                $cstock->close();

                $cstock = $mysqli->prepare("UPDATE stock_inward_det set return_qty=return_qty+? where stock_mst_id=? and  product_id=?");
                // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
                $cstock->bind_param("iii", $item['qty'], $item['invoice_id'],  $item['id']);
                $cstock->execute();
                $cstock->close();
            }

            alert('Return successfully.', 'success');
            redirect('purchase-return');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('purchase-return');
        }
    } else {
        echo $error;
        alert($error, 'error', 'ERROR');
        redirect('purchase-return');
    }
}

$per = permissions(4, $mysqli, $user);


?>





<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Purchase Return</h4>
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
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Purchase Return
                </button>


            <?php
            }
            ?>

            <!-- Modal -->

            <div class="table-responsive mt-3">
                <table class="table ">
                    <thead>
                        <tr>
                            <th>S.No</th>
                         
                            <th scope="col">Vendor </th>
                            <th scope="col">User </th>
                            <th scope="col">Description</th>
                            <th scope="col">Invoice Date</th>
                            <th scope="col">Action</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $stmt = $mysqli->prepare("SELECT a.*,d.name as user,f.company_name from  purchase_return_mst a  JOIN admin_users d on a.user_id=d.id join vendor e on a.vendor_id=e.id join company f on e.company_id=f.id where date(a.invoice_date)>=? and date(a.invoice_date)<=?");
                        $stmt->bind_param('ss', $_GET['fromDt'], $_GET['toDt']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $sno = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<tr>
                                                <td>' . $sno++ . '</td>
                                               
                                                <td>' . $row['company_name'] . '</td>
                                                <td>' . $row['user'] . '</td>
                                                <td>' . $row['description'] . '</td>
                                                <td>' . $row['invoice_date'] . '</td>
                                                <td><button class="btn btn-primary btn-sm" onclick="showproduct(' . $row['id'] . ')"><i class="fa fa-suitcase"></i></button>
                                                    <a class="btn btn-success btn-sm" href="purchase-return-report?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>
                                                </td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Purchase Return</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <input type="hidden" name="prod_list" id="prod_list" value="">
                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Return  Date</label>
                        <input class="form-control" type="date" name="invoice_date" id="invoice_date">

                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Select Vendor</label>
                        <select class="form-control" name="vendor_id" id="vendor_id">
                            <option value="">Select Vendor</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT  * from vendor a join company b on a.company_id=b.id ;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['company_name'] . '</option>';
                            }
                            ?>

                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Select Product</label>
                        <select class="form-control" name="product_id" id="product_id">
                            <option value="">Select Product</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT  * from products ;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>

                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Select Batch No</label>
                        <select class="form-control" name="batch_no" id="batch_no">
                            <option value="">Select Batch No</option>

                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="validationCustom02" class="form-label">Select Invoice</label>
                        <select class="form-control" name="invoice_id" id="invoice_id">
                            <option value="">Select Invoice</option>


                        </select>
                    </div>


                    <div class="col-md-3 mt-3">
                        <label for="" class="form-label">Current Stock</label>
                        <input type="text" disabled id="current_stock" class="form-control">

                    </div>


                    <div class="col-md-3 mt-3">
                        <label for="validationCustom03" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="qty" required>
                        <div class="invalid-feedback">
                            Quantity
                        </div>
                    </div>
                    <div class="col-md-1 mt-3">
                        <label for="validationCustom03" class="form-label">Add</label>
                        <button type="button" class="btn btn-success" onclick="addItem();">Add</button>
                    </div>
                    <div class="col-md-12 mt-3">
                        <table class="table">
                            <thead>
                                <th>Product Name</th>
                                <th>Batch Name</th>
                                <th>Qty</th>
                                <th>Action</th>

                            </thead>
                            <tbody id="productList">

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="validationCustom03" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required> </textarea>

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_btn" onclick="saveBill();">Save changes</button>
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
    $("#product_id").on("change", function() {
        let product = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-batch_no.php',
            data: {
                id: product
            },
            success: function(data) {
                $("#batch_no").html(data);


            }
        });
    });

    $("#batch_no").on("change", function() {
        let batch_no = $(this).find(":selected").text();
        let cs = $(this).find(":selected").data("cs");
        $("#current_stock").val(cs)
        $.ajax({
            method: 'POST',
            url: 'ajax/get-invoice.php',
            data: {
                id: batch_no
            },
            success: function(data) {
                $("#invoice_id").html(data);


            }
        });
    });






    $(document).ready(function() {
        $('#customersTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });





    var products = new Array();;

    function addItem() {
        var id = $('#product_id').val()
        var prod_name = $('#product_id option:selected').text()
        var qty = $('#qty').val()
        var invoice_id = $('#invoice_id').val()
        var batch_no = $('#batch_no').find(":selected").text()


        var prod_qty = $('#product option:selected').data('qty');
        if (prod_qty < qty) {
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
        var row = '<tr class="prod' + id + '"><td>' + prod_name + '</td> <td>' + batch_no + '</td>' + '<td>' + qty + '</td>' + '<td>  <button onclick="removeItem(' + id + ')" class="btn btn-sm btn-danger ml-3" type="button"><i class="fa fa-trash"></i> </button></td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,
            invoice_id: invoice_id,
            batch_no: batch_no,

        }
        products[id] = prod;
        console.log(products);
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
            url: 'ajax/purchase-return-product.php',
            data: {
                id: id
            },
            success: function(data) {
                data = JSON.parse(data);
                sno = 1;
                row = "";
                data.forEach(element => {
                    row += '<tr><td>' + sno++ + '</td><td>' + element['name'] + '</td><td>' + element['qty'] + '</td></tr>';

                });
                $("#showproduct_table").html(row);
                $("#showproduct").modal("show");


            }
        });
    }
</script>