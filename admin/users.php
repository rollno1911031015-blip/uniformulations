<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {
    $error = '';


    if (!validate_phone($_POST['mobile'])) {
        alert("Invalid mobile number", "error", "error");
        redirect("?");
    }

    if (!empty($_POST['id'])) {
        if (empty($_POST['username'])) {
            alert("Enter user Name.", "error", "error");
            redirect("users");
        } else {
            $ids = implode(', ', $_POST['division']);
            $stmt = $mysqli->prepare("UPDATE  admin_users set name=?,mobile=?,password=?,username=?,role=?,designation=?,division=?,parent_id=? where id=?");
            $stmt->bind_param("ssssiisii", $_POST['category'], $_POST['mobile'], $_POST['t_password'], $_POST['username'], $_POST['role'], $_POST['designation'], $ids, $_POST['parent_id'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('user updated successfully', 'success');
            redirect("users");
        }
    } else {
        if (empty($_POST['username'])) {
            alert("Enter user Name.", "error", "error");
            redirect("users");
        } else {

            $ids = implode(', ', $_POST['division']);

            $stmt = $mysqli->prepare("INSERT into admin_users (name,mobile,password,username,role,designation,division,parent_id) values (?,?,?,?,?,?,?,?) ;");
            $stmt->bind_param("ssssiisi", $_POST['category'], $_POST['mobile'], $_POST['t_password'], $_POST['username'], $_POST['role'], $_POST['designation'], $ids, $_POST['parent_id']);
            $stmt->execute();
            $stmt->close();

            alert("user  created successfully.", "success", "success");
            redirect("users");
        }
    }
}

$per = permissions(1, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>User List</h4>
        </div>
        <div class="">
            <?php

            if ($per['edit'] == 1) {
            ?>

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add user</button>
            <?php } ?>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Mobile</th>
                        <th>Role</th>
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

                    $stmt = $mysqli->prepare("SELECT a.*,b.role from admin_users a  join role b on a.role=b.id ");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['id'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['username'] . '</td>
                                        <td>' . $row['mobile'] . '</td>
                                        <td>' . $row['role'] . '</td>
                                        <td>' . $row['created_at'] . '</td>';


                        if ($per['edit'] == 1) {
                            echo '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-category="' . $row['name'] . '" data-mobile="' . $row['mobile'] . '" data-password="' . $row['password'] . '" 
                                        data-t_password="' . $row['password'] . '"
                                        data-username="' . $row['username'] . '"
                                        data-role="' . $row['role'] . '"
                                        data-designation="' . $row['designation'] . '"
                                        data-division="' . $row['division'] . '"
                                        data-parent_id="' . $row['parent_id'] . '"
                                        
                                        ><i class="fa fa-pencil"></i></button></td>';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add User</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Name</label>
                            <input type="text" class="form-control" id="category" name="category" value="" required>
                        </div>
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="" required>
                        </div>
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Mobile</label>
                            <input type="number" class="form-control" id="mobile" name="mobile" value="" required>

                        </div>

                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Role</label>
                            <select class="form-control" id="role" name="role" value="" required>
                                <option value="">Select</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `role` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();
                                $no = 1;
                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['role'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Designation</label>
                            <select class="form-control" id="designation" name="designation" value="" required>
                                <option value="">Select</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `designation` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();
                                $no = 1;
                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Reporting Manager</label>
                            <select class="form-control" id="parent_id" name="parent_id" value="" required>
                                <option value="">Select</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `admin_users` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();
                                $no = 1;
                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>

                        <div class="col-md-12">
                            <label for="validationCustom01" class="form-label"> Division</label>
                            <select class="form-control" id="division" name="division[]" value="" required multiple>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `brand` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();

                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>




                        <label>Password</label>
                        <div class="input-group has-validation">

                            <label for="Transactional Password"></label>

                            <input type="password" class="form-control pass-input " id="t_password" name="t_password" value="" required>
                            <span class="fas toggle-password fa-eye-slash btn btn-primary"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="BtnSave">Save changes</button>
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
        $("#mobile").val($(this).data("mobile"));
        $("#password").val($(this).data("password"));
        $("#t_password").val($(this).data("t_password"));
        $("#username").val($(this).data("username"));
        $("#role").val($(this).data("role"));
        $("#designation").val($(this).data("designation"));
        $("#parent_id").val($(this).data("parent_id"));


        var divisionValues = $(this).data("division").toString().split(', ');

        $("#division").val(divisionValues);

        $("#modal_name").text("Update user");

        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add User");
        $("#category").val("");
        $("#mobile").val("");
        $("#password").val("");
        $("#t_password").val("");

        $("#exampleModal").modal("show");
    })
</script>