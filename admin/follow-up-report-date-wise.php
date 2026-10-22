<?php
include("../layouts/header.php");








?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Follow Up Report Date Wise</h4>
        </div>
        <form method="GET">
            <div class="d-flex">
            <select name="division" id="division" class="from-control">
                        <option value="">Select</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from `brand` ");
                        // $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                        $company = $stmt->get_result();

                        while ($row = $company->fetch_assoc()) {
                            $sl = "";
                            if ($_GET['division'] == $row['id']) {
                                $sl = "selected";
                            }

                            echo '<option value="' . $row['id'] . '" ' . $sl . '>' . $row['name'] . '</option>';
                        }

                        ?>
                    </select>
                <input type="date" class="form-control" name="date" required value="<?= $_GET['date'] ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Remarks</th>



                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT a.*,c.company_name as name from lead_follow_up a join lead b on a.lead_id=b.id join company c on b.company_id=c.id where date(a.date)=date(?) and b.active=1 and  FIND_IN_SET(?, b.division) ");
                    $stmt->bind_param("si", $_GET['date'],$_GET['division']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['date'] . '</td>
                                        <td>' . $row['amount'] . '</td>
                                        <td>' . $row['remarks'] . '</td>
                                         
                                    </tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </div>

</div>



<?php include("../layouts/footer.php"); ?>