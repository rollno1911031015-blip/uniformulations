<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {



    if (!empty($_POST['id'])) {
        if (empty($_POST['company_name'])) {
            alert("Enter  Name.", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("UPDATE  designation set name=? where id=?");
            $stmt->bind_param("si", $_POST['company_name'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Save successfully', 'success');
            redirect("?");
        }
    } else {
        if (empty($_POST['company_name'])) {
            alert("Enter  Name.", "error", "error");
            redirect("?");
        } else {


            $stmt = $mysqli->prepare("INSERT into designation (name) values (?) ;");
            $stmt->bind_param("s", $_POST['company_name']);
            $stmt->execute();
            $stmt->close();

            alert("Save successfully.", "success", "success");
            redirect("?");
        }
    }
}



$per = permissions(1, $mysqli, $user);


?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Designation</h4>
        </div>
        <div class="">

            <?php
            if ($per['edit'] == 1) {
            ?>


                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Designation</button>
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
                    <th> Name</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <?php
            if ($per['edit'] == 1) {
            ?>
                    <th>Action</th>
                    <?php }?>

                </tr>
            </thead>
            <tbody>
                <?php

                $stmt = $mysqli->prepare("SELECT * from `designation`");
                // $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';

                                        
                                        if ($per['edit'] == 1) {
                                            echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-company_name="' . $row['name'] . '"><i class="fa fa-pencil"></i></button></td>';
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
        <form class="row g-3 needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Designation</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="uid">
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="" required>

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
<?php include("../layouts/footer.php"); ?>

<script>
    $(document).on("click", ".edit_company", function() {
        $("#uid").val($(this).data("id"));
        $("#company_name").val($(this).data("company_name"));
        $("#modal_name").text("Update Designation");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Designation");
        $("#company_name").val("");

        $("#exampleModal").modal("show");
    })
</script>