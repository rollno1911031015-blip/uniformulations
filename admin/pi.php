<?php
include("../layouts/header.php");

if (isset($_POST['btnSave'])) {
    $order_det = $mysqli->prepare("UPDATE  order_mst set order_status='Order'  where id=?");
    $order_det->bind_param("i",   $_POST['id']);
    $order_det->execute();
    $order_det->close();
    alert("save successfully", "success");
    redirect("?status=processing");
}
$per = permissions(9, $mysqli, $user);
?>

<div class="card">
    <div class="card-header ">
        <div class="page-title">
            <h4> Quote</h4>
        </div>
        <form method="GET">
            <div class="row mt-4">
                <div class="col-md-2">
                    <input type="hidden" name="status" value="<?= $_GET['status'] ?>">
                    <input type="date" name="fromDt" class="form-control" id="fromDt" required>
                </div>
                <div class="col-md-2">
                    <input type="date" name="toDt" class="form-control" id="toDt" required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>

            </div>
        </form>

    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <th>S.No</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Description</th>
                <th>User</th>
                <th>Order Date</th>

                <th>Created at</th>
                <th>Action</th>

            </thead>
            <tbody>
                <?php
                if (empty($_GET['fromDt'])  && empty($_GET['toDt'])) {



                    $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer, c.name AS user
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN admin_users c ON a.user_id = c.id
                WHERE a.status = ? AND a.user_type = 'staff' and a.order_status='PI' group by a.created_at desc

                UNION

                SELECT a.*, b.name AS customer, c.name AS user
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN lead c ON a.user_id = c.id
                WHERE a.status = ? AND a.user_type = 'customer' and a.order_status='PI' group by a.created_at desc;");


                    $stmt->bind_param('ss', $_GET['status'], $_GET['status']);
                } else {
                    $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer, c.name AS user
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN admin_users c ON a.user_id = c.id
                WHERE a.status = ? AND a.user_type = 'staff' and date(a.order_date)>=? and date(a.order_date)<=? and a.order_status='PI'

                UNION

                SELECT a.*, b.name AS customer, c.name AS user
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN lead c ON a.user_id = c.id
                WHERE a.status = ? AND a.user_type = 'customer' and date(a.order_date)>=? and date(a.order_date)<=? and a.order_status='PI';");


                    $stmt->bind_param('ssssss',  $_GET['status'], $_GET['fromDt'], $_GET['toDt'], $_GET['status'], $_GET['fromDt'], $_GET['toDt']);
                }
                $stmt->execute();
                $res = $stmt->get_result();
                $sno = 1;
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                                            <td>' . $sno++ . '</td>
                                             <td>' . $row['vehicle_no'] . '</td>
                                            <td>' . $row['customer'] . '</td>
                                           
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                           
                                            <td>' . $row['order_date'] . '</td>
                                            <td>' . $row['created_at'] . '</td>
                                            <td><form method="post"> <a   class="btn btn-primary btn-sm" href="order-view?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>';
                    if ($per['edit'] == 1) {





                        echo '<button class="btn btn-dark btn-sm mx-1 Convert" type="button" data-id="' . $row['id'] . '">Convert to Order</button>';





                        echo  '</form></td>';
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>




<form method="POST">
    <div class="modal fade" id="modalId">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Convert to Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <h4>You are going to convert this Quote to order?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnSave" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>




<?php include("../layouts/footer.php"); ?>


<script>
    $(document).on("click", ".Convert", function() {
        $("#id").val($(this).data("id"))
        $("#modalId").modal("show");
    });
</script>