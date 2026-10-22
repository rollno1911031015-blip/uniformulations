<?php
include("../layouts/header.php");

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
        $stmt = $mysqli->prepare("SELECT * from `sliders` where id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
        $file_url = $company['image'];
    }

    if (!empty($_POST['id'])) { {
            $stmt = $mysqli->prepare("UPDATE  sliders set image=? where id=?");
            $stmt->bind_param("si", $file_url, $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Save successfully', 'success');
            redirect("?");
        }
    } else { {
            $stmt = $mysqli->prepare("INSERT into sliders (image) values (?) ;");
            $stmt->bind_param("s", $file_url);
            $stmt->execute();
            $stmt->close();

            alert("Save successfully.", "success", "success");
            redirect("?");
        }
    }
}


if(isset($_POST['btnDelete'])){
    $stmt = $mysqli->prepare("DELETE from sliders where id=? ;");
    $stmt->bind_param("i", $_POST['btnDelete']);
    $stmt->execute();
    $stmt->close();

    alert("Save successfully.", "success", "success");
    redirect("?");

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

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Image</button>

            <?php } ?>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>

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

                    $stmt = $mysqli->prepare("SELECT * from `sliders` order by id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                                        <td><img src="' . $row['image'] . '" width="75px"></td>
                                 
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';
                        if ($per['edit'] == 1) {

                            echo  '
                            <td>
                            <button type="button" class="btn btn-primary edit_company btn-sm" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                            <form method="post">
                            <button type="submit" name="btnDelete" class="btn btn-danger  btn-sm" id=""  value="' . $row['id'] . '" ><i class="fa fa-trash"></i></button>
                            </form>
                            
                            </td>';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Division</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Image</label>
                        <input type="file" class="form-control" id="file" name="file" value="">

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
        $("#modal_name").text("Update Slider");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Slider");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });

 
</script>