<?php
include("../layouts/header.php");

if (isset($_POST["btnRepeatOrder"])) {
    try {
        //code...

        $stmt = $mysqli->prepare("SELECT * from po_mst where id=?");
         $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $po_mst = $stmt->get_result()->fetch_assoc();

        $stmt = $mysqli->prepare("INSERT into po_mst(vendor_id,description,user_id,status,loc_id,delivery_date,transport_mode) values(?,?,?,'Generate PO',?,?,?);");
        $stmt->bind_param("isiiss", $po_mst['vendor_id'],   $po_mst['description'], $user['id'], $po_mst['loc_id'], $po_mst['delivery_date'], $po_mst['transport_date']);
        $stmt->execute();
        $stock_id = $mysqli->insert_id;
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT * from po_det where po_id=?");
        $stmt->bind_param("i", $po_mst['id']);
        $stmt->execute();
        $po_det = $stmt->get_result();
        while ($row = $po_det->fetch_assoc()) {


            $stmt = $mysqli->prepare("INSERT into  po_det(po_id,product_id,qty,price) values(?,?,?,?);");
            $stmt->bind_param("iiid",  $stock_id, $row['id'], $row['qty'], $row['price']);
            $stmt->execute();
            $stmt->close();
        }
    } catch (\Throwable $th) {
        echo $th->getMessage();
        die;
    }
    alert('Save successfully.', 'success');
    redirect('?status='.$_GET['status'].'');
}

if (isset($_POST['btnCancelPO'])) {

    $stmt = $mysqli->prepare("UPDATE po_mst set status='cancel' where id=?");
    $stmt->bind_param("i", $_POST['id']);
   $stmt->execute();
    alert('Save successfully.', 'success');
    redirect('?status='.$_GET['status'].'');
}

$per = permissions(4, $mysqli, $user);
?>

<div class="card">
    <div class="card-header ">
        <div class="page-title">
            <h4>Purchase Order</h4>
        </div>

        <div class="mt-4">
            <a class="btn <?php if($_GET['status']=='Generate PO') echo 'btn-success'; else echo  'btn-primary' ?>" href="generated-po?status=Generate PO">Generated PO</a>
            <a class="btn <?php if($_GET['status']=='Received PO') echo 'btn-success'; else echo  'btn-primary' ?>" href="generated-po?status=Received PO">Received PO</a>
            <a class="btn <?php if($_GET['status']=='cancel') echo 'btn-success'; else echo  'btn-primary' ?>" href="generated-po?status=cancel">Cancel PO</a>
            <?php
            if ($per['edit'] == 1) {
            ?>
                <a class="btn btn-dark float-end" href="generate-po">Generate PO</a>

            <?php
            }
            ?>

        </div>
    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <th>S.No</th>
                <th>Vendor</th>
                <th>Description</th>
                <th>User</th>
                <th>Location</th>

                <th>Created at</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php
                $stmt = $mysqli->prepare("SELECT a.*,b.con_person_name as vendor, c.name as user,d.address as location,e.company_name as company from   po_mst a join vendor b on a.vendor_id=b.id join admin_users c on a.user_id=c.id join locations d on a.loc_id=d.id  join company  e on b.company_id=e.id where a.status=?  ;");
                $stmt->bind_param('s', $_GET['status']);
                $stmt->execute();
                $res = $stmt->get_result();
                $sno = 1;
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            <td>' . $row['company'] . '</td>
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                            <td>' . $row['location'] . '</td>
                                          
                                            <td>' . $row['created_at'] . '</td>
                                            <td>';

                                           

                                            if($_GET['status']=="Generate PO"){
                                                echo '<button class="btn btn-danger btn-sm cancelPo" type="button" value="' . $row['id'] . '">Cancel PO</button>';
                                            }
                                            
                                            echo '<a href="generated-po-view?id=' . $row['id'] . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>';

                                            
                                            
                                            echo '</td>
                                         </tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>





<form method="POST">
    <div
        class="modal fade"
        id="modalId">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Repeat Order
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <h5>Repeat this order?</h5>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnRepeatOrder" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form method="POST">
    <div
        class="modal fade"
        id="cancelPoModal">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                      Cancel PO
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cid" name="id">
                    <h5>Cancel this order?</h5>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnCancelPO" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include("../layouts/footer.php"); ?>


<script>
    $(document).on("click", ".repeatOrder", function() {
        $("#id").val($(this).val())
        $("#modalId").modal("show")
    })

    $(document).on("click", ".cancelPo", function() {
        $("#cid").val($(this).val())
        $("#cancelPoModal").modal("show")
    })
</script>