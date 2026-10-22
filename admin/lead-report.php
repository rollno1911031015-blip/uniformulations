<?php
include("../layouts/header.php");



$stmt = $mysqli->prepare("SELECT  * from admin_users where designation=?");
$stmt->bind_param("i", $_GET['designation']);
$stmt->execute();
$admin_users = $stmt->get_result();
while ($row = $admin_users->fetch_assoc()) {
    $stmt1 = $mysqli->prepare("SELECT  * from status");
    //$stmt->bind_param("isi", $_GET['id'], $customer_type, $user['id']);
    $stmt1->execute();
    $lead_status = $stmt1->get_result();
    while ($row1 = $lead_status->fetch_assoc()) {
        $stmt = $mysqli->prepare("SELECT count(*) as total_leads,
        sum(CASE WHEN a.status='" . $row1['id'] . "' THEN 1 ELSE 0 END) as '" . $row1['status'] . "'
        FROM `lead` a  where a.user_id=? and date(created_at)>=? and date(created_at)<=? ");
        $stmt->bind_param("iss", $row['id'],$_GET['fromDt'],$_GET['toDt']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
       
            $data[$row['name']][] = array("status" => $row1['name'], "value" => $res[$row1['status']], "status_id" => $row1['id'], "user_name" => $row['name']);
         
    }
}







?>

<div class="card">
    <div class="card-header">
        <div class="page-title mb-4">
            <h4>Lead Report</h4>
        </div>
        <form method="GET" class="row">
            <div class="col-4">
                <select name="designation" id="designation" required class="form-control">
                    <option value="">Select Designation</option>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from designation");
                    // $stmt->bind_param("s", $_GET['date']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "' " . ($row['id'] == $_GET['designation'] ? "selected" : "") . ">" . $row['name'] . "</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="col-4 d-flex">
                <input type="date" class="form-control" name="fromDt" value="<?=$_GET['fromDt']?>" required>
               
                <input type="date" class="form-control" name="toDt" value="<?=$_GET['toDt']?>" required>
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