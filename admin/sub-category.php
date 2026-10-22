<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {


    try {


        if (!empty($_POST['id'])) {


            if (empty($_POST['category'])) {
                alert("Enter sub category Name.", "error", "error");
                redirect("sub-category");
            } else {
                $stmt = $mysqli->prepare("UPDATE   sub_categories set name=?,category_id=? where id=?");
                $stmt->bind_param("ssi", $_POST['category'], $_POST['category_id'], $_POST['id']);
                $stmt->execute();
                $stmt->close();
                alert('sub category updated successfully', 'success');
                redirect("sub-category");
            }
        } else {




            if (empty($_POST['category'])) {
                alert("Enter sub category Name.", "error", "error");

                redirect("sub-category");
            } else {


                $stmt = $mysqli->prepare("INSERT into  sub_categories (name,category_id) values (?,?) ;");
                $stmt->bind_param("ss", $_POST['category'], $_POST['category_id']);
                $stmt->execute();
                $stmt->close();

                alert(" sub Category name created successfully.", "success", "success");
                redirect("sub-category");
            }
        }
    } catch (Exception $e) {
        alert($e->getMessage(), 'error');
        redirect("sub-category");
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

            if ($i >= 1 && !empty($filesop[1])) {

                $category = $filesop[0];
                $sub_category = $filesop[1];

                $count++;
                $stmt = $mysqli->prepare("SELECT * from categories where name=? limit 1");
                $stmt->bind_param('s', $category);
                $stmt->execute();
                $categories = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($categories) {

                    $stmt = $mysqli->prepare("SELECT * from  sub_categories where name=? limit 1");
                    $stmt->bind_param('s', $sub_category);
                    $stmt->execute();
                    $sub_categories = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    if ($sub_categories) {
                        $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                        $duplicate++;
                    } else {

                        try {
                            $stmt = $mysqli->prepare("INSERT INTO sub_categories (name,category_id) VALUES (?,?)");
                            $stmt->bind_param("si",   $sub_category, $categories['id']);
                            $stmt->execute();
                            $success++;
                            $error .= "Lead  row " . $i . " added successfully..<br>";
                        } catch (Exception $e) {
                            alert($e->getMessage(), "error", "error");
                            echo $e->getMessage();
                        }
                    }
                } else {
                    $error .= 'Brand Not Found';
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


?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Sub Category List</h4>
        </div>
        <div class="">


            <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Sub Category</button>

            <button type="button" class="btn btn-secondary import-sub-category"><i class="fa fa-download"></i> Import Sub Category</button>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Category Name</th>
                        <th> Sub Category Name</th>

                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT a.*,b.id as category_id,b.name as category from sub_categories a JOIN categories b on a.category_id=b.id order by a.id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['category'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                     
                                        
                                        <td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-category="' . $row['name'] . '"  data-category_id="' . $row['category_id'] . '"><i class="fa fa-pencil"></i></button></td>
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Company</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Select Category</label>
                        <select class="form-control" id="category_id" name="category_id" value="" required>
                            <option value="">Select</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `categories`");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $category = $stmt->get_result();
                            while ($row = $category->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }

                            ?>
                        </select>

                    </div>


                    <div class="col-md mt-3">
                        <label for="validationCustom01" class="form-label"> Name</label>
                        <input type="text" class="form-control" id="category" name="category" value="" required>

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



<div class="modal fade" id="import-sub-category">
    <div class="modal-dialog">
        <form class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Import Sub Category</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="import-sub-category-sample.csv" class="btn btn-success btn-sm float-end">Download</a>

                    <div>
                        <label for="">Import Sub Category</label>
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

        $("#category_id").val($(this).data("category_id"));

        $("#modal_name").text("Update  Sub Category");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Sub Category");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });

    $(".import-sub-category").on("click", function() {
        $("#import-sub-category").modal("show");
    })
</script>