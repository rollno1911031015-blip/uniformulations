<?php
include("../layouts/header.php");
if (isset($_POST['BtnSave'])) {



    if (!empty($_POST['id'])) {
        if (empty($_POST['amount'])) {
            alert("Enter Amount.", "error", "error");
            redirect("?");
        } else {
            $stmt = $mysqli->prepare("UPDATE  direct_sale set customer_id=?,division_id=?,sale_date=?,amount=? where id=?");
            $stmt->bind_param("iisdi", $_POST['customer_id'], $_POST['division_id'],  $_POST['sale_date'], $_POST['amount'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
            alert(' updated successfully', 'success');
            redirect("?");
        }
    } else {
        if (empty($_POST['amount'])) {
            alert("Enter Amount.", "error", "error");
            redirect("?");
        } else {


            $stmt = $mysqli->prepare("INSERT into direct_sale (customer_id,division_id,sale_date,amount) values (?,?,?,?) ;");
            $stmt->bind_param("iisd", $_POST['customer_id'], $_POST['division_id'],  $_POST['sale_date'], $_POST['amount']);
            $stmt->execute();
            $stmt->close();

            alert("sale created successfully.", "success", "success");
            redirect("?");
        }
    }
}


if (isset($_POST['btnDelete'])) {
    $stmt = $mysqli->prepare("DELETE from direct_sale  where id=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $stmt->close();
    alert(' Save successfully', 'success');
    redirect("?");
}

if (isset($_POST['btnSavePayment'])) {
    try {

        $stmt = $mysqli->prepare("INSERT into direct_sale_payment (direct_sale_id,amount,pay_date,pay_mode,ref_id,remarks,user_id) values (?,?,?,?,?,?,?) ;");
        $stmt->bind_param("idssssi", $_POST['id'], $_POST['amount'],  $_POST['pay_date'], $_POST['pay_mode'], $_POST['ref_id'], $_POST['remarks'], $user["id"]);
        $stmt->execute();
        $stmt->close();

        alert("Save successfully.", "success", "success");
        redirect("?limit=" . $_GET["limit"] . "&fromDt=" . $_GET["fromDt"] . "&toDt=" . $_GET["toDt"] . "&division=" . $_GET["division"] . "");
    } catch (\Throwable $th) {
        alert($th->getMessage(), 'error');
        redirect("?limit=" . $_GET["limit"] . "&fromDt=" . $_GET["fromDt"] . "&toDt=" . $_GET["toDt"] . "&division=" . $_GET["division"] . "");
    }
}

$per = permissions(2, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Direct Sale</h4>
        </div>
        <div class="">

            <?php
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Sale</button>
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
                    <input type="date" name="fromDt" class="form-control"  
                    value="<?= isset($_GET['fromDt']) ? htmlspecialchars($_GET['fromDt']) : '' ?>"  required>
                    <input type="date" name="toDt" class="form-control"  
                    value="<?= isset($_GET['toDt']) ? htmlspecialchars($_GET['toDt']) : '' ?>"  required>
                    <select name="division" id="division" class="form-control" required>
                        <option value="">Select</option>
                        <?php

                        $stmt = $mysqli->prepare("SELECT * from  brand  ");

                        $stmt->execute();
                        $company = $stmt->get_result();

                        while ($row = $company->fetch_assoc()) {
                            $sl = "";
                            if ($row['id'] == $_GET['division']) {
                                $sl = "selected";
                            }
                            echo '<option value="' . $row['id'] . '" ' . $sl . ' >' . $row['name'] . '</option>';
                        }
                        ?>

                    </select>
                    


                    <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Company Name</th>
                    <th>Customer Name</th>
                    <th>Division Name</th>
                    <th>Sale Date</th>
                    <th>Sale Amount</th>
                    <th>Received Amount</th>
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

                $totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM direct_sale WHERE division_id = ? and date(sale_date)>=? and date(sale_date)<=?");
                $totalRecordsQuery->bind_param("iss", $_GET['division'], $_POST['fromDt'], $_POST['toDt']); // Bind search parameter
                $totalRecordsQuery->execute();
                $totalRecordsResult = $totalRecordsQuery->get_result();
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

                // Determine the total number of pages
                $totalPages = ceil($totalRecords / $recordsPerPage);

                // Calculate the starting record for the current page
                $startFrom = ($currentPage - 1) * $recordsPerPage;

                $stmt = $mysqli->prepare("SELECT a.*,b.name as name,c.name as division,d.company_name,
                   IFNULL(SUM(e.amount), 0) AS received_amt from  direct_sale a join lead b on a.customer_id=b.id join brand c on a.division_id=c.id join company d on b.company_id=d.id left join direct_sale_payment e on a.id=e.direct_sale_id where a.division_id =? and date(sale_date)>=date(?) and date(sale_date)<=date(?)  group by a.id limit ?,? ");
                $stmt->bind_param("issii", $_GET['division'], $_GET['fromDt'], $_GET['toDt'], $startFrom, $recordsPerPage);
                $stmt->execute();
                $company = $stmt->get_result();
                $no = 1;
                while ($row = $company->fetch_assoc()) {
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['company_name'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['division'] . '</td>
                                        <td>' . $row['sale_date'] . '</td>
                          
                                        <td>' . $row['amount'] . '</td>
                                                      <td>' . $row['received_amt'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>';



                    if ($per['edit'] == 1) {

                        echo  '
                        
                        <td>
                                  <button type="button" class="btn btn-secondary btn-sm paymentReceived"   data-id="' . $row['id'] . '"  > ₹ </button>
          <button type="button" class="btn btn-success btn-sm paymentHistory" id=""  data-id="' . $row['id'] . '"  ><i class="fa fa-history" aria-hidden="true"></i></button>

                        <button type="button" class="btn btn-danger btn-sm delete" id="edit_company"  data-id="' . $row['id'] . '"  ><i class="fa fa-trash"></i></button>
                        
                        
                        </td>
                        
                        
                        
                        
                        ';
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
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Sale</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="uid">
                    <div class="col-md">

                        <label for="validationCustom01" class="form-label"> Division</label>
                        <select name="division_id" id="division_id" class="form-control" required>
                            <option value="">Select</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from  brand  ");

                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>

                        </select>

                        <label for="validationCustom01" class="form-label mt-4"> Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select</option>

                        </select>

                        <label for="validationCustom01" class="form-label mt-4"> Sale Date</label>
                        <input type="date" name="sale_date" id="sale_date" class="form-control" required>

                        <label for="validationCustom01" class="form-label mt-4"> Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
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




<form method="post">
    <div
        class="modal fade"
        id="deleteId"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"

        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Delete
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="d_id" name="id">
                    <h4>Are you sure you want to delete this entry?</h4>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnDelete" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

</form>




<form method="post" class="needs-validation" novalidate>
    <div
        class="modal fade"
        id="receivedPaymentModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"

        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Received Payment
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="receivedPayment_id" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Pay Date</label>
                            <input type="date" name="pay_date" class="form-control" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Pay Mode</label>
                            <select name="pay_mode" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Net Banking">Net Banking</option>
                                <option value="Gpay">Gpay</option>

                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Ref ID</label>
                            <input type="text" name="ref_id" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnSavePayment" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

</form>



<div
    class="modal fade"
    id="paymentHistoryModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"

    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Payment History
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Ref ID</th>
                            <th>User</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="paymentHistoryTable">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>




<?php include("../layouts/footer.php"); ?>

<script>
    $(document).ready(function() {
        $("#exampleModal").on("shown.bs.modal", function() {
            $("#customer_id").select2({
                dropdownParent: $("#exampleModal") // Ensures dropdown stays within modal
            });


            $("#division_id").select2({
                dropdownParent: $("#exampleModal") // Ensures dropdown stays within modal
            });
        });

    });

    $(document).on("click", ".edit_company", function() {
        $("#uid").val($(this).data("id"));
        $("#company_name").val($(this).data("company_name"));
        $("#modal_name").text("Update Sale");


        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Sale");
        $("#company_name").val("");

        $("#exampleModal").modal("show");
    });

    $("#division_id").on("change", function() {
        var id = $(this).val()

        $.ajax({
            method: 'POST',
            url: 'ajax/get-customer-direct-sale.php',
            data: {
                id: id
            },
            success: function(data) {
                data = JSON.parse(data)
                var html = "";
                console.log(data);
                data.forEach(element => {
                    html += `<option value="${element.id}">${element.name} (${element.company_name})</option>`;
                });
                $("#customer_id").html(html);

            }
        });
    });

    $(document).on("click", ".delete", function() {
        $("#d_id").val($(this).data("id"))
        $("#deleteId").modal("show")
    });

    $(document).on("click", ".paymentReceived", function() {
        $("#receivedPayment_id").val($(this).data("id"));
        $("#receivedPaymentModal").modal("show")
    });


    $(document).on("click", ".paymentHistory", function() {

        $.ajax({
            method: 'POST',
            url: 'ajax/get-direct-sale-payment-history.php',
            data: {
                id: $(this).data("id")
            },
            success: function(data) {
                data = JSON.parse(data)
                var html = "";
                var sno = 1;
                console.log(data);
                data.forEach(element => {
                    html += `<tr>
                            <td>${sno++}</td>
                            <td>${element.pay_date}</td>
                            <td>${element.amount}</td>
                            <td>${element.pay_mode}</td>
                            <td>${element.ref_id}</td>
                            <td>${element.user}</td>
                            <td>${element.remarks}</td>
                        </tr>`;
                });
                $("#paymentHistoryTable").html(html)
                $("#paymentHistoryModal").modal("show");

            }
        });

    });
</script>