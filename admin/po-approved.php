<?php
include("../layouts/header.php");
$partial1 = "";
$stmt = $mysqli->prepare("SELECT * from   po_mst");
// $stmt->bind_param('i', $_GET['']);
$stmt->execute();
$po_mst = $stmt->get_result();
while ($row = $po_mst->fetch_assoc()) {
    $stmt = $mysqli->prepare("SELECT * from   po_det where po_id=?");
    $stmt->bind_param('i', $row['id']);
    $stmt->execute();
    $po_det = $stmt->get_result();
    $count = 0;
    $is_received = 0;
    while ($row1 = $po_det->fetch_assoc()) {
        $count++;
        if ($row1['is_received'] == 1) {
            $is_received++;
        }
    }


    if ($_GET['status'] == "Full") {
        if ($count == $is_received) {
            $partial[] = $row['id'];
            $partial1 = implode(",", $partial);
        }
    } else {
        if ($count != $is_received) {
            $partial[] = $row['id'];
            $partial1 = implode(",", $partial);
        }
    }
}
?>



<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>PO</h4>
        </div>

    </div>

    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <th>S.No</th>
                <th>Vendor</th>
                <th>Description</th>
                <th>User</th>
                <th>Location</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php
                if (!empty($res)) {


                    $sno = 1;
                    while ($row = $res->fetch_assoc()) {
                        echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            <td>' . $row['vendor'] . '</td>
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                            <td>' . $row['location'] . '</td>
                                            <td>' . $row['status'] . '</td>
                                            <td><a href="account-generate-po-view?id=' . $row['id'] . '" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                                         </tr>';
                    }
                }
                ?>
            </tbody>

        </table>

    </div>

</div>






<?php include("../layouts/footer.php"); ?>

 