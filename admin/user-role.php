<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {
    echo '<pre>';
    print_r($_POST);
    foreach ($_POST as $key) {
    }
    die;

    if (!empty($_POST['id'])) {

        if (empty($_POST['checked'])) {
            alert('Select at least one permission', 'error');
            redirect("?");
        }

        $ids = implode(", ", $_POST['checked']);
        $stmt = $mysqli->prepare("UPDATE  role set permission=? where id=?");
        $stmt->bind_param("si", $ids, $_POST['id']);
        $stmt->execute();
        $stmt->close();
        alert('Save successfully', 'success');
        redirect("?");
    }
}

 
?>

<div class="card">
    <div class="card-header ">
        <div class="page-title">
            <h4>User Role</h4>
        </div>

        <div class="col-4">
            <form method="GET" id="form">
                <select name="role_id" id="role_id" class="form-control">
                    <option value="">Select</option>
                    <?php
                    $stmt = $mysqli->prepare("SELECT * from `role`");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['role'] . '</option>';
                    }
                    ?>

                </select>
            </form>
        </div>

    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>S.no</th>


                    <th>Permission</th>
                    <th>View</th>
                    <th>Edit</th>

                </tr>
            </thead>
            <tbody>
                <?php

                $stmt = $mysqli->prepare("SELECT * from permission where role_id=?");
                $stmt->bind_param("i", $_GET['role_id']);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    $edit="";
                    if($row['edit']==1){
                        $edit="checked";
                    }
                    $view="";
                    if($row['view']==1){
                        $view="checked";
                    }
                    echo '<tr>
                            <td>' . $no++ . '</td>
                            <td>' . $row['name'] . '</td>
                            <td><input type="checkbox"  class="permission" data-id="' . $row['id'] . '" data-type="view" '.$view.'></td>
                            <td><input type="checkbox" class="permission" data-id="' . $row['id'] . '" data-type="edit" '.$edit.'></td>
                          
                        </tr>';
                }
                ?>

            </tbody>
        </table>
    </div>

</div>



<?php include("../layouts/footer.php"); ?>

<script>
    $(document).on("change", "#role_id", function() {


        $("#form").submit();
    });

    $(document).on("click", ".permission", function() {


        var id = $(this).data("id");
        var type = $(this).data("type");
        var check = 0;
        if ($(this).prop("checked")) {
            check = 1;
        } else {
            check = 0;
        }



        $.ajax({
            method: 'POST',
            url: 'ajax/update-permissions.php',
            data: {
                id: id,
                type: type,
                check: check

            },
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

    $("#role_id").val("<?=$_GET['role_id']?>")
</script>