<?php
include("../layouts/header.php");
$audit_date = date('Y-m-d');

$stmt = $mysqli->prepare("SELECT * from   audit_settings  where id=1");
//$stmt->bind_param("s", $_POST['audit_count']);
$stmt->execute();
$audit_setting = $stmt->get_result()->fetch_assoc();
$stmt->close();


if ($audit_setting['type'] == "automatic") {
    $stmt = $mysqli->prepare("SELECT * from   audit_report_mst  where audit_date=?");
    $stmt->bind_param("s", $audit_date);
    $stmt->execute();
    $audit_mst = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (empty($audit_mst)) {
        $stmt = $mysqli->prepare("SELECT * from   products  order by id desc limit 1");
        //$stmt->bind_param("s", $_POST['audit_count']);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        rand(1, $products['id']);
        $product_ids = [];

        $audit_count = $audit_setting['audit_count'];
        for ($i = count($product_ids); $i < $audit_count;) {

            $no = rand(1, $products['id']);

            $stmt = $mysqli->prepare("SELECT * from   products  where id=?");
            $stmt->bind_param("i", $no);
            $stmt->execute();
            $prod = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!empty($prod)) {
                if (!in_array($no, $product_ids)) {
                    $product_ids[] = $no;
                    $i++;
                }
            }
        }




        $stmt = $mysqli->prepare("INSERT into audit_report_mst(audit_date) values(?);");
        $stmt->bind_param("s", $audit_date);
        $stmt->execute();
        $mst_id = $stmt->insert_id;
        $stmt->close();


        for ($j = 0; $j < count($product_ids); $j++) {
            $stmt = $mysqli->prepare("INSERT into audit_report(mst_id,product_id,audit_date) values(?,?,?);");
            $stmt->bind_param("iis", $mst_id, $product_ids[$j], $audit_date);
            $stmt->execute();

            $stmt->close();
        }
    }
} else {

    $stmt = $mysqli->prepare("SELECT * from   audit_report_mst  where audit_date=?");
    $stmt->bind_param("s", $audit_date);
    $stmt->execute();
    $audit_mst = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (empty($audit_mst)) {


        $stmt = $mysqli->prepare("INSERT into audit_report_mst(audit_date) values(?);");
        $stmt->bind_param("s", $audit_date);
        $stmt->execute();
        $mst_id = $stmt->insert_id;
        $stmt->close();

        $product_ids = explode(', ', $audit_setting['product_ids']);
        for ($j = 0; $j < count($product_ids); $j++) {
            $stmt = $mysqli->prepare("INSERT into audit_report(mst_id,product_id,audit_date) values(?,?,?);");
            $stmt->bind_param("iis", $mst_id, $product_ids[$j], $audit_date);
            $stmt->execute();

            $stmt->close();
        }
    }
}
?>

<div class="card">
    <div class="card-header ">
        <div class="page-title">
            <h4>Audit Report</h4>
        </div>
        <div class="mt-4">
            <a class="btn <?php if ($_GET['status'] == 'pending') echo 'btn-success ';
                            else echo "btn-primary"; ?>" href="?status=pending">Pending</a>
            <a class="btn <?php if ($_GET['status'] == 'completed') echo 'btn-success ';
                            else echo "btn-primary"; ?>" href="?status=completed">completed</a>


        </div>
    </div>
    <div class="card-body">

        <table class="table">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $mysqli->prepare("SELECT * from   audit_report_mst  where  status=?");
                $stmt->bind_param("s", $_GET['status']);
                $stmt->execute();
                $audit_mst = $stmt->get_result();
                $stmt->close();
                $sno = 1;
                while ($row = $audit_mst->fetch_assoc()) {
                    echo '<tr>
                            <td>' . $sno++ . '</td>
                            <td>' . $row['audit_date'] . '</td>
                            <td>' . $row['status'] . '</td>
                            <td><a class="btn btn-primary btn-sm" href="audit-report-view?id=' . $row['id'] . '"><i class="fa fa-eye" aria-hidden="true"></i></a> </td>
                        </tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>










<?php include("../layouts/footer.php"); ?>