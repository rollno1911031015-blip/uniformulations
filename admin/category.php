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
        $stmt = $mysqli->prepare("SELECT * from `categories` where id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
        $file_url = $company['image'];
    }
    if (!empty($_POST['id'])) {
        if (empty($_POST['category'])) {
            alert("Enter category Name.", "error", "error");
            redirect("category");
        } else {
            $stmt = $mysqli->prepare("UPDATE  categories set name=?,image=? where id=?");
            $stmt->bind_param("ssi", $_POST['category'], $file_url, $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('category updated successfully', 'success');
            redirect("category");
        }
    } else {
        if (empty($_POST['category'])) {
            alert("Enter Category Name.", "error", "error");
            redirect("category");
        } else {


            $stmt = $mysqli->prepare("INSERT into categories (name,brand_id,image) values (?,?,?) ;");
            $stmt->bind_param("sis", $_POST['category'], $_POST['brand_id'], $file_url);
            $stmt->execute();
            $stmt->close();

            alert("Category name created successfully.", "success", "success");
            redirect("category");
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

            if ($i >= 1 && !empty($filesop[1])) {

                $brand = $filesop[0];
                $category = $filesop[1];

                $count++;
                $stmt = $mysqli->prepare("SELECT * from brand where name=? limit 1");
                $stmt->bind_param('s', $brand);
                $stmt->execute();
                $brands = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($brands) {

                    // $stmt = $mysqli->prepare("SELECT * from  categories where name=? limit 1");
                    // $stmt->bind_param('s', $category);
                    // $stmt->execute();
                    // $categories = $stmt->get_result()->fetch_assoc();
                    // $stmt->close();
                    // if ($categories) {
                    //     $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                    //     $duplicate++;
                    // } else 
                    
                    {

                        try {
                            $stmt = $mysqli->prepare("INSERT INTO categories (name,brand_id) VALUES (?,?)");
                            $stmt->bind_param("si",   $category, $brands['id']);
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

$per = permissions(3, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Category List</h4>
        </div>
        <div class="">
            <?php
            if ($per['edit'] == 1) {
            ?>

                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Category</button>
                <button type="button" class="btn btn-secondary import-category"><i class="fa fa-download"></i> Import Category</button>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Image</th>
                        <th>Division Name</th>
                        <th>Category Name</th>
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

                    $stmt = $mysqli->prepare("SELECT a.*,b.name as brand from categories a join brand b on a.brand_id=b.id order by a.id desc");
                    // $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                                       <td><img src="' . $row['image'] . '" width="75px"></td>
                                        <td>' . $row['brand'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';

                        if ($per['edit'] == 1) {
                            echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-category="' . $row['name'] . '" data-brand_id="' . $row['brand_id'] . '"><i class="fa fa-pencil"></i></button></td>';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Company</span></h5>
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

                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Select Division</label>
                        <select class="form-control" id="brand_id" name="brand_id" value="" required>
                            <option value="">Select</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `brand`");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $category = $stmt->get_result();
                            while ($row = $category->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }

                            ?>
                        </select>

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


<div class="modal fade" id="import-category">
    <div class="modal-dialog">
        <form class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Import Category</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="import-category-sample.csv" class="btn btn-success btn-sm float-end">Download</a>

                    <div>
                        <label for="">Import Category</label>
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
        $("#brand_id").val($(this).data("brand_id"));
        $("#modal_name").text("Update Category");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Category");
        $("#category").val("");

        $("#exampleModal").modal("show");
    });
    $(".import-category").on("click", function() {
        $("#import-category").modal("show");
    })
</script>