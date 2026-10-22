<?php
include("../layouts/header.php");

$_GET['start_date'] ?? date("Y-m-d");
$_GET['end_date'] ?? date("Y-m-d");

$stmt = $mysqli->prepare("SELECT  * from source");
//$stmt->bind_param("isi", $_GET['id'], $customer_type, $user['id']);
$stmt->execute();
$source = $stmt->get_result();
while ($row = $source->fetch_assoc()) {
    $stmt1 = $mysqli->prepare("SELECT  * from status");
    //$stmt->bind_param("isi", $_GET['id'], $customer_type, $user['id']);
    $stmt1->execute();
    $lead_status = $stmt1->get_result();
    while ($row1 = $lead_status->fetch_assoc()) {
        $stmt = $mysqli->prepare("SELECT count(*) as total_leads,
        sum(CASE WHEN a.status='" . $row1['id'] . "' THEN 1 ELSE 0 END) as '" . $row1['status'] . "'
        FROM `lead` a  where a.source=? and date(a.created_at)>=?  and date(a.created_at)<=? ");
        $stmt->bind_param("sss", $row['name'],$_GET['start_date'],$_GET['end_date']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
       
       
            $data[$row['name']][] = array("status" => $row1['name'], "value" => $res[$row1['status']], "status_id" => $row1['id']);
         
    }
}
 





?>

<div class="card">
    <div class="card-header">
        <div class="page-title mb-4">
            <h4>Source Report</h4>
        </div>
        <form method="GET" class="row">
            <div class="col-4">
                <input type="date" name="start_date" id="" value="<?=$_GET['start_date'] ?? ""?>" required class="form-control">
                 

            </div>
            <div class="col-4">
            <input type="date" name="end_date" id="" value="<?=$_GET['end_date'] ?? ""?>" required class="form-control">

            </div>
            <div class="col-1">
                <button class="btn btn-primary float-end" type="submit">Search</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table mydataTableReport">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <?php
                        $stmt1 = $mysqli->prepare("SELECT  * from status");
                        //$stmt->bind_param("isi", $_GET['id'], $customer_type, $user['id']);
                        $stmt1->execute();
                        $lead_status = $stmt1->get_result();
                        while ($row = $lead_status->fetch_assoc()) {
                            echo '<th>' . $row['name'] . '</th>';
                        }
                        ?>


                    </tr>
                </thead>
                <tbody>

                    <?php
                    $sno = 1;
                    foreach ($data as $key => $value) {
                        echo '<tr>
                                <td>' . $sno++ . '</td>
                                <td>' . $key . '</td>';

                        foreach ($value as $k => $v) {
                            echo '<td>' . $v['value'] . '</td>';
                        }
                        echo  '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</div>



<?php include("../layouts/footer.php"); ?>
<script>
    $("#division_id").select2();
</script>