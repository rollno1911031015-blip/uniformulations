<?php
include("../layouts/header.php");
if (isset($_POST['btnSave'])) {


    $stmt = $mysqli->prepare("SELECT * from   current_stock  where prod_id=?");
    $stmt->bind_param("i", $_POST['product_id']);
    $stmt->execute();
    $current_stock = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $stmt = $mysqli->prepare("UPDATE   audit_report set current_stock=?,physical_stock=?,user_id=?,is_audit=1 where id=?");
    $stmt->bind_param("iiii", $current_stock['current_stock'], $_POST['physical_stock'], $user['id'], $_POST['id']);
    $stmt->execute();
    $stmt->close();



    $check = 0;
    $stmt = $mysqli->prepare("SELECT * from   audit_report  where mst_id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $audit_report = $stmt->get_result();
    $stmt->close();
    while ($row = $audit_report->fetch_assoc()) {
        if ($row['is_audit'] == 0) {
            $check = 1;
        }
    }

    if ($check == 0) {
        $stmt = $mysqli->prepare("UPDATE    audit_report_mst set status='completed' where id=?");
        $stmt->bind_param("i",  $_GET['id']);
        $stmt->execute();
        $stmt->close();
    }


    alert('Save successfully', 'success');


    redirect("?id=" . $_GET['id'] . "");
}
$per = permissions(11, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Audit Report</h4>
        </div>
    </div>
    <div class="card-body">

        <table class="table">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Physical Stock</th>

                    <th>User</th>
                    <?php
                    if ($per['edit'] == 1) {
                        echo ' <th>Action</th>';
                    }
                    ?>

                </tr>
            </thead>
            <tbody>
                <?php



                $stmt = $mysqli->prepare("SELECT 
    a.*, 
    b.name AS product_name, 
    c.current_stock AS current_stock, 
    d.name AS user 
FROM 
    audit_report a 
    JOIN products b ON a.product_id = b.id 
    JOIN current_stock c ON a.product_id = c.prod_id 
    LEFT JOIN admin_users d ON a.user_id = d.id 
WHERE 
    a.mst_id = ? AND is_audit = 0

UNION ALL

SELECT 
    a.*, 
    b.name AS product_name, 
    a.current_stock AS current_stock, 
    d.name AS user 
FROM 
    audit_report a 
    JOIN products b ON a.product_id = b.id 
    LEFT JOIN admin_users d ON a.user_id = d.id 
WHERE 
    a.mst_id = ? AND is_audit = 1;");
                $stmt->bind_param("ii", $_GET['id'], $_GET['id']);
                $stmt->execute();
                $audit_report = $stmt->get_result();
                $stmt->close();
                $sno = 1;
                while ($row = $audit_report->fetch_assoc()) {
                    echo '<tr>
                            <td>' . $sno++ . '</td>
                            <td>' . $row['audit_date'] . '</td>
                            <td>' . $row['product_name'] . '</td>
                            <td>' . $row['current_stock'] . '</td>
                            <td>' . $row['physical_stock'] . '</td>
                          
                            <td>' . $row['user'] . '</td>
                          ';
                          if($per['edit']==1){

                          
                    if ($row['is_audit'] == 0) {
                        echo  '  <td><button type="button" class="btn btn-primary btn-sm Edit"  data-id="' . $row['id'] . '"  data-physical_stock="' . $row['physical_stock'] . '" 
                                data-product_id="' . $row['product_id'] . '"
                                > <i class="fa fa-pencil" aria-hidden="true"></i></button> </td>';
                    } else {
                        echo '<td><h4><span class="badge badge-success">Audited</span> </h4></td>';
                    }
                }



                    echo '</tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>










<?php include("../layouts/footer.php"); ?>


<div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Edit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="product1_id" name="product_id">
                <div class="modal-body">
                    <label for="">Physical Stock</label>
                    <input type="number" class="form-control" id="physical_stock" name="physical_stock">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnSave" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>




<script>
    $(document).on("click", ".Edit", function() {
        $("#physical_stock").val($(this).data("physical_stock"));
        $("#id").val($(this).data("id"));
        $("#product1_id").val($(this).data("product_id"));
        $("#modalId").modal("show");
    })
</script>