<?php
include("../layouts/header.php");
$where = "";
if (!empty($_GET['date'])) {
    $where .= "and date(created_date)>=date(?)";
}
if (!empty($_GET['todate'])) {
    $where .= "and date(created_date)<=date(?)";
}
if (isset($_GET['loc_id']) && isset($_GET['product_id'])) {
    $stmt = $mysqli->prepare("select t.*,b.current_stock from (SELECT b.loc_id,a.product_id,'inward' as tran_type,a.qty,b.created_date,c.name as user_name,d.con_person_name as vendor_customer,b.invoice as invoice from stock_inward_det a join stock_inward_mst b on a.stock_mst_id=b.id JOIN admin_users c on b.user_id=c.id JOIN vendor d on b.vendor_id=d.id 
union
SELECT b.loc_id,product_id,'outward' as tran_type,qty,b.created_date,c.name as user_name,d.name as  vendor_customer,b.id as invoice from stock_outward_det a join stock_outward_mst b on a.stock_outward_mst_id=b.id JOIN admin_users c on b.user_id=c.id JOIN lead d on b.lead_id=d.id
union
SELECT c.loc_id,product_id,'sale-return' as tran_type,qty,b.created_at as created_date,d.name as user_name,e.name as vendor_customer,b.challan_id as invoice from sale_return_det a join sale_return_mst b on a.sale_return_mst_id=b.id join stock_outward_mst c on b.challan_id=c.id JOIN admin_users d on b.user_id=d.id JOIN lead e on b.lead_id=e.id
union
SELECT c.loc_id,product_id,'purchase-return' as tran_type,qty,b.created_at as created_date,d.name as user_name,e.con_person_name as vendor_customer,c.invoice as invoice from purchase_return_det a join purchase_return_mst b on a.p_return_id=b.id join stock_inward_mst c on b.invoice_id=c.id JOIN admin_users d on b.user_id=d.id JOIN vendor e on b.vendor_id=e.id )t left join current_stock b on t.product_id=b.prod_id and t.loc_id=b.loc_id where t.loc_id=? and t.product_id=? " . $where . " order by t.created_date");

    if (!empty($_GET['date']) && !empty($_GET['todate'])) {
        $stmt->bind_param("iiss", $_GET['loc_id'], $_GET['product_id'], $_GET['date'], $_GET['todate']);
    } else {
        $stmt->bind_param("ii", $_GET['loc_id'], $_GET['product_id']);
    }

    $stmt->execute();
    $report = $stmt->get_result();
}

?>

<div class="content-wrap">
    <!-- main page content. the place to put widgets in. usually consists of .row > .col-lg-* > .widget.  -->
    <main id="content" class="content" role="main">
        <div class="container">
            <div class="row">
                <div class="card col-lg-12">
                    <div class="card-header">
                        <h4 class="pull-left">Product Report</h4>

                    </div>
                    <div class="card-body table-responsive">
                        <div class="form-group col-md-10">
                            <table class="table">
                                <form method="get">
                                    <tr>
                                        <td>
                                            <select name="loc_id" id="loc_id" class="form-control" required>
                                                <option value="">--Select Locations--</option>
                                                <?php
                                                $stmt = $mysqli->prepare("SELECT * from   locations  ;");
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_assoc()) {
                                                    echo '<option value="' . $row['id'] . '" >' . $row['name'] . ', ' . $row['address'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="product_id" id="product_id" class="form-control" required>
                                                <option value="">--Select Product--</option>
                                                <?php
                                                $stmt = $mysqli->prepare("SELECT * from   products order by name asc  ;");
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_assoc()) {
                                                    echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" name="date" id="date" class="form-control">

                                        </td>
                                        <td>
                                            <input type="date" name="todate" id="todate" class="form-control">
                                        </td>
                                        <td>
                                            <button type="submit" name="getrecord" class="btn btn-success">Search</button>
                                        </td>
                                    </tr>
                                </form>
                            </table>

                        </div>

                        <table class="table table-hover datatable ">
                            <thead class="bg-ligth text-dark">
                                <tr>
                                    <th>S.no</th>
                                    <th>Trans. Type</th>
                                    <th>Vendor/Customer</th>
                                    <th>Invoice/Challan</th>
                                    <th>Qty</th>
                                    <th>Balance</th>
                                    <th>Staff</th>

                                    <th>Created Date</th>


                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                $current_stock = 0;
                                if (!empty($report)) {


                                    while ($row = $report->fetch_assoc()) {
                                        if ($sno == 1) $current_stock = (int)$row['current_stock'];
                                        if ($row['tran_type'] == "outward") {
                                            $current_stock = $current_stock - $row['qty'];
                                        } else if ($row['tran_type'] == "inward") {
                                            $current_stock = $current_stock + $row['qty'];
                                        } else if ($row['tran_type'] == "sale-return") {
                                            $current_stock = $current_stock + $row['qty'];
                                        } else if ($row['tran_type'] == "purchase-return") {
                                            $current_stock = $current_stock - $row['qty'];
                                        }
                                        echo '<tr>
                                <td>' . $sno++ . '</td>
                                <td>' . $row['tran_type'] . '</td>
                                <td>' . $row['vendor_customer'] . '</td>
                                <td>' . $row['invoice'] . '</td>
                                <td>' . $row['qty'] . '</td>
                                        
                                <td>' . $current_stock . '</td>
                                <td>' . $row['user_name'] . '</td>
                                <td>' . $row['created_date'] . '</td>
                                </tr>';
                                    }
                                }
                                ?>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>



<?php
include("../layouts/footer.php");
?>


<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        $("#loc_id").select2();
        $("#product_id").select2();

    });

    $(document).ready(function() {
        $('#customersTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
    $("#loc_id").val(<?= $_GET['loc_id'] ?>)
    $("#product_id").val(<?= $_GET['product_id'] ?>)
    $("#date").val("<?= $_GET['date'] ?>")
    $("#todate").val("<?= $_GET['todate'] ?>")
</script>