<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {


    if (!validate_phone($_POST['contact_details'])) {
        alert("Invalid mobile number", "error", "error");
        redirect("?");
    }

    if (!empty($_POST['id'])) {

        if (empty($_POST['name'])) {
            alert("name is required.", "error", "error");
            redirect("store-location");
        } else {
            $stmt = $mysqli->prepare("UPDATE  locations set name=?,address=?,contact_details=? where id=?");
            $stmt->bind_param("sssi", $_POST['name'], $_POST['address'], $_POST['contact_details'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('location updated successfully', 'success');
            redirect("store-location");
        }
    } else {
        if (empty($_POST['name'])) {
            alert("Name is required", "error", "error");
            redirect("store-location");
        } else {


            $stmt = $mysqli->prepare("INSERT into locations (name,address,contact_details) values (?,?,?) ;");
            $stmt->bind_param("sss", $_POST['name'], $_POST['address'], $_POST['contact_details']);
            $stmt->execute();
            $stmt->close();

            alert("location created successfully.", "success", "success");
            redirect("store-location");
        }
    }
}
$per = permissions(2, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Store Location List</h4>
        </div>
        <div class="">

            <?php
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Store Location</button>
            <?php
            }
            ?>


        </div>
    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>S.no</th>

                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact </th>

                    <th>Created Date</th>
                    <?php
            if ($per['edit'] == 1) {
            ?>

                    <th>Action</th>
                    <?php }?>

                </tr>
            </thead>
            <tbody>
                <?php

                $stmt = $mysqli->prepare("SELECT * from locations");
                // $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['address'] . '</td>
                                        <td>' . $row['contact_details'] . '</td>
 
                                        <td>' . $row['created_date'] . '</td>';

                                        
                                        if ($per['edit'] == 1) {
                                        echo '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-name="' .
                        $row['name'] . '" 
                                        data-address="' . $row['address'] . '"
                                        data-contact_details="' . $row['contact_details'] . '"
    
                                        
                                        ><i class="fa fa-pencil"></i></button></td>';
                                        }

                                   echo  '</tr>';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Location</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="row">



                        <div class="col-md-6">
                            <label> Name</label>
                            <input type="text" name="name" id="u_name" class="form-control" required>

                        </div>
                        <div class="col-md-6">
                            <label>Address</label>
                            <input type="text" name="address" id="address" class="form-control" required>

                        </div>
                        <div class="col-md-6">
                            <label>Contact</label>
                            <input type="text" name="contact_details" id="contact_details" class="form-control" required>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<?php include("../layouts/footer.php"); ?>
<script>
    $(document).on("click", ".edit_company", function() {
        $("#id").val($(this).data("id"));
        $("#u_name").val($(this).data("name"));
        $("#contact_details").val($(this).data("contact_details"));

        $("#address").val($(this).data("address"));

        $("#modal_name").text("Update Location");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Location");
        $("#name").val("");
        $("#contact_details").val("");

        $("#address").val("");
        $("#id").val("");


        $("#exampleModal").modal("show");
    });
</script>