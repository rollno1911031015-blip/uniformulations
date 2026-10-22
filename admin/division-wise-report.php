<?php
include("../layouts/header.php");


if (!empty($_GET['division_id'])) {
    $arr = [];

    $stmt = $mysqli->prepare("SELECT * from lead where find_in_set(?, division)");
    $stmt->bind_param("s", $_GET['division_id']);
    $stmt->execute();
    $lead = $stmt->get_result();

    while ($row = $lead->fetch_assoc()) {
        $stmt = $mysqli->prepare("SELECT d.company_name as name,sum(b.qty*b.price) yearly,
SUM(CASE WHEN MONTH(a.order_date) = 1 THEN b.qty * b.price ELSE 0 END) AS jan_sale,
SUM(CASE WHEN MONTH(a.order_date) = 2 THEN b.qty * b.price ELSE 0 END) AS feb_sale,
SUM(CASE WHEN MONTH(a.order_date) = 3 THEN b.qty * b.price ELSE 0 END) AS mar_sale,
SUM(CASE WHEN MONTH(a.order_date) = 4 THEN b.qty * b.price ELSE 0 END) AS apr_sale,
SUM(CASE WHEN MONTH(a.order_date) = 5 THEN b.qty * b.price ELSE 0 END) AS may_sale,
SUM(CASE WHEN MONTH(a.order_date) = 6 THEN b.qty * b.price ELSE 0 END) AS jun_sale,
SUM(CASE WHEN MONTH(a.order_date) = 7 THEN b.qty * b.price ELSE 0 END) AS jul_sale,
SUM(CASE WHEN MONTH(a.order_date) = 8 THEN b.qty * b.price ELSE 0 END) AS aug_sale,
SUM(CASE WHEN MONTH(a.order_date) = 9 THEN b.qty * b.price ELSE 0 END) AS sep_sale,
SUM(CASE WHEN MONTH(a.order_date) = 10 THEN b.qty * b.price ELSE 0 END) AS oct_sale,
SUM(CASE WHEN MONTH(a.order_date) = 11 THEN b.qty * b.price ELSE 0 END) AS nov_sale,
SUM(CASE WHEN MONTH(a.order_date) = 12 THEN b.qty * b.price ELSE 0 END) AS dec_sale,
SUM(CASE WHEN MONTH(a.order_date) >= 1 AND MONTH(a.order_date) <= 6 THEN b.qty * b.price ELSE 0 END) AS first_half_year_sale,
SUM(CASE WHEN MONTH(a.order_date) >= 7 AND MONTH(a.order_date) <= 12 THEN b.qty * b.price ELSE 0 END) AS second_half_year_sale,
SUM(CASE WHEN MONTH(a.order_date) >= 1 AND MONTH(a.order_date) <= 3 THEN b.qty * b.price ELSE 0 END) AS first_quarter,
SUM(CASE WHEN MONTH(a.order_date) >= 4 AND MONTH(a.order_date) <= 6 THEN b.qty * b.price ELSE 0 END) AS second_quarter,
SUM(CASE WHEN MONTH(a.order_date) >= 7 AND MONTH(a.order_date) <= 9 THEN b.qty * b.price ELSE 0 END) AS third_quarter,
SUM(CASE WHEN MONTH(a.order_date) >= 10 AND MONTH(a.order_date) <= 12 THEN b.qty * b.price ELSE 0 END) AS fourth_quarter

from order_mst a join order_det b on a.id=b.mst_id join lead c on a.customer_id=c.id join company d on c.company_id=d.id where a.customer_id=? and c.active=1 and year(a.order_date)=(?) group by a.customer_id");
        $stmt->bind_param("is", $row['id'], $_GET['year']);
        $stmt->execute();
        $order_mst = $stmt->get_result()->fetch_assoc();
        if ($order_mst) {
            $arr[] = $order_mst;
        }
    }
}





?>

<div class="card">
    <div class="card-header">
        <div class="page-title mb-4">
            <h4>Division Wise Report</h4>
        </div>
        <form method="GET" class="row">
            <div class="col-4">
                <select name="division_id" id="division_id" required class="form-control">
                    <option value="">Select Division</option>
                    <?php

                    $stmt = $mysqli->prepare("SELECT * from brand");
                    // $stmt->bind_param("s", $_GET['date']);
                    $stmt->execute();
                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "' " . ($row['id'] == $_GET['division_id'] ? "selected" : "") . ">" . $row['name'] . "</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="col-4">
                <select name="year" id="year" required class="form-control">
                    <option value="">Select Year</option>
                    <?php

                    for ($i = 2020; $i <= date("Y"); $i++) {
                        echo "<option value='" . $i . "' " . ($i == $_GET['year'] ? "selected" : "") . ">" . $i . "</option>";
                    }


                    ?>
                </select>

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
                        <th>S.no</th>
                        <th>Customer Name</th>

                        <th>January</th>
                        <th>February</th>
                        <th>March</th>
                        <th>April</th>
                        <th>May</th>
                        <th>June</th>
                        <th>July</th>
                        <th>August</th>
                        <th>September</th>
                        <th>October</th>
                        <th>November</th>
                        <th>December</th>
                        <th>Yearly Sale</th>
                        
                        <th>First half year</th>
                        <th>Second half year</th>
                        <th>First quarter</th>
                        <th>Second quarter</th>
                        <th>Third quarter</th>
                        <th>Fourth quarter</th>


                    </tr>
                </thead>
                <tbody>
                    <?php


                    $sno = 1;

                    foreach ($arr as $key => $value) {
                        echo '<tr>
                        <td>' . $sno++ . '</td>
                        <td>' . ($value['name'] ?? '-') . '</td>
                
                
                        <td>' . ($value['jan_sale'] ?? 0) . '</td>
                        <td>' . ($value['feb_sale'] ?? 0) . '</td>
                        <td>' . ($value['mar_sale'] ?? 0) . '</td>
                        <td>' . ($value['apr_sale'] ?? 0) . '</td>
                        <td>' . ($value['may_sale'] ?? 0) . '</td>
                        <td>' . ($value['jun_sale'] ?? 0) . '</td>
                        <td>' . ($value['jul_sale'] ?? 0) . '</td>
                        <td>' . ($value['aug_sale'] ?? 0) . '</td>
                        <td>' . ($value['sep_sale'] ?? 0) . '</td>
                        <td>' . ($value['oct_sale'] ?? 0) . '</td>
                        <td>' . ($value['nov_sale'] ?? 0) . '</td>
                        <td>' . ($value['dec_sale'] ?? 0) . '</td>
                            <td>' . ($value['yearly'] ?? 0) . '</td>
                                    <td>' . ($value['first_half_year_sale'] / 2 ?? 0) . '</td>
                        <td>' . ($value['second_half_year_sale'] / 2 ?? 0) . '</td>
                        <td>' . ($value['first_quarter'] / 3 ?? 0) . '</td>
                        <td>' . ($value['second_quarter'] / 3 ?? 0) . '</td>
                        <td>' . ($value['third_quarter'] / 3 ?? 0) . '</td>
                        <td>' . ($value['fourth_quarter'] / 3 ?? 0) . '</td>
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
    $("#division_id").select2();
</script>