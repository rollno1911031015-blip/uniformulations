<?php
include("../layouts/header.php");

$per = permissions(3, $mysqli, $user);
?>
<style>
 /* For WebKit browsers */
::-webkit-scrollbar {
  width: 10px; /* Width of the scrollbar */
 
}
 

</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Product List</h4>
        </div>
        <div class="">
            <?php
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Product</button>
                <a class="btn btn-secondary" href="import-product"><i class="fa fa-download" aria-hidden="true"></i> Import Product </a>
            <?php  } ?>


        </div>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="d-flex justify-content-between">
                <div class="">
                <select name="division" id="division" onchange="this.form.submit()">
                <?php
                    $stmt = $mysqli->prepare("SELECT * from  brand ");
                    //$stmt->bind_param("ssi",$_POST['name'],$_POST['password'],$user['id']);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $stmt->close();
                    $sno = 1;
                    while ($row = $res->fetch_assoc()) {
                        ?>
                        <option value="<?=$row['name']?>" <?php echo isset($_GET['division']) && $_GET['division'] == $row['name'] ? 'selected' : ''; ?>><?=$row['name']?></option>
                        <?php } ?>
                       
                    </select>
                    <select name="limit" id="limit" onchange="this.form.submit()">
                        <option value="20" <?php echo isset($_GET['limit']) && $_GET['limit'] == 20 ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo isset($_GET['limit']) && $_GET['limit'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['limit']) && $_GET['limit'] == 100 ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo isset($_GET['limit']) && $_GET['limit'] == 200 ? 'selected' : ''; ?>>200</option>
                        <option value="300" <?php echo isset($_GET['limit']) && $_GET['limit'] == 300 ? 'selected' : ''; ?>>300</option>
                        <option value="500" <?php echo isset($_GET['limit']) && $_GET['limit'] == 500 ? 'selected' : ''; ?>>500</option>
                    </select>

                </div>
                <div class="d-flex">


                    <input type="text" class="float-end form-control" id="searchInput" value="<?= $_GET['search'] ?? "" ?>" name="search" placeholder="Search by keyword">
                    <button type="submit" class="float-end btn btn-primary" placeholder="Search by keyword">Search</button>
                </div>
            </div>
        </form>
        <table class="table mydataTableProducts">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Image</th>
                    <th>Division</th>
                    <th>Category</th>


                    <th>Name</th>
                    <th>Composition</th>
                    <th>Article No.</th>



                    <th>Price</th>
                    <th>UOM</th>
                    <th>Opening Stock</th>
                    <th>min Stock</th>
                    <th>Warranty </th>
                    <th>Company </th>
                    <th>Packing </th>
                    <th>Location </th>
                    <th>Location 2 </th>
                    <th>Sale Rate </th>

                    <th>Cost </th>
                    <th>Generic </th>

                    <th>H.Scm </th>
                    <th>G.Scm </th>
                    <th>Fix Dis </th>
                    <th>CGST </th>
                    <th>SGST </th>
                    <th>IGST </th>
                    <th>Cess </th>
                    <th>Narcotic Item </th>
                    <th>Food License </th>

                    <th>New Launch</th>
                    <th>Active</th>

                    <?php
                    if ($per['edit'] == 1) {
                    ?>
                        <th>Action</th>
                    <?php  } ?>

                </tr>
            </thead>
            <tbody>
                <?php
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

                $search = "%" . $_GET['search'] . "%";

                    $division="";
                if(!empty($_GET['division'])){
                    $division=$_GET['division'];

                }

                // Determine the total number of records
                $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM products WHERE name LIKE ? and brand=?");
                $stmt->bind_param("ss", $search,$division);
                $stmt->execute();
                $result = $stmt->get_result();
                $totalRecords = $result->fetch_assoc()['total'];

                // Determine the total number of pages
                $totalPages = ceil($totalRecords / $recordsPerPage);

                // Calculate the starting record for the current page
                $startFrom = ($currentPage - 1) * $recordsPerPage;

                // Prepare and execute the query with pagination
                $stmt = $mysqli->prepare("SELECT * FROM products WHERE name LIKE ? and brand=? ORDER BY id DESC LIMIT ?, ?");
                $stmt->bind_param("ssii", $search, $division,$startFrom, $recordsPerPage);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                          <td><img src="' . $row['image'] . '" width="75px"></td>
                                        <td>' . $row['brand'] . '</td>
                                        <td>' . $row['category'] . '</td>
                                      
                                       
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['composition'] . '</td>
                                        <td>' . $row['product_code'] . '</td>
                                     
                                       
 
                                        <td>' . $row['price'] . '</td>
                                        <td>' . $row['uom'] . '</td>
                                        <td>' . $row['opening_stock'] . '</td>
                                        <td>' . $row['min_stock'] . '</td>
                                        <td>' . $row['warranty_days'] . '</td>
                                        <td>' . $row['company'] . '</td>
                                        <td>' . $row['packing'] . '</td>
                                        <td>' . $row['location'] . '</td>
                                        <td>' . $row['location2'] . '</td>
                                        <td>' . $row['sale_rete'] . '</td>
                                      
                                        <td>' . $row['cost'] . '</td>
                                        <td>' . $row['generic'] . '</td>
                                      
                                        <td>' . $row['hscm'] . '</td>
                                        <td>' . $row['qscm'] . '</td>
                                        <td>' . $row['fix_dis'] . '</td>
                                        <td>' . $row['cgst'] . '</td>
                                        <td>' . $row['sgst'] . '</td>
                                        <td>' . $row['igst'] . '</td>
                                        <td>' . $row['cess'] . '</td>
                                        <td>' . $row['narcotic_item'] . '</td>
                                        <td>' . $row['food_license'] . '</td>
                                        <td>';
                                        if ($row['new_launch'] == '1')
                                        echo '<i class="fa fa-check text-success"></i>';
                                    else echo '<i class="fa fa-times text-danger"></i>';
                                    echo '</td><td>';
                    if ($row['is_active'] == '1')
                        echo '<i class="fa fa-check text-success"></i>';
                    else echo '<i class="fa fa-times text-danger"></i>';
                    echo '</td>';
                    if ($per['edit'] == 1) {



                        echo  '<td><button type="button" class="btn btn-primary edit_company btn-sm" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                                        <button type="button" data-id="' . $row['id'] . '" class="btn btn-danger Delete btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </td>';
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
                        echo ($page == $currentPage) ? '<strong class="badge bg-success p-2 m-2">' . $page . '</strong> ' : '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '&search=' . $_GET['search'] . '&division=' . $_GET['division'] . '"   class="badge bg-primary p-2 m-2">' . $page . '</a> ';
                    }

                    // Add ellipses if there are more pages
                    if ($currentPage - $range > 1) {
                        echo '... ';
                    }

                    // First page link
                    if ($currentPage - $range > 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit'] . '&search=' . $_GET['search'] . '&division=' . $_GET['division'] . '"  class="badge bg-warning p-2 m-2">1</a> ';
                    }

                    // Last page link
                    if ($currentPage + $range < $totalPages - 1) {
                        echo '... <a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '&search=' . $_GET['search'] . '&division=' . $_GET['division'] . '" class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
                    }
                }


                ?>



            </ul>
        </nav>
    </div>

