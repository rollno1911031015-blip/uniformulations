<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {


    if (!validate_phone($_POST['contact_no'])) {
        alert("Invalid mobile number", "error", "error");
        redirect("?");
    }


    if (!empty($_POST['id'])) {

        if (empty($_POST['company_id'])) {
            alert("Enter Company Name.", "error", "error");
            redirect("company");
        } else {
            $stmt = $mysqli->prepare("UPDATE  vendor set company_id=?,con_person_name=?,contact_no=?,address=?,gst=?,state=?,city=?,pincode=?,country=?,email=?,license_no=? where id=?");
            $stmt->bind_param("issssssssssi", $_POST['company_id'], $_POST['con_person_name'], $_POST['contact_no'], $_POST['address'], $_POST['gst'], $_POST['state'], $_POST['city'], $_POST['pincode'], $_POST['country'], $_POST['email'],$_POST['license_no'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Vendor updated successfully', 'success');
            redirect("vendor");
        }
    } else {
        if (empty($_POST['company_id'])) {
            alert("Select Company Name.", "error", "error");
            redirect("vendor");
        } else {


            $stmt = $mysqli->prepare("INSERT into vendor (company_id,con_person_name,contact_no,address,gst,state,city,pincode,country,email,license_no) values (?,?,?,?,?,?,?,?,?,?,?) ;");
            $stmt->bind_param("issssssssss", $_POST['company_id'], $_POST['con_person_name'], $_POST['contact_no'], $_POST['address'], $_POST['gst'], $_POST['state'], $_POST['city'], $_POST['pincode'], $_POST['country'], $_POST['email'],$_POST['license_no']);
            $stmt->execute();
            $stmt->close();

            alert("Company name created successfully.", "success", "success");
            redirect("vendor");
        }
    }
}

if (isset($_POST['btnImport'])) {
    $upload_dir = 'temp/';
    $file_url = "";
    if (isset($_FILES['file']['name'])) {
        $pic_name = $_FILES["file"]["name"];
        $pic_tmp_name = $_FILES["file"]["tmp_name"];
        $error = $_FILES["file"]["error"];
        if ($error <= 0) {
            $random_name = rand(1000, 1000000) . "-" . $pic_name;
            $upload_name = $upload_dir . strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);
            try {
                $res = move_uploaded_file($pic_tmp_name, $upload_name);
                $file_url = $upload_name;
            } catch (Exception $e) {
                alert($e->getMessage(), "error", "error");
            }
        }
    }


    if ($file_url !== '') {
        $file = $file_url;
        $handle = fopen($file, 'r');
        $i = 1;
        $success = 0;
        $duplicate = 0;
        $error = "";
        $count = 0;
        while (($filesop = fgetcsv($handle, 1000, ',')) !== false) {

            if ($i >= 1 && !empty($filesop[2])) {


                $company = $filesop[0];
                $concat_person = $filesop[1];
                $contact = $filesop[2];
                $email = $filesop[3];
                $gst = $filesop[4];
                $address = $filesop[5];
                $state = $filesop[6];
                $city = $filesop[7];
                $pincode = $filesop[8];


                $count++;




                $stmt = $mysqli->prepare("SELECT * from company where company_name=? limit 1");
                $stmt->bind_param('s', $company);
                $stmt->execute();
                $company1 = $stmt->get_result()->fetch_assoc();
                $stmt->close();




                if ($company1) {


                    $stmt = $mysqli->prepare("SELECT * from vendor where contact_no=?   limit 1");
                    $stmt->bind_param('s', $contact);
                    $stmt->execute();
                    $res = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    if ($res) {
                        $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                        $duplicate++;
                    } else {

                        try {
                            $stmt = $mysqli->prepare("INSERT into vendor (company_id,con_person_name,contact_no,address,gst,state,city,pincode,email) values (?,?,?,?,?,?,?,?,?) ;");
                            $stmt->bind_param("issssssss",  $company1['id'], $concat_person, $contact, $address, $gst, $state, $city, $pincode, $email);
                            $stmt->execute();
                            $success++;

                            $error .= "Lead  row " . $i . " added successfully..<br>";
                        } catch (Exception $e) {
                            alert($e->getMessage(), "error", "error");
                            echo $e->getMessage();
                            die;
                        }
                    }
                } else {
                    $error .= 'Invalid format <br>';
                }
                $i++;
            }
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        alert($msg, 'info');

        fclose($handle);
    }
    redirect("?");
}

$per = permissions(2, $mysqli, $user);

?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Vendor List</h4>
        </div>
        <div class="">
            <?php
            if ($per['edit'] == 1) {
            ?>


                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Vendor</button>
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#ImportModal">
                    <i class="fa fa-download" aria-hidden="true"></i> Bulk Import
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Company Name</th>
                    <th>Name</th>
                    <th>Mobile No.</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>GST</th>
                    <th>License No</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <?php
                    if ($per['edit'] == 1) {
                    ?>
                        <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php

                $stmt = $mysqli->prepare("SELECT a.*,b.company_name as company_name from vendor a left JOIN company b on a.company_id=b.id");
                // $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['company_name'] . '</td>
                                        <td>' . $row['con_person_name'] . '</td>
                                        <td>' . $row['contact_no'] . '</td>
                                        <td>' . $row['email'] . '</td>
                                        <td>' . $row['address'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['pincode'] . ', ' . $row['country'] . '</td>
                                        <td>' . $row['gst'] . '</td>
                                        <td>' . $row['license_no'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';

                    if ($per['edit'] == 1) {
                        echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-company_id="' .
                            $row['company_id'] . '" 
                                        data-con_person_name="' . $row['con_person_name'] . '"
                                        data-contact_no="' . $row['contact_no'] . '"
                                        data-address="' . $row['address'] . '"
                                        data-gst="' . $row['gst'] . '"
                                        data-state="' . $row['state'] . '"
                                        data-city="' . $row['city'] . '"
                                        data-pincode="' . $row['pincode'] . '"
                                        data-country="' . $row['country'] . '"
                                        data-email="' . $row['email'] . '"
                                        data-license_no="' . $row['license_no'] . '"
                                        
                                        ><i class="fa fa-pencil"></i></button></td>';
                    }

                    echo '</tr>';
                }
                ?>

            </tbody>
        </table>
    </div>

</div>


<div class="modal fade" id="exampleModal">
    <div class="modal-dialog">
        <form class=" g-3 needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Vendor</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="row">


                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label">Select Company Name</label>
                            <select class="form-control" id="company_id" name="company_id" value="">
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from company order by company_name asc");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['company_name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label>Concern Person Name</label>
                            <input type="text" name="con_person_name" id="con_person_name" class="form-control" required>

                        </div>
                        <div class="col-md-6">
                            <label>Contact No.</label>
                            <input type="number" name="contact_no" id="contact_no" class="form-control" required>

                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control">

                        </div>
                        <div class="col-md-6">
                            <label>GST</label>
                            <input type="text" name="gst" id="gst" class="form-control">

                        </div>
                        <div class="col-md-6">
                            <label>License No</label>
                            <input type="text" name="license_no" id="license_no" class="form-control">

                        </div>
                        <div class="col-md-12">
                            <label>Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>

                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Select State</label>
                            <select class="form-control state" id="state" name="state" value="">
                                <?php
                                $stmt = $mysqli->prepare("SELECT DISTINCT state from  state_district");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['state'] . '">' . $row['state'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Select City</label>
                            <select class="form-control" id="city" name="city" value="">
                                <option value="">Select City</option>
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label>Pin-code</label>
                            <input name="pincode" id="pincode" class="form-control">

                        </div>
                        <div class="col-md-6">
                            <label>Country</label>
                            <input name="country" id="country" class="form-control" value="India">

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

<form method="POST" enctype="multipart/form-data">

    <div class="modal fade" id="ImportModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Import </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <diV class="col-md-6">
                            <input type="file" name="file" id="file" class="form-control">

                        </diV>
                        <div class="col-md-6">
                            <a href="import-vendor.csv" class="btn btn-sm btn-dark float-end" download="import-vendor.csv">Download Sample File</a>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="btnImport" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include("../layouts/footer.php"); ?>
<script>
    $(document).on("click", ".edit_company", function() {
        $("#id").val($(this).data("id"));
        $("#company_id").val($(this).data("company_id"));
        $("#con_person_name").val($(this).data("con_person_name"));
        $("#contact_no").val($(this).data("contact_no"));
        $("#address").val($(this).data("address"));
        $("#gst").val($(this).data("gst"));
        $("#state").val($(this).data("state"));
        let option = "<option>" + $(this).data("city") + "</option>";
        $("#city").html(option);
        $("#pincode").val($(this).data("pincode"));
        $("#country").val($(this).data("country"));
        $("#email").val($(this).data("email"));
        $("#license_no").val($(this).data("license_no"));
        $("#modal_name").text("Update Vendor");
        $("#btn_save").attr("name", "updatevendor");

        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Vendor");
        $("#company_id").val("");
        $("#con_person_name").val("");
        $("#contact_no").val("");
        $("#address").val("");
        $("#gst").val("");
        $("#state").val("");
        $("#city").val("");
        $("#pincode").val("");

        $("#btn_save").attr("name", "addvendor");
        $("#exampleModal").modal("show");
    })

    $(".state").on("change", function() {
        state = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {

                $("#city").html(data);

            }
        });
    })
</script>