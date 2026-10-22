<?php
include("../layouts/header.php");
if (isset($_POST['BtnApproved'])) {

    $order_det = $mysqli->prepare("UPDATE  order_mst set status='processing'  where id=?");
    $order_det->bind_param("i",   $_POST['BtnApproved']);
    $order_det->execute();
    $order_det->close();
    alert("save successfully", "success");
    redirect("?status=pending for approval");
}
if (isset($_POST['btnCancel'])) {
    $order_det = $mysqli->prepare("UPDATE  order_mst set status='cancelled'  where id=?");
    $order_det->bind_param("i",   $_POST['id']);
    $order_det->execute();
    $order_det->close();
    alert("save successfully", "success");
    redirect("?status=pending");
}
$per = permissions(9, $mysqli, $user);
?>

<div class="card">
    <div class="card-header ">
        <div class="page-title">
            <h4> <?= $_GET['status'] ?> orders</h4>
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



                    $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer, c.name AS user,d.company_name
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN admin_users c ON a.user_id = c.id
                JOIN company d ON b.company_id = d.id
                WHERE a.status = ? AND a.user_type = 'staff' and a.order_status='Order' group by a.created_at desc

                UNION

                SELECT a.*, b.name AS customer, c.name AS user,d.company_name
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN lead c ON a.user_id = c.id
                  JOIN company d ON b.company_id = d.id
                WHERE a.status = ? AND a.user_type = 'customer' and a.order_status='Order' group by a.created_at desc;");


                    $stmt->bind_param('ss', $_GET['status'], $_GET['status']);
                } else {
                    $stmt = $mysqli->prepare("SELECT a.*, b.name AS customer, c.name AS user,d.company_name
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN admin_users c ON a.user_id = c.id
                  JOIN company d ON b.company_id = d.id
                WHERE a.status = ? AND a.user_type = 'staff' and date(a.order_date)>=? and date(a.order_date)<=? and a.order_status='Order'

                UNION

                SELECT a.*, b.name AS customer, c.name AS user,d.company_name
                FROM order_mst a
                JOIN lead b ON a.customer_id = b.id
                JOIN lead c ON a.user_id = c.id
                  JOIN company d ON b.company_id = d.id
                WHERE a.status = ? AND a.user_type = 'customer' and date(a.order_date)>=? and date(a.order_date)<=? and a.order_status='Order';");


                    $stmt->bind_param('ssssss',  $_GET['status'], $_GET['fromDt'], $_GET['toDt'], $_GET['status'], $_GET['fromDt'], $_GET['toDt']);
                }
                $stmt->execute();
                $res = $stmt->get_result();
                $sno = 1;
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                                            <td>' . $sno++ . '</td>
                                             <td>' . $row['vehicle_no'] . '</td>
                                            <td>' . $row['company_name'] . '</td>
                                           
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                           
                                            <td>' . $row['order_date'] . '</td>
                                            <td>' . $row['created_at'] . '</td>
                                            <td><form method="post"> <a   class="btn btn-primary btn-sm" href="order-view?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>';
                    if ($per['edit'] == 1) {


                        if ($row['status'] == "processing" || $row['status'] == "pending") {


                            echo '<a class="btn btn-dark btn-sm" href="outward-stock?customer_id=' . $row['customer_id'] . '&order_id=' . $row['id'] . '">Outward</a>
                        <button class="btn btn-danger btn-sm cancel" data-id="' . $row['id'] . '" type="button">Cancel</button>
                        
                        ';
                        }

                        if ($row['status'] == "pending for approval") {


                            // echo '<button class="btn btn-success btn-sm mx-2" type="submit" value="' . $row['id'] . '" name="BtnApproved">Approved</button>';
                            echo '<a class="btn btn-success btn-sm mx-2"  href="generate-customer-order?order_id=' . $row['id'] . '&customer_id='.$row['customer_id'].'">Approved</a>';
                        }


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
                        Cancel Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <h4>Are you sure you want to cancel this order?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnCancel" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>




<?php include("../layouts/footer.php"); ?>


<script>
    $(document).on("click", ".cancel", function() {
        $("#id").val($(this).data("id"))
        $("#modalId").modal("show");
    });
</script>