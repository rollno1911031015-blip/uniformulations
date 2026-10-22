<?php
include("../layouts/header.php");
$status = $_GET['status'];
if (isset($_POST['btnAllocate'])) {
    $stmt = $mysqli->prepare("UPDATE repair_mst set vendor_id=?, status='repairing' where   id=?");
    $stmt->bind_param("ii", $_POST['vendor_id'], $_POST['id']);
    $stmt->execute();
    $stmt->close();
    alert("save successfully", "success");
    redirect("?status=repair pending");
}

if (isset($_POST['BtnRepairComplete'])) {

    $stmt = $mysqli->prepare("UPDATE repair_mst set  status='repair complete' where   id=?");
    $stmt->bind_param("i",  $_POST['RepairComplete_id']);
    $stmt->execute();
    $stmt->close();
    alert("save successfully", "success");
    redirect("?status=repairing");
}
if (isset($_POST['BtnCompleted'])) {
    $stmt = $mysqli->prepare("UPDATE repair_mst set  status='completed' where   id=?");
    $stmt->bind_param("i",  $_POST['Completed_id']);
    $stmt->execute();
    $stmt->close();
    alert("save successfully", "success");
    redirect("?status=repair complete");
}
?>

<div class="card">
    <div class="card-header">
        <div class="page-title">
            <h4>Repair Orders</h4>
        </div>

        <div class="mt-4">
            <a href="?status=repair pending" class="btn <?php if ($status == "repair pending") echo "btn-success";
                                                        else echo "btn-primary" ?>">Pending</a>

            <a href="?status=repairing" class="btn <?php if ($status == "repairing") echo "btn-success";
                                                    else echo "btn-primary" ?>">Repairing</a>
            <a href="?status=repair complete" class="btn <?php if ($status == "repair complete") echo "btn-success";
                                                            else echo "btn-primary" ?>">Repair Complete</a>
            <a href="?status=completed" class="btn <?php if ($status == "completed") echo "btn-success";
                                                    else echo "btn-primary" ?>">Completed</a>

        </div>

    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <th>S.No</th>
                <th>Customer</th>
                <th>Description</th>
                <th>User</th>
                <th>Vendor</th>

                <th>Created at</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php
                $stmt = $mysqli->prepare("SELECT a.*,b.name as customer,c.name as user,v.con_person_name as vendor from   repair_mst a join lead b on a.customer_id=b.id join admin_users c on a.user_id=c.id left join vendor v on a.vendor_id=v.id where a.status=?   ;");
                $stmt->bind_param('s', $_GET['status']);
                $stmt->execute();
                $res = $stmt->get_result();
                $sno = 1;
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            <td>' . $row['customer'] . '</td>
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                            <td>' . $row['vendor'] . '</td>
                                           
                                            <td>' . $row['created_at'] . '</td>
                                            <td><form method="post"> <a   class="btn btn-primary btn-sm" href="repair-order-view?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>';

                    if ($status == "repair pending") {
                        echo '<button class="btn btn-dark mx-2 btn-sm allocate" value="' . $row['id'] . '" type="button">Allocate Vendor</button>';
                    } else if ($status == "repairing") {
                        echo '<button class="btn btn-dark mx-2 btn-sm BtnRepairComplete" value="' . $row['id'] . '" type="button" name="">Repair Complete</button>';
                    } else if ($status == "repair complete") {
                        echo '<button class="btn btn-dark mx-2 btn-sm BtnCompleted" value="' . $row['id'] . '" type="button" name=""> Deliver to client</button>';
                    }

                    echo  '</form></td>
                                         </tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>

<div class="modal fade" id="vendorModal">
    <div class="modal-dialog " role="document">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Allocate vendor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <label for="">Select Vendor</label>
                    <select name="vendor_id" id="vendor_id" class="form-control">
                        <option value="">---Select Vendor---</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT *from vendor    ;");
                        //$stmt->bind_param('s', $_GET['status']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $sno = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['con_person_name'] . '</option>';
                        }
                        ?>

                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnAllocate" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="RepairComplete">
    <div class="modal-dialog  " role="document">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Confirmation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="RepairComplete_id" name="RepairComplete_id">
                    <h4>Are you sure you want to take this action?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="BtnRepairComplete" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="Completed">
    <div class="modal-dialog  " role="document">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Confirmation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="Completed_id" name="Completed_id">
                    <h4>Are you sure you want to take this action?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="BtnCompleted" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>




<?php include("../layouts/footer.php"); ?>

<script>
    $(document).on("click", ".allocate", function() {
        $("#id").val($(this).val())
        $("#vendorModal").modal("show")

    });

    $(document).on("click", ".BtnRepairComplete", function() {
        $("#RepairComplete_id").val($(this).val())
        $("#RepairComplete").modal("show")
    });


    $(document).on("click", ".BtnCompleted", function() {
        $("#Completed_id").val($(this).val())
        $("#Completed").modal("show")
    });
</script>