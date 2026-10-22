<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT  a.*,b.name as location,c.name as user from defective_stock_mst a JOIN locations b on a.loc_id=b.id JOIN admin_users c on a.user_id=c.id where a.id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (isset($_POST['btnSave'])) {
    $stmt = $mysqli->prepare("UPDATE   defective_stock_det set repaired_qty=? where id=?");
    $stmt->bind_param("ii", $_POST['repaired_qty'], $_POST['id']);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT * from   defective_stock_mst  where id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $defective_stock_mst = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $mysqli->prepare("UPDATE   current_stock set current_stock=current_stock+? where prod_id=? and loc_id=?");
    $stmt->bind_param("iii", $_POST['repaired_qty'], $_POST['prod_id'],$defective_stock_mst['loc_id']);
    $stmt->execute();
    $stmt->close();


    
    alert("Save successfully.", "success", "success");
    redirect("?id=".$_GET['id']."");
}
?>

<style>
    @media print {
        .noPrint {
            display: none;
            margin-top: 0px;
        }
    }

    body {
        margin-top: 10px;
        color: #484b51;
    }

    .text-secondary-d1 {
        color: #728299 !important;
    }

    .page-header {
        margin: 0 0 1rem;
        padding-bottom: 1rem;
        padding-top: .5rem;
        border-bottom: 1px dotted #e2e2e2;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -ms-flex-align: center;
        align-items: center;
    }

    .page-title {
        padding: 0;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 300;
    }

    .brc-default-l1 {
        border-color: #dce9f0 !important;
    }

    .ml-n1,
    .mx-n1 {
        margin-left: -.25rem !important;
    }

    .mr-n1,
    .mx-n1 {
        margin-right: -.25rem !important;
    }

    .mb-4,
    .my-4 {
        margin-bottom: 1.5rem !important;
    }

    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, .1);
    }

    .text-grey-m2 {
        color: #888a8d !important;
    }

    .text-success-m2 {
        color: #86bd68 !important;
    }

    .font-bolder,
    .text-600 {
        font-weight: 600 !important;
    }

    .text-110 {
        font-size: 100% !important;
    }

    .text-blue {
        color: #478fcc !important;
    }

    .pb-25,
    .py-25 {
        padding-bottom: .75rem !important;
    }

    .pt-25,
    .py-25 {
        padding-top: .75rem !important;
    }

    .bgc-default-tp1 {
        background-color: rgba(121, 169, 197, .92) !important;
    }

    .bgc-default-l4,
    .bgc-h-default-l4:hover {
        background-color: #f3f8fa !important;
    }

    .page-header .page-tools {
        -ms-flex-item-align: end;
        align-self: flex-end;
    }

    .btn-light {
        color: #757984;
        background-color: #f5f6f9;
        border-color: #dddfe4;
    }

    .w-2 {
        width: 1rem;
    }

    .text-120 {
        font-size: 110% !important;
    }

    .text-primary-m1 {
        color: #4087d4 !important;
    }

    .text-danger-m1 {
        color: #dd4949 !important;
    }

    .text-blue-m2 {
        color: #68a3d5 !important;
    }

    .text-150 {
        font-size: 150% !important;
    }

    .text-60 {
        font-size: 60% !important;
    }

    .text-grey-m1 {
        color: #7b7d81 !important;
    }

    .align-bottom {
        vertical-align: bottom !important;
    }

    #itemlist td {
        padding: 6px 15px;
    }
</style>
<script>
    function printcontent() {
        var printContents = document.getElementById('printorder').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>


<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Defective Report</h4>
            <div class="pull-right">
                <button class="noPrint btn bg-white btn-light mx-1px text-95" onclick="printcontent()" data-title="Print">
                    <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                    Print
                </button>
            </div>
        </div>
        <div class="card-body table-responsive" id="printorder">
            <div class="row">
                <div class="col-md-12" style="display: flex; justify-content: space-between;">
                    <div>

                        Location : <?= $res['location'] ?> <br>
                        Staff : <?= $res['user'] ?> <br>
                        Description : <?= $res['description'] ?> <br>
                    </div>
                    <div>

                        Created Date : <?= $res['created_at'] ?>
                    </div>
                </div>

            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h4>Products</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">UOM</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,b.price as price,b.uom from  defective_stock_det a JOIN products b on a.prod_id=b.id where a.defective_mst_id=?");
                                $stmt->bind_param('i', $res['id']);
                                $stmt->execute();
                                $products = $stmt->get_result();
                                while ($row = $products->fetch_assoc()) {
                                    echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        <td>' . $row['product'] . '</td>
                                                        <td>' . $row['price'] . '</td>
                                                        <td>' . $row['qty'] . '</td>
                                                        <td>' . $row['uom'] . '</td>
                                                        <td>' . $row['price'] * $row['qty'] . '</td>
                                                        <td>' . $row['status']  . '</td>
                                                        <td>' . $row['created_at'] . '</td>
                                                        <td><button type="button" class="btn btn-primary btn-sm Edit" data-id="' . $row['id'] . '" data-repaired_qty="' . $row['qty'] . '" data-prod_id="' . $row['prod_id'] . '"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                                                    </tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>


                </div>


            </div>


        </div>
    </div>
</div>
<div class="modal fade" id="modalId">
    <div class="modal-dialog" role="document">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Repaired QTY
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="prod_id" name="prod_id">
                    <label for="">Repaired Qty</label>
                    <input type="" name="repaired_qty" id="repaired_qty" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnSave" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include("../layouts/footer.php"); ?>






<script>
    $(document).on("click", '.Edit', function() {

        $("#id").val($(this).data("id"))
        $("#prod_id").val($(this).data("prod_id"))
        $("#repaired_qty").val($(this).data("repaired_qty"))
        $("#modalId").modal("show");
    })
</script>