</div>

<div class="modal fade" id="DeleteModal">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalTitleId">
                        Delete Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Are you sure you want to delete this item?</h4>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="SendOTP">SendOTP</button>
                </div>
            </div>
        </form>
    </div>
</div>




<?php include("../layouts/footer.php"); ?>

<script>
    $(".mydataTableProducts").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": true,

        "searching": true, // Disables the search bar
        "paging": false, // Enables pagination
        "info": false,

    }).buttons().container().appendTo('.col-md-6:eq()');

    $(document).on("click", ".edit_company", function() {
        var id = $(this).data("id");
        $("#product_id").val(id)
        $.ajax({
            method: 'POST',
            url: 'ajax/get-product-details.php',
            data: {
                id: id
            },

            success: function(data) {

                data = JSON.parse(data);

                $.each(data, function(i, o) {
                    console.log(i, o)

                    $('input[name=' + i + ']').val(o);
                    $('select[name=' + i + ']').val(o);
                });


                $("#modal_name").text("Update Product");
                $("#btn_save").attr("name", "updatevendor");

                $("#add_product_modal").modal("show");

            }
        });

    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Product");
        $("#name").val("");
        $("#category").val("");
        $("#subcategory").val("");

        $("#price").val("");
        $("#stock").val("");
        $("#active").val("");
        $("#warranty").val("");
        $("#modal_name").text("Add Product");
        $("#id").val("");

        $("#btn_save").attr("name", "addvendor");
        $("#add_product_modal").modal("show");
    });
    $(document).on("click", ".Delete", function() {
        $("#DeleteModal").modal("show");
    });
</script>