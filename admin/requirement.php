<?php
include("../layouts/header.php");

if (isset($_POST['prod_list'])) {

    $error = '';
    $p = json_decode($_POST['prod_list'], true);
    $p = array_filter($p, fn($value) => !is_null($value) && $value !== '');

    if (sizeOf($p) <= 0)
        $error = 'No product found';

    if (empty($_POST['name']))
        $error = 'name is required.';


    if ($error == '') {
        try {

            $stmt = $mysqli->prepare("INSERT into requirement_mst(name,description,user_id) values(?,?,?);");
            $stmt->bind_param("ssi", $_POST['name'],   $_POST['description'], $user['id']);
            $stmt->execute();
            $stock_id = $mysqli->insert_id;
            $stmt->close();


            foreach ($p as $item) {
                if (empty($item['id'])) continue;
                $stmt = $mysqli->prepare("INSERT into  requirement_det(mst_id,product_id,qty) values(?,?,?);");
                $stmt->bind_param("iii",  $stock_id, $item['id'], $item['qty']);
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

?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Requirement PO</h4>
        </div>

    </div>
    <div class="card-body">
        <form action="" id="frmMain" class="row" method="post" enctype="multipart/form-data">
            <div class=" table-responsive">
                <div class="row mt-3">

                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control">

                    </div>





                    <div class="col-md-6">
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
                        <a class="btn btn-dark btn-sm" href="import-requirement-list.csv" download="import-requirement-list.csv">Download Sample File</a>
                    </div>

                </div>

                <table class="table table-bordered">
                    <tr>
                    <td>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="">--Select Division--</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  brand;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '"  > ' . $row['name'] .  '</option>';
                                }
                                ?>


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
                        <td class="d-none">
                            <input type="number" step="0.01" name="price" id="price" placeholder="Enter Price" class="form-control" required>
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
                            <th class="d-none">Price</th>
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
    //   $("#prod_id").select2();
    // });

    $(document).ready(function() {

        $("#prod_id").select2();
        $("#brand_id").select2();
    });
    $("#prod_id").on("change", function() {
        $("#price").val($(this).find(':selected').data('price'))
    });

    var products = new Array();;
    var row;

    function addItem() {
        var id = $('#prod_id').val()
        var prod_name = $('#prod_id option:selected').text()
        var qty = $('#qty').val()
        var price = $('#price').val()



        if (qty <= 0) {
            alert('Qty should be more than zero.');
            return;
        }
        if (id <= 0) {
            alert('Select product.');
            return;
        }

        if (price <= 0) {
            alert('Enter Price.');
            return;
        }
        var sno = 1;
        var ex_p = products.filter((item, index) => item.id == id)
        console.log(products[id])
        if (products[id] != undefined) {
            alert('This product already added.');
            return;
        }
        row = '<tr class="prod' + id + '"><td>' + (sno++) + '</td><td>' + prod_name + '</td>' + '<td class="qty' + id + '">' + qty + '</td>' +
            ' <td>  <button onclick="removeItem(' + id +
            ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
            id + ',' + qty + ', ' + price +
            ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
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


        }
        products[id] = prod;


        $('#productList').find('.qty' + id).html(qty);
        $('#productList').find('.price' + id).html(price);
        $("#modalId").modal("hide");
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

    $("#brand_id").on("change", function() {
        let name = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-product-via-brand.php',
            data: {
                name: name
            },
            success: function(data) {

                $("#prod_id").html(data);

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
                url: 'ajax/upload-requirement-list.php',
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
                                row = '<tr class="prod' + element.id + '"><td>' + (sno++) + '</td><td>' + element.name + '</td><td class="qty' + element.id + '">' + element.qty +
                                    '<td>  <button onclick="removeItem(' + element.id +
                                    ')" class="btn btn-sm btn-danger ml-3 float-right" type="button"><i class="fa fa-trash"></i> </button> <button onclick="editItem(' +
                                    element.id + ',' + element.qty +
                                    ')" class="btn btn-sm btn-warning ml-3 float-right" type="button"><i class="fa fa-pencil"></i> </button> </td></tr>';
                                $('#productList').append(row);
                                var prod = {
                                    id: element.id,
                                    qty: element.qty,


                                }
                                products[element.id] = prod;
                            });
                        }
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

    function saveBill() {
        $('#prod_list').val(JSON.stringify(products));
        $('#frmMain').submit()
    }
</script>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        // $("select").select2();

    });
</script>