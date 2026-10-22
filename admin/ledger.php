<?php
include("../layouts/header.php");

$per = permissions(2, $mysqli, $user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Ledger</h4>
        </div>

    </div>
    <form method="GET">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap mb-4  pb-0">

            <div class="form-sort me-2 mb-3" >
                <select name="limit" id="limit" onchange="this.form.submit()" class="form-select">
                    <option value="20" <?php echo isset($_GET['limit']) && $_GET['limit'] == 20 ? 'selected' : ''; ?>>20</option>
                    <option value="50" <?php echo isset($_GET['limit']) && $_GET['limit'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo isset($_GET['limit']) && $_GET['limit'] == 100 ? 'selected' : ''; ?>>100</option>
                    <option value="200" <?php echo isset($_GET['limit']) && $_GET['limit'] == 200 ? 'selected' : ''; ?>>200</option>
                    <option value="300" <?php echo isset($_GET['limit']) && $_GET['limit'] == 300 ? 'selected' : ''; ?>>300</option>
                    <option value="500" <?php echo isset($_GET['limit']) && $_GET['limit'] == 500 ? 'selected' : ''; ?>>500</option>
                </select>
            </div>
            <div class="d-flex align-items-center mb-3">
                <div class="input-icon-start me-2 position-relative">
                    <select name="customer" id="customer" class="form-control" required>
                        <option value="">--Select Customer/Employee--</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from  lead  where user_type='customer'");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            $sl = "";
                            if ($row['id'] == $_GET['customer']) {
                                $sl = "selected";
                            }
                            echo '<option value="' . $row['id'] . '" ' . $sl . ' >' . $row['name'] . '</option>';
                        }
                        ?>
                    </select>
                    
                </div>
                <div class="search-set">
                    <div class="page-btn">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="card-body">
        <?php
        $balance =0;
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Customer Name</th>
                    <th>Date</th>
                    <th>Debit Amount</th>
                    <th>Credit Amount</th>
                    <th>Amount</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $search = '';
                if (!empty($_GET['search'])) {
                    $search = '%' . $mysqli->real_escape_string($_GET['search']) . '%';
                } else {
                    $search = "%";
                }

                if (empty($_GET['limit'])) {
                    $_GET['limit'] = 50;
                }
                $recordsPerPage = $_GET['limit'];

                // Determine the current page
                if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                    $currentPage = intval($_GET['page']);
                } else {
                    $currentPage = 1;
                }

                $totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM 
                (SELECT ds.sale_date AS txn_date FROM direct_sale ds WHERE ds.customer_id = ?
                UNION ALL
                SELECT dp.pay_date AS txn_date FROM direct_sale_payment dp 
                JOIN direct_sale ds ON dp.direct_sale_id = ds.id WHERE ds.customer_id = ? ) AS ledger_count");

                $totalRecordsQuery->bind_param("ii",  $_GET['customer'], $_GET['customer']); 
                $totalRecordsQuery->execute();
                $totalRecordsResult = $totalRecordsQuery->get_result();
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

                // Determine the total number of pages
                $totalPages = ceil($totalRecords / $recordsPerPage);

                // Calculate the starting record for the current page
                $startFrom = ($currentPage - 1) * $recordsPerPage;



                $sql = "SELECT * FROM (SELECT ds.sale_date AS txn_date,l.name AS customer_name,ds.amount AS debit,0 AS credit,'Sale' AS type FROM direct_sale ds JOIN lead l ON ds.customer_id = l.id WHERE ds.customer_id = ? 
            UNION ALL
            SELECT dp.pay_date AS txn_date, l.name AS customer_name, 0 AS debit, dp.amount AS credit, 'Payment' AS type FROM direct_sale_payment dp
                JOIN direct_sale ds ON dp.direct_sale_id = ds.id
                JOIN lead l ON ds.customer_id = l.id
                WHERE ds.customer_id = ?
            ) ledger ORDER BY txn_date ASC LIMIT ?, ?;";

                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("iiii", $_GET['customer'], $_GET['customer'], $startFrom, $recordsPerPage);


                $stmt->execute();
                $company = $stmt->get_result();

                $no = 1;
                
                while ($row = $company->fetch_assoc()) {
                    $sign='';
                     if ( $row['debit'] !='0') {
                                $balance += $row['debit'];
                            } elseif ($row['credit'] !='0') {
                                $sign = '-';
                                $balance -= $row['credit'];
                            }
                    echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td>' . $row['customer_name'] . '</td>
                                        <td>' . date('d-M-Y', strtotime($row['txn_date'])) . '</td>   
                                        <td>' . $row['debit'] . '</td>
                                        <td> ' .$sign. $row['credit'] . '</td>
                                        <td>' . number_format($balance, 2, '.', '') . '</td>';





                    echo  '</tr>';
                }
                ?>

            </tbody>
        </table>
        <nav aria-label="...">
            <ul class="pagination float-end">
                <?php

                $range = 2; // Number of pages before and after the current page to show

                if ($totalPages > 1) {
                    // First page link if needed
                    if ($currentPage - $range > 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit']  . '" class="badge bg-warning p-2 m-2">1</a> ';
                        echo '... ';
                    } elseif ($currentPage - $range == 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit'] . '" class="badge bg-warning p-2 m-2">1</a> ';
                    }

                    // Page number links around current page
                    for ($page = max(1, $currentPage - $range); $page <= min($totalPages, $currentPage + $range); $page++) {
                        if ($page == $currentPage) {
                            echo '<strong class="badge bg-success p-2 m-2">' . $page . '</strong> ';
                        } else {
                            echo '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '" class="badge bg-primary p-2 m-2">' . $page . '</a> ';
                        }
                    }

                    // Last page link if needed
                    if ($currentPage + $range < $totalPages - 1) {
                        echo '... ';
                        echo '<a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '" class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
                    } elseif ($currentPage + $range == $totalPages - 1) {
                        echo '<a href="?page=' . $totalPages . '&limit=' . $_GET['limit']  . '" class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
                    }
                }

                ?>



            </ul>
        </nav>
    </div>

</div>



<?php include("../layouts/footer.php"); ?>
<script>
    $(document).ready(function() {
        $("#customer").select2();

    });
</script>