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


    if ($error == '') {
        try {

            $stmt = $mysqli->prepare("INSERT into repair_mst(customer_id,description,user_id,order_date,expected_delivery_date) values(?,?,?,?,?);");
            $stmt->bind_param("isiss", $_POST['customer_id'],   $_POST['description'], $user['id'], $_POST['order_date'], $_POST['delivery_date']);
            $stmt->execute();
            $order_id = $mysqli->insert_id;
            $stmt->close();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into  repair_det(mst_id,product_id,qty) values(?,?,?);");
                $stmt->bind_param("iii",  $order_id, $item['id'], $item['qty']);
                $stmt->execute();
                $stmt->close();
            }

            alert('Save successfully.', 'success');
            redirect('?');
        } catch (Exception $ex) {
            alert($ex->getMessage(), 'error', 'ERROR');
            redirect('?');
        }
    } else {

        alert($error, 'error', 'ERROR');
        redirect('?');
    }
}

$per = permissions(12, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>New Repair Order</h4>
        </div>

    </div>
    <div class="card-body">
        <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">
            <div class=" table-responsive">
                <div class="row mt-3">



                    <div class="form-group col-md-4">
                        <label>Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">--Select Customer--</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT a.*,b.company_name as company_name from  lead a JOIN company b on a.company_id=b.id  ;");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '" >' . $row['name'] .  ', ' . $row['company_name'] .  '</option>';
                            }
                            ?>
                        </select>
                    </div>



                    <div class="form-group col-md-4">
                        <label for="">Order Date</label>
                        <input type="date" name="order_date" id="order_date" class="form-control">

                    </div>

                    <div class="form-group col-md-4">
                        <label for="">Expected Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                    </div>





                    <div class="col-md-6">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                    </div>
                </div>
                <hr>
                <table class="table table-bordered">
                    <tr>


                        <td>
                            <select name="prod_id" id="prod_id" class="form-control" required>
                                <option value="">--Select Product--</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from products where is_active=1");
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
                            <input type="number" name="qty" id="qty" placeholder="Enter Quantity" class="form-control" required>
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
                            <th>Qty</th>

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



<?php include("../layouts/footer.php"); ?>




<script>
    // $(document).ready(function() {
    //   //change selectboxes to selectize mode to be searchable
    //   $("#loc_id").select2();
    //   $("#prod_id").select2();
    // });


    var products = new Array();;
    var row;
    var sno = 1;

    function addItem() {
        var id = $('#prod_id').val()
        var prod_name = $('#prod_id option:selected').text()
        var qty = $('#qty').val()




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

        row = '<tr class="prod' + id + '"><td>' + (sno++) + '</td> <td>' + prod_name + '</td>' + '<td class="qty' + id + '">' + qty + '</td>' +
            '<td>  <button onclick="removeItem(' + id +
            ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
            id + ',' + qty +
            ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
        $('#productList').append(row);
        //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
        var prod = {
            id: id,
            qty: qty,
            price: price,

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

    function editItem(id, qty) {
        $("#edititemid").val(id)
        $("#edititemqty").val(qty)
        $("#modalId").modal("show");

    }

    function saveItem() {
        id = $("#edititemid").val()
        qty = $("#edititemqty").val()
        prod_name = "asdfadsf asdfadsf";
        var prod = {
            id: id,
            qty: qty,

        }
        products[id] = prod;


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
</script>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable


    });
</script>