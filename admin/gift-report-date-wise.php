<?php
include("../layouts/header.php");








?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Gift Report Date Wise</h4>
        </div>
        <form method="GET">
            <div class="d-flex">
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

                        <th>Remarks</th>



                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT a.*,c.company_name as name from lead_gift a join lead b on a.lead_id=b.id join company c on b.company_id=c.id where date(a.date)=date(?) and b.active=1");
                    $stmt->bind_param("s", $_GET['date']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['date'] . '</td>
                                      
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