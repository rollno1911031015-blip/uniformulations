<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {




    $error = '';
 

    if (!empty($_POST['id'])) {
        if (empty($_POST['days'])) {
            alert("Enter category Name.", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("UPDATE  days set days=? where id=?");
            $stmt->bind_param("ii", $_POST['days'],$_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Save successfully', 'success');
            redirect("?");
        }
    } else {
        if (empty($_POST['days'])) {
            alert("Enter Days .", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("INSERT into days (days) values (?) ;");
            $stmt->bind_param("i", $_POST['days']);
            $stmt->execute();
            $stmt->close();

            alert("Save successfully.", "success", "success");
            redirect("?");
        }
    }
}


$per = permissions(3, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Division List</h4>
        </div>
        <div class="">
            <?php
            if ($per['edit'] == 1) {
            ?>

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Days</button>
               
            <?php } ?>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                 
                        <th>Days</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <?php
                        if ($per['edit'] == 1) {
                        ?>
                            <th>Action</th>
                        <?php  } ?>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from `days`");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                             
                                        <td>' . $row['days'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';
                        if ($per['edit'] == 1) {

                            echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company" 
                             data-id="' . $row['id'] . '" 
                            data-days="' . $row['days'] . '"><i class="fa fa-pencil"></i></button></td>';
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
        <form class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Days</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Days</label>
                        <input type="text" class="form-control" id="days" name="days" value="" required>

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
        $("#days").val($(this).data("days"));
        $("#modal_name").text("Update Days");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Days");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });

    $(".import-brand").on("click", function() {


        $("#import-brand").modal("show");
    })
</script>