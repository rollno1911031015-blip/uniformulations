<?php
include("../layouts/header.php");


$per = permissions(4, $mysqli, $user);


?>

<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Inward Stock</h4>
            <div class="pull-right">
                <?php 
                if($per['edit']==1){
                    ?>
                                 <a href="inward-stock" class="btn btn-success float-end">New Stock</a>

                    <?php
                }
                ?>
   
            </div>
            <div>

            </div>
        </div>
        <div class="card-body table-responsive">
            <div class="form-group col-md-8">
              
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="date" name="fromDt" value="<?= $_GET['fromDt'] ?>" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="toDt" value="<?= $_GET['toDt'] ?>" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <select name="loc_id" id="loc_id" class="form-control" required>

                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '"> ' . $row['name'] . ', ' . $row['address'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="" class="btn btn-success">Search</button>
                            </div>
                        </div>
                    </form>
            

            </div>

            <table class="table table-hover datatable ">
                <thead class="bg-ligth text-dark">
                    <tr>
                        <th>#</th>
                        <th>Created Date</th>
                        <th>Vendor</th>
                        <th>Invoice No.</th>
                        <th>Location</th>
                        <th>User</xth>
                        <th>Action</xth>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['loc_id'])) {
                        //$loc_id = implode(',', $_GET['loc_id']);
                        $stmt = $mysqli->prepare("SELECT a.*,d.name as loc,d.address as address_loc,e.name as user_name,f.con_person_name as vendor,g.company_name from   stock_inward_mst a JOIN locations d on a.loc_id=d.id JOIN admin_users e on a.user_id=e.id JOIN vendor f on a.vendor_id=f.id left join company g on f.company_id=g.id where a.loc_id=? and date(a.invoice_date)>=? and date(a.invoice_date)<=?");
                        $stmt->bind_param('iss', $_GET['loc_id'], $_GET['fromDt'], $_GET['toDt']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $count = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<tr>
                                  <td scope="row">' . $count++ . '</td>';
                            echo '<td>' . $row['created_date'] . '</td>
                                 <td>'   . $row['company_name']  . '</td>
                                    <td>' . $row['invoice'] . ' </td>
                                    <td> ' . $row['address_loc'] . '</td>
                                    <td>' . $row['user_name'] . '</td>
                                    <td><a class="btn btn-primary btn-sm" href="inward-report?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a></td>
                                    ';

                            echo '
                              
                                </tr>';
                        }
                    }

                    ?>


                </tbody>
            </table>
        </div>
    </div>
</div>


<?php
include("../layouts/footer.php")



?>
<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        // $("#loc_id").select2();

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
</script>