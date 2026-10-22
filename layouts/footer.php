</div>
</div>


</div>
<!-- /Main Wrapper -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

<script src="../public/dataTables/datatables.min.js"></script>
<!-- Feather Icon JS -->
<script src="../public/js/feather.min.js"></script>

<!-- Slimscroll JS -->
<script src="../public/js/jquery.slimscroll.min.js"></script>

<!-- Bootstrap Core JS -->
<script src="../public/js/bootstrap.bundle.min.js"></script>

<!-- Chart JS -->
<script src="../public/js/apexcharts.min.js"></script>
<script src="../public/js/chart-data.js"></script>

<!-- Sweetalert 2 -->
<script src="../public/js/sweetalert2.all.min.js"></script>
<script src="../public/js/sweetalerts.min.js"></script>

<!-- Custom JS -->
<!-- <script src="../public/js/theme-script.js"></script> -->
<script src="../public/js/script.js"></script>


</body>

</html>


<div class="modal fade" id="add_product_modal">
    <div class="modal-dialog modal-lg">
        <form class=" g-3 needs-validation" novalidate method="POST" id="AddProductForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Product</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="product_id" name="id" type="hidden">
                    <div class="row">

                        <div class="col-md-6 mt-3">
                            <label for="validationCustom01" class="form-label"> Image</label>
                            <input type="file" class="form-control" id="file" name="file" value="">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Company</label>
                            <input type="text" name="company" id="company" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label> Select Division</label>
                            <select name="brand" id="brand" class="form-control " required>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from brand");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '"  >' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label> Select Category</label>
                            <select name="category" id="category" class="form-control category" required>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from categories");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '" data-id="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Composition</label>
                            <input type="text" name="composition" id="composition" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Type of Unit</label>
                            <select name="unit_type" id="unit_type" class="form-control" required>
                                <option value="">Select Unit Type</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  unit_type");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                }
                                ?>

                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Packing</label>
                            <select name="packing" id="packing" class="form-control">
                                <option value="">Select</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  pack");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>MRP</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Sale Rate</label>
                            <input type="number" step="0.01" name="sale_rate" id="sale_rate" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>CGST</label>
                            <input type="number" step="0.01" name="cgst" id="cgst" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>SGST</label>
                            <input type="number" step="0.01" name="sgst" id="sgst" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>IGST</label>
                            <input type="number" step="0.01" name="igst" id="igst" class="form-control">

                        </div>


                        <div class="col-md-6 mt-3">
                            <label>H.Scm Allow</label>
                            <select name="hscm" id="hscm" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Q.Scm Allow</label>
                            <select name="qscm" id="qscm" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Generic</label>
                            <select name="generic" id="generic" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Min Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>HSN Code</label>
                            <input type="text" name="hsn_code" id="hsn_code" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Cess</label>
                            <input type="number" step="0.01" name="cess" id="cess" class="form-control">

                        </div>


                        <div class="col-md-6 mt-3">
                            <label>Narcotic Item</label>
                            <select name="narcotic_item" id="narcotic_item" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Food License</label>
                            <select name="food_license" id="food_license" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Bar code</label>
                            <input type="text" name="barcode" id="barcode" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Active</label>
                            <select name="active" id="active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">In active</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>New Launch</label>
                            <select name="new_launch" id="new_launch" class="form-control" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>



                        <!-- display none -->
                        <div class="col-md-6 mt-3 d-none">
                            <label>Location</label>
                            <input type="text" name="location" id="location" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3 d-none">
                            <label>Location 2</label>
                            <input type="text" name="location_2" id="location_2" class="form-control">

                        </div>


                        <div class="col-md-6 mt-3 d-none">
                            <label>Purchase Rate</label>
                            <input type="number" step="0.01" name="purchase_rate" id="purchase_rate" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3 d-none">
                            <label>Cost</label>
                            <input type="number" step="0.01" name="cost" id="cost" class="form-control">

                        </div>




                        <div class="col-md-6 mt-3 d-none">
                            <label>Expiry</label>
                            <select name="expiry" id="expiry" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>



                        <div class="col-md-6 mt-3 d-none">
                            <label>Fix Dis(Y/N/M)</label>
                            <select name="fix_dis" id="fix_dis" class="form-control">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>

                        </div>


                        <div class="col-md-6 mt-3 d-none">
                            <label>Opening Stock</label>
                            <input type="number" name="opening_stock" id="opening_stock" class="form-control">
                        </div>



                        <div class="col-md-6 mt-3 d-none">
                            <label>Warranty (in days)</label>
                            <input type="number" name="warranty" id="warranty" class="form-control">
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave" id="">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" /> -->




<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="../public//richtexteditor/rte_theme_default.css" />
<script type="text/javascript" src="../public//richtexteditor/rte.js"></script>
<script type="text/javascript" src='../public//richtexteditor/plugins/all_plugins.js'></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(".dataTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": true,
        "buttons": ["excel", 'csv'],
    }).buttons().container().appendTo('.col-md-6:eq()');


    $(".mydataTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": false,

        "searching": false, // Disables the search bar
        "paging": false, // Enables pagination
        "info": false,
        "buttons": ["excel", 'csv'],

    }).buttons().container().appendTo('.col-md-6:eq()');

    $(".mydataTableReport").DataTable({
        "responsive": false,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": false,

        "searching": true, // Disables the search bar
        "paging": false, // Enables pagination
        "info": false,
        "buttons": ["excel", 'csv'],

    }).buttons().container().appendTo('.col-md-6:eq()');
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }





    $(".add_product").on("click", function() {

        $("#btn_save").attr("type", "button");
        $("#btn_save").addClass('btn_save_');
        $("#name").val("")
        $("#price").val("")
        $("#unit_type").val("")
        $("#stock").val("")
        $("#warranty").val("")
        $("#subcategory").val("")
        $(".category").val("")
        $("#add_product_modal").modal("show");
    })
    $(document).on("click", '.btn_save_', function() {

        $.ajax({
            method: 'POST',
            url: 'ajax/add_product.php',
            //    data: {
            //        state: state
            //    },
            data: $("#AddProductForm").serialize(),
            success: function(data) {

                data = JSON.parse(data);
                if (data.error == false) {
                    toastr.success(data.msg)
                    $("#add_product_modal").modal("hide");
                } else {
                    toastr.error(data.msg)
                }

            }
        });
    });

    $(".category").on("change", function() {
        let name = $(this).find(':selected').data('id');

        $.ajax({
            method: 'POST',
            url: 'ajax/get-subcategory.php',
            data: {
                name: name
            },
            success: function(data) {

                $(".subcategory").html(data);

            }
        });
    });
