<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {

    if (!empty($_POST['id'])) {
        if (empty($_POST['category'])) {
            alert("Enter  Name.", "error", "error");
            redirect("unit-type");
        } else {
            $stmt = $mysqli->prepare("UPDATE  unit_type set name=? where id=?");
            $stmt->bind_param("si", $_POST['category'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('updated successfully', 'success');
            redirect("unit-type");
        }
    } else {
        if (empty($_POST['category'])) {
            alert("Enter  Name.", "error", "error");
            redirect("unit-type");
        } else {
            $stmt = $mysqli->prepare("INSERT into unit_type (name) values (?) ;");
            $stmt->bind_param("s", $_POST['category']);
            $stmt->execute();
            $stmt->close();

            alert("Save  successfully.", "success", "success");
            redirect("unit-type");
        }
    }
}


$per = permissions(3, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Unit Type</h4>
        </div>
        <div class="">
            <?php
            if ($per['edit'] == 1) {
            ?>

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Unit</button>
            <?php } ?>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th> Name</th>
                        <th>Created Date</th>
                        <?php
                        if ($per['edit'] == 1) {
                        ?>

                            <th>Action</th>
                        <?php } ?>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from `unit_type` order by id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>';


                        if ($per['edit'] == 1) {
                            echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-category="' . $row['name'] . '"><i class="fa fa-pencil"></i></button></td>';
                        }

                        echo  '</tr>';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Unit</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Name</label>
                        <input type="text" class="form-control" id="category" name="category" value="" required>

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
        $("#modal_name").text("Update unit");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add unit");
        $("#category").val("");

        $("#exampleModal").modal("show");
    })
</script>