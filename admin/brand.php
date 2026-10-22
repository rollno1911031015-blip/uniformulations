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
    }else{
        $stmt = $mysqli->prepare("SELECT * from `brand` where id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
        $file_url=$company['image'];
    }

    if (!empty($_POST['id'])) {
        if (empty($_POST['category'])) {
            alert("Enter category Name.", "error", "error");
            redirect("brand");
        } else {
            $stmt = $mysqli->prepare("UPDATE  brand set name=?,image=? where id=?");
            $stmt->bind_param("ssi", $_POST['category'], $file_url,$_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('category updated successfully', 'success');
            redirect("brand");
        }
    } else {
        if (empty($_POST['category'])) {
            alert("Enter Category Name.", "error", "error");
            redirect("brand");
        } else {
            $stmt = $mysqli->prepare("INSERT into brand (name,image) values (?,?) ;");
            $stmt->bind_param("ss", $_POST['category'],$file_url);
            $stmt->execute();
            $stmt->close();

            alert("Category name created successfully.", "success", "success");
            redirect("brand");
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

            if ($i >= 1 && !empty($filesop[0])) {

                $brand = $filesop[0];




                $count++;


                $stmt = $mysqli->prepare("SELECT * from brand where name=? limit 1");
                $stmt->bind_param('s', $brand);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($res) {
                    $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                    $duplicate++;
                } else {
                    try {
                        $stmt = $mysqli->prepare("INSERT INTO brand (name) VALUES (?)");
                        $stmt->bind_param("s",   $brand);
                        $stmt->execute();
                        $success++;
                        $error .= "Lead  row " . $i . " added successfully..<br>";
                        alert("Save Successfully", "success", "success");
                    } catch (Exception $e) {
                        alert($e->getMessage(), "error", "error");
                        echo $e->getMessage();
                    }
                }
            } else {
                $error .= 'Invalid format <br>';
            }
            $i++;
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        alert($msg, 'info');

        fclose($handle);
    }
    redirect('?');
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

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Division</button>
                <button type="button" class="btn btn-secondary import-brand"><i class="fa fa-download"></i> Import Division</button>
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
                        <th>Division Name</th>
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

                    $stmt = $mysqli->prepare("SELECT * from `brand` order by id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                                        <td><img src="'. $row['image'] .'" width="75px"></td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';
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
                        <input type="file" class="form-control" id="file" name="file" value="" >

                    </div>
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






<div class="modal fade" id="import-brand">
    <div class="modal-dialog">
        <form class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Import Division</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="import-brand-sample.csv" class="btn btn-success btn-sm float-end">Download</a>

                    <div>
                        <label for="">Import Division</label>
                        <input type="file" class="form-control" name="file">

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="btnImport" id="">Save changes</button>
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
        $("#modal_name").text("Update Category");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Brand");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });

    $(".import-brand").on("click", function() {


        $("#import-brand").modal("show");
    })
</script>