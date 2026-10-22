<?php
include("../layouts/header.php");


$stmt = $mysqli->prepare("SELECT a.*,b.con_person_name as vendor, c.name as user,d.address as location from   po_mst a join vendor b on a.vendor_id=b.id join admin_users c on a.user_id=c.id join locations d on a.loc_id=d.id where a.id=?  ;");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

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
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>PO</h4>
        </div>

    </div>
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">PO</h4>
            <div class="pull-right">
                <button class="noPrint btn bg-white btn-light mx-1px text-95" onclick="printcontent()" data-title="Print">
                    <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                    Print
                </button>

            </div>

        </div>
        <div class="card-body" id="printorder">
            <div class="row">

                <div style="display: flex; justify-content: space-between;">
                    <div>
                        GSTIN : 02AARFA0495L1Z9
                    </div>
                    <div>
                        Purchase Order

                    </div>
                    <div>
                        <div style="text-align: right;">
                            Mobile : 7206682066
                        </div>
                    </div>
                </div>
                <div class="text-center d-flex justify-content-center">
                    <!-- <div>
                        <img src="logo.png" style="width: 80px;">
                    </div> -->
                    <div style="margin-left: 1rem;">
                        <h1>Clikzop Expertz</h1>
                        <h6>Address</h6>
                    </div>
                </div>



            </div>
            <div class="row">
                <div class="col-md">
                    Vendor : <?= $res['vendor'] ?> <br>
                    Location : <?= $res['location'] ?><br>
                    Description : <?= $res['description'] ?><br>
                    User : <?= $res['user'] ?>

                </div>

            </div>
            <div class="">
                <hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="3"></th>
                            <th colspan="2">PO</th>
                            <th colspan="2">Inward</th>
                        </tr>
                        <tr>
                            <th>S.No</th>
                            <th>#</th>
                            <th>Product </th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,c.qty as inward_qty,c.price as inward_price  from   po_det a join products b on a.product_id=b.id join stock_inward_det c on a.product_id=c.product_id where a.po_id=? group by a.id;");
                        $stmt->bind_param('i', $_GET['id']);
                        $stmt->execute();
                        $po_det = $stmt->get_result();
                        $sno = 1;

                        while ($row = $po_det->fetch_assoc()) {
                            $checked = "";
                            if ($row['is_received'] == 1) {
                                $checked = "checked";
                            }
                            echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            <td><input type="checkbox" class="check" data-id="' . $row['id'] . '" ' . $checked . '></td>
                                            <td>' . $row['product_name'] . '</td>
                                            <td>' . $row['qty'] . '</td>
                                            <td>' . $row['price'] . '</td>
                                            <td>' . $row['inward_qty'] . '</td>
                                            <td>' . $row['inward_price'] . '</td>
                                     </tr>';
                        }

                        ?>
                    </tbody>

                </table>


            </div>

        </div>
    </div>

</div>






<?php include("../layouts/footer.php"); ?>

<script>
    function printcontent() {
        $(".buttons").hide()
        var printContents = document.getElementById('printorder').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }

    $(document).on("click", ".check", function() {

        var check = $(this).data("id")
        var value = 0;
        if ($(this).prop('checked') == true) {
            value = 1;
        }
        $.ajax({
            method: 'POST',
            url: 'ajax/check-product-received.php',
            data: {
                value: value,
                id: check
            },
            success: function(data) {

                data = JSON.parse(data)
                toastr.success(data.msg);

            }
        });
    });
</script>