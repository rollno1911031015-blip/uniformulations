<?php
include("../layouts/header.php");
 

$per = permissions(3, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Follow Up History</h4>
        </div>
        <div class="">
            
        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                       
                      
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from  lead_follow_up where lead_id=?");
                     $stmt->bind_param("i", $_GET['id']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $no++ . '</td>
                             
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


 