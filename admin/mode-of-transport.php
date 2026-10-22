<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {

    if (!empty($_POST['id'])) {
        if (empty($_POST['category'])) {
            alert("Enter  Name.", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("UPDATE  mode_of_transport set name=?,contact_person=?,mobile_no=?,address=? where id=?");
            $stmt->bind_param("ssssi", $_POST['category'],$_POST['contact_person'],$_POST['mobile_no'],$_POST['address'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Save successfully', 'success');
            redirect("?");
        }
    } else {
        if (empty($_POST['category'])) {
            alert("Enter  Name.", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("INSERT into mode_of_transport (name,contact_person,mobile_no,address) values (?,?,?,?) ;");
            $stmt->bind_param("ssss", $_POST['category'],$_POST['contact_person'],$_POST['mobile_no'],$_POST['address']);
            $stmt->execute();
            $stmt->close();

            alert("Save successfully.", "success", "success");
            redirect("?");
        }
    }
}

 
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Mode of transport</h4>
        </div>
        <div class="">


            <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Mode of Transport</button>


        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transport Name</th>
                        <th>Contact Person Name</th>
                        <th>Mobile No</th>
                        <th>Address</th>
                      
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from `mode_of_transport` order by id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['contact_person'] . '</td>
                                        <td>' . $row['mobile_no'] . '</td>
                                        <td>' . $row['address'] . '</td>
                                    
                                        
                                        <td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" 
                                        data-category="' . $row['name'] . '"
                                        data-contact_person="' . $row['contact_person'] . '"
                                        data-mobile_no="' . $row['mobile_no'] . '"
                                        data-address="' . $row['address'] . '"
                                        
                                        ><i class="fa fa-pencil"></i></button></td>
                                    </tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </div>

</div>



<div class="modal fade" id="exampleModal">
    <div class="modal-dialog">
        <form class="row g-3 needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Mode of Transport</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md-12">
                        <label for="validationCustom01" class="form-label"> Transport Name</label>
                        <input type="text" class="form-control" id="category" name="category" value="" required>

                    </div>

                    <div class="col-md-12">
                        <label for="validationCustom01" class="form-label"> Contact person name</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="" required>

                    </div>
                    <div class="col-md-12">
                        <label for="validationCustom01" class="form-label"> Mobile Number</label>
                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="" required>

                    </div>
                    <div class="col-md-12">
                        <label for="validationCustom01" class="form-label"> Address</label>
                        <textarea class="form-control" id="address" name="address" value="" required></textarea>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave" id="btn_save">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>







<?php include("../layouts/footer.php"); ?>
<script>
    $(document).on("click", ".edit_company", function() {
        $("#id").val($(this).data("id"));
        $("#category").val($(this).data("category"));
        $("#contact_person").val($(this).data("contact_person"));
        $("#mobile_no").val($(this).data("mobile_no"));
        $("#address").val($(this).data("address"));
        $("#modal_name").text("Update ");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add ");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });
</script>