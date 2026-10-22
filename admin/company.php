<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {



    if (!empty($_POST['id'])) {
        if (empty($_POST['company_name'])) {
            alert("Enter Company Name.", "error", "error");
            redirect("company.php");
        } else {
            $stmt = $mysqli->prepare("UPDATE  company set company_name=? where id=?");
            $stmt->bind_param("si", $_POST['company_name'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert('Company updated successfully', 'success');
            redirect("company.php");
        }
    } else {
        if (empty($_POST['company_name'])) {
            alert("Enter Company Name.", "error", "error");
            redirect("company.php");
        } else {


            $stmt = $mysqli->prepare("INSERT into company (company_name) values (?) ;");
            $stmt->bind_param("s", $_POST['company_name']);
            $stmt->execute();
            $stmt->close();

            alert("Company name created successfully.", "success", "success");
            redirect("company.php");
        }
    }
}


$per = permissions(2, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Company List</h4>
        </div>
        <div class="">

            <?php
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Company</button>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="d-flex justify-content-between">
                <div class="">
                    <select name="limit" id="limit" onchange="this.form.submit()" class="form-control">
                        <option value="20" <?php echo isset($_GET['limit']) && $_GET['limit'] == 20 ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo isset($_GET['limit']) && $_GET['limit'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['limit']) && $_GET['limit'] == 100 ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo isset($_GET['limit']) && $_GET['limit'] == 200 ? 'selected' : ''; ?>>200</option>
                        <option value="300" <?php echo isset($_GET['limit']) && $_GET['limit'] == 300 ? 'selected' : ''; ?>>300</option>
                        <option value="500" <?php echo isset($_GET['limit']) && $_GET['limit'] == 500 ? 'selected' : ''; ?>>500</option>
                    </select>

                </div>
                <div class="d-flex">
                    <input type="text" class="float-end form-control" value="<?php if(isset($_GET['search'])) echo $_GET['search'] ?>" name="search" id="search" placeholder="Search by keyword">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Company Name</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <?php
                    if ($per['edit'] == 1) {
                    ?>
                        <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $search = '';
                if (!empty($_GET['search'])) {
                    $search = '%' . $mysqli->real_escape_string($_GET['search']) . '%';
                } else {
                    $search = "%";
                }

                if (empty($_GET['limit'])) {
                    $_GET['limit'] = 20;
                }
                $recordsPerPage = $_GET['limit'];

                // Determine the current page
                if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                    $currentPage = intval($_GET['page']);
                } else {
                    $currentPage = 1;
                }

                $totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM company WHERE company_name LIKE ?");
                $totalRecordsQuery->bind_param("s", $search); // Bind search parameter
                $totalRecordsQuery->execute();
                $totalRecordsResult = $totalRecordsQuery->get_result();
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

                // Determine the total number of pages
                $totalPages = ceil($totalRecords / $recordsPerPage);

                // Calculate the starting record for the current page
                $startFrom = ($currentPage - 1) * $recordsPerPage;

                $stmt = $mysqli->prepare("SELECT * from  company where company_name like ? limit ?,?");
                $stmt->bind_param("sii", $search, $startFrom, $recordsPerPage);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['company_name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';



                    if ($per['edit'] == 1) {

                        echo  '<td><button type="button" class="btn btn-primary edit_company" id="edit_company"  data-id="' . $row['id'] . '" data-company_name="' . $row['company_name'] . '"><i class="fa fa-pencil"></i></button></td>';
                    }

                    echo  '</tr>';
                }
                ?>

            </tbody>
        </table>
        <nav aria-label="...">
            <ul class="pagination float-end">
                <?php

                $range = 2; // Number of pages before and after the current page to show

                if ($totalPages > 1) {
                    for ($page = max(1, $currentPage - $range); $page <= min($totalPages, $currentPage + $range); $page++) {
                        echo ($page == $currentPage) ? '<strong class="badge bg-success p-2 m-2">' . $page . '</strong> ' : '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '" class="badge bg-primary p-2 m-2">' . $page . '</a> ';
                    }

                    // Add ellipses if there are more pages
                    if ($currentPage - $range > 1) {
                        echo '... ';
                    }

                    // First page link
                    if ($currentPage - $range > 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit'] . '"  class="badge bg-warning p-2 m-2">1</a> ';
                    }

                    // Last page link
                    if ($currentPage + $range < $totalPages - 1) {
                        echo '... <a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '" class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
                    }
                }


                ?>



            </ul>
        </nav>
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

                    <input type="hidden" name="id" id="uid">
                    <div class="col-md">
                        <label for="validationCustom01" class="form-label"> Company Name</label>
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
        $("#modal_name").text("Update Company");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Company");
        $("#company_name").val("");

        $("#exampleModal").modal("show");
    })
</script>