<?php
include("../layouts/header.php");


if (!empty($_GET['division_id'])) {
   
        $stmt = $mysqli->prepare("SELECT b.state  as name,sum(a.amount) yearly,
SUM(CASE WHEN MONTH(a.sale_date) = 1 THEN a.amount ELSE 0 END) AS jan_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 2 THEN a.amount ELSE 0 END) AS feb_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 3 THEN a.amount ELSE 0 END) AS mar_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 4 THEN a.amount ELSE 0 END) AS apr_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 5 THEN a.amount ELSE 0 END) AS may_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 6 THEN a.amount ELSE 0 END) AS jun_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 7 THEN a.amount ELSE 0 END) AS jul_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 8 THEN a.amount ELSE 0 END) AS aug_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 9 THEN a.amount ELSE 0 END) AS sep_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 10 THEN a.amount ELSE 0 END) AS oct_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 11 THEN a.amount ELSE 0 END) AS nov_sale,
SUM(CASE WHEN MONTH(a.sale_date) = 12 THEN a.amount ELSE 0 END) AS dec_sale,
SUM(CASE WHEN MONTH(a.sale_date) >= 1 AND MONTH(a.sale_date) <= 6 THEN a.amount ELSE 0 END) AS first_half_year_sale,
SUM(CASE WHEN MONTH(a.sale_date) >= 7 AND MONTH(a.sale_date) <= 12 THEN a.amount ELSE 0 END) AS second_half_year_sale,
SUM(CASE WHEN MONTH(a.sale_date) >= 1 AND MONTH(a.sale_date) <= 3 THEN a.amount ELSE 0 END) AS first_quarter,
SUM(CASE WHEN MONTH(a.sale_date) >= 4 AND MONTH(a.sale_date) <= 6 THEN a.amount ELSE 0 END) AS second_quarter,
SUM(CASE WHEN MONTH(a.sale_date) >= 7 AND MONTH(a.sale_date) <= 9 THEN a.amount ELSE 0 END) AS third_quarter,
SUM(CASE WHEN MONTH(a.sale_date) >= 10 AND MONTH(a.sale_date) <= 12 THEN a.amount ELSE 0 END) AS fourth_quarter

from direct_sale a join lead b on a.customer_id=b.id join company c on b.company_id=c.id where a.division_id=? and b.active=1 and year(a.sale_date)=(?)   group by b.state ");
        $stmt->bind_param("ii", $_GET['division_id'], $_GET['year'] );
        $stmt->execute();
        $order_mst = $stmt->get_result();
        
    }





?>

<div class="card">
    <div class="card-header">
        <div class="page-title mb-4">
            <h4>State wise report</h4>
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
                        <th>State</th>


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
                    $jan = 0;
                    $feb = 0;
                    $march = 0;
                    $april = 0;
                    $may = 0;
                    $june = 0;
                    $july = 0;
                    $august = 0;
                    $sep = 0;
                    $oct = 0;
                    $nov = 0;
                    $dec = 0;

                    $sno = 1;

                    while ($value=$order_mst->fetch_assoc()) {
                        $jan += ($value['jan_sale'] ?? 0);
                        $feb += ($value['feb_sale'] ?? 0);
                        $march += ($value['mar_sale'] ?? 0);
                        $april += ($value['apr_sale'] ?? 0);
                        $may += ($value['may_sale'] ?? 0);
                        $june += ($value['jun_sale'] ?? 0);
                        $july += ($value['jul_sale'] ?? 0);
                        $august += ($value['aug_sale'] ?? 0);
                        $sep += ($value['sep_sale'] ?? 0);
                        $oct += ($value['oct_sale'] ?? 0);
                        $nov += ($value['nov_sale'] ?? 0);
                        $dec += ($value['dec_sale'] ?? 0);
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
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th><?= $jan ?></th>
                        <th><?= $feb ?></th>
                        <th><?= $march ?></th>
                        <th><?= $april ?></th>
                        <th><?= $may ?></th>
                        <th><?= $june ?></th>
                        <th><?= $july ?></th>
                        <th><?= $august ?></th>
                        <th><?= $sep ?></th>
                        <th><?= $oct ?></th>
                        <th><?= $nov ?></th>
                        <th><?= $dec ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

</div>



<?php include("../layouts/footer.php"); ?>
<script>
    $("#division_id").select2();
</script>