</script>
<?php

if (isset($_POST['BtnSave'])) {




    $error = '';

    $upload_dir = 'uploaded/';
    $file_url = "";
    if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])) {
        $pic_name = $_FILES["file"]["name"];
        $pic_tmp_name = $_FILES["file"]["tmp_name"]; {
            $random_name = rand(1000, 10000000000000000) . "-" . $pic_name;
            $upload_name = $upload_dir . strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);
            try {
                $res = move_uploaded_file($pic_tmp_name, $upload_name);
                $file_url = $upload_name;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    } else {
        $stmt = $mysqli->prepare("SELECT * from `products` where id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
        $file_url = $company['image'];
    }


    if (!empty($_POST['id'])) {
        if (empty($_POST['name'])) {
            alert("name is required.", "error", "error");
            redirect("?");
        } else {
            try {
                $stmt = $mysqli->prepare("UPDATE  products set brand=?,product_code=?,name=?,price=?,min_stock=?,warranty_days=?,is_active=?,uom=?,opening_stock=?,category=?,sub_category=?,composition=?,company=?,packing=?,location=?,location2=?,sale_rete=?,purchase_rate=?,cost=?,generic=?,expiry=?,hscm=?,qscm=?,fix_dis=?,cgst=?,sgst=?,igst=?,cess=?,narcotic_item=?,food_license=?,image=?,new_launch=? where id=?");
                $stmt->bind_param("sssdiiisisssssssdddsssssddddsssii",  $_POST['brand'], $product_code, $_POST['name'],  $_POST['price'], $_POST['stock'], $_POST['warranty'], $_POST['active'], $_POST['unit_type'], $_POST['opening_stock'], $_POST['category'], $_POST['subcategory'], $_POST['composition'], $_POST['company'], $_POST['packing'], $_POST['location'], $_POST['location2'], $_POST['sale_rete'], $_POST['purchase_rate'], $_POST['cost'], $_POST['generic'], $_POST['expiry'], $_POST['hscm'], $_POST['qscm'], $_POST['fix_dis'], $_POST['cgst'], $_POST['sgst'], $_POST['igst'], $_POST['cess'], $_POST['narcotic_item'], $_POST['food_license'], $file_url, $_POST['new_launch'], $_POST['id']);
                $stmt->execute();
                $stmt->close();
                alert('Updated successfully', 'success');
                redirect("?division=" . $_GET['division'] . "&limit=" . $_GET['limit'] . "&page=" . $_GET['page'] . "");
            } catch (Exception $e) {
                alert($e->getMessage(), 'error');
                redirect("?division=" . $_GET['division'] . "&limit=" . $_GET['limit'] . "&page=" . $_GET['page'] . "");
            }
        }
    } else {
        if (empty($_POST['name'])) {
            alert("Name is required", "error", "error");
            redirect("?");
        } else {

            $product_code = rand(11111111, 99999999);
            $stmt = $mysqli->prepare("INSERT into products (brand,product_code,name,price,min_stock,warranty_days,is_active,uom,opening_stock,category,sub_category,composition,company,packing,location,location2,sale_rete,purchase_rate,cost,generic,expiry,hscm,qscm,fix_dis,cgst,sgst,igst,cess,narcotic_item,food_license,image,new_launch) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ;");
            $stmt->bind_param("sssdiiisisssssssdddsssssddddsssi", $_POST['brand'], $product_code, $_POST['name'],  $_POST['price'], $_POST['stock'], $_POST['warranty'], $_POST['active'], $_POST['unit_type'], $_POST['opening_stock'], $_POST['category'], $_POST['subcategory'], $_POST['composition'], $_POST['company'], $_POST['packing'], $_POST['location'], $_POST['location2'], $_POST['sale_rete'], $_POST['purchase_rate'], $_POST['cost'], $_POST['generic'], $_POST['expiry'], $_POST['hscm'], $_POST['qscm'], $_POST['fix_dis'], $_POST['cgst'], $_POST['sgst'], $_POST['igst'], $_POST['cess'], $_POST['narcotic_item'], $_POST['food_license'], $file_url, $_POST['new_launch']);
            $stmt->execute();
            $stmt->close();

            alert("Product created successfully.", "success", "success");
            redirect("?");
        }
    }
}

toaster();
?>
<script>
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
<script>
    $(document).ready(function() {
        $('input, select, textarea, button').on('keypress', function(e) {
            if (e.which === 13) { // 13 is the Enter key code
                e.preventDefault(); // Prevent form submission

                var fields = $('input, select, textarea, button');
                var index = fields.index(this);
                var nextField = fields.eq(index + 1);
                if (nextField.length) {
                    nextField.focus(); // Focus the next input field
                }
            }
        });
    });
</script>

 