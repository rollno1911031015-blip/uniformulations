<?php
include("../layouts/header.php");








?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Follow Up Report Date Wise</h4>
        </div>
        <form method="GET" class="row">
        <div class="col-2">
                <select name="year" id="year" required class="form-control">
                    <option value="">Select Year</option>
                    <?php 
                    for ($i=2022; $i <=date("Y") ; $i++) { 
                        ?>
                        <option value="<?=$i?>" <?php echo isset($_GET['year']) && $_GET['year'] == $i ? 'selected' : ''; ?>><?=$i?></option>
                        <?php
                    }
                    ?>
                  
                </select>

            </div>
            <div class="col-6">
                <select name="customer_id" id="customer_id" required class="form-control">
                    <option value="">Select Customer</option>
                    <?php

                    $stmt = $mysqli->prepare("SELECT a.*,b.company_name as name,a.id as customer_id from lead a join company b on a.company_id=b.id");
                   // $stmt->bind_param("s", $_GET['date']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo "<option value='".$row['id']."' ".($row['id'] == $_GET['customer_id'] ? "selected" : "").">".$row['name']."</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="col-3">
            <button class="btn btn-primary float-end" type="submit">Search</button>
            </div>
        </form>
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

                    $stmt = $mysqli->prepare("SELECT a.*,c.company_name as name from lead_follow_up a join lead b on a.lead_id=b.id join company c on b.company_id=c.id where a.lead_id=? and b.active=1 and year(a.date)=?");
                    $stmt->bind_param("is", $_GET['customer_id'],$_GET['year']);
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
<script>
    $("#customer_id").select2();
</script>