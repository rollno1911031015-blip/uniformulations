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

            $stmt = $mysqli->prepare("INSERT into defective_stock_mst(loc_id,user_id,description) values(?,?,?);");
            $stmt->bind_param("iis", $_POST['loc_id'], $user['id'], $_POST['description']);
            $stmt->execute();
            $purchase_return_mst = $mysqli->insert_id;
            $stmt->close();
 


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into defective_stock_det(defective_mst_id,prod_id,qty) values(?,?,?);");
                $stmt->bind_param("iii",  $purchase_return_mst, $item['id'], $item['qty']);
                $stmt->execute();
                $stmt->close();

                $cstock = $mysqli->prepare("UPDATE current_stock set current_stock=current_stock-? where loc_id=? and  prod_id=?");
                // $cstock = $mysqli->prepare("INSERT INTO `current_stock`(`loc_id`, `prod_id`, `current_stock`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE current_stock=current_stock-?;");
                $cstock->bind_param("iii", $item['qty'], $_POST['loc_id'],  $item['id']);
                $cstock->execute();
                $cstock->close();
            }

            alert('Defect Stock Added successfully.', 'success');
            redirect('defective-stock.php');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
        }
    } else {
        echo $error;
        alert($error, 'error', 'ERROR');
    }
}


?>

<div class="row">
                <div class="card col-lg-12">
                    <div class="card-header">
                        <h4 class="pull-left">Defective Stock</h4>

                    </div>
                    <div class="card-body table-responsive">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Add Defective Stock
                        </button>

                        <!-- Modal -->

                        <div class="table-responsive mt-3">
                            <table class="table ">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Products & Qty</th>
                                      
                                        <th scope="col">Description</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $stmt = $mysqli->prepare("SELECT  a.*,b.name as location,c.name as user from defective_stock_mst a JOIN locations b on a.loc_id=b.id JOIN admin_users c on a.user_id=c.id");

                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $sno = 1;
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<tr>
                                                <td>' . $sno++ . '</td>
                                                <td>' . $row['location'] . '</td>
                                                <td>' . $row['user'] . '</td>';

                                                $stmt1 = $mysqli->prepare("SELECT  a.*,b.name as product,b.price as price,b.uom from  defective_stock_det a JOIN products b on a.prod_id=b.id where a.defective_mst_id=?");
                                                $stmt1->bind_param('i', $row['id']);
                                                $stmt1->execute();
                                                $products = $stmt1->get_result();
                                                echo '<td><ol>';
                                                while($row1=$products->fetch_assoc()){
                                                    echo '<li>' . $row1['product'] . ' (QTY : '.$row1['qty'].')</li>';
                                                }

                                               
                                               
                                               echo '</ol></td><td>' . $row['description'] . '</td>
                                                <td>' . $row['created_at'] . '</td>
                                                <td> 
                                                    <a class="btn btn-success btn-sm" href="defective-report?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>
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



            <div class="modal fade" id="exampleModal" >
    <div class="modal-dialog">
        <form method="POST" id="frmMain">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Defective Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <input type="hidden" name="prod_list" id="prod_list" value="">
                    <div class="col-md-12 mt-3">
                        <label for="validationCustom01" class="form-label">Select Location</label> <br>
                        <select class="form-control" name="loc_id" id="loc_id">
                            <option value="">Select Location</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT  * from locations;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>

                        </select>

                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="validationCustom02" class="form-label">Select Product</label>
                        <select class="form-control" name="product" id="product">
                            <option value="">Select Product</option>

                        </select>
                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom03" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="qty" required>
                        <div class="invalid-feedback">
                            Quantity
                        </div>
                    </div>
                    <div class="col-md-2 mt-3">
                        <label for="validationCustom03" class="form-label">Add</label>
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
                    <div class="col-md-12 mt-3">
                        <label for="validationCustom03" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required> </textarea>

                    </div>
                 

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_btn" onclick="saveBill();">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="showproduct" >
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
 
    $("#loc_id").on("change", function() {
        let invoice_id = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-product-current-product.php',
            data: {
                id: invoice_id
            },
            success: function(data) {
                $("#product").html(data);


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
        var id = $('#product').val()
        var prod_name = $('#product option:selected').text()
        var qty = $('#qty').val()
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
        var row = '<tr class="prod' + id + '"><td>' + prod_name + '</td>' + '<td>' + qty + '</td>' + '<td>  <button onclick="removeItem(' + id + ')" class="btn btn-sm btn-danger ml-3" type="button"><i class="fa fa-trash"></i> </button></td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,

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