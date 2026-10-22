<?php
include("../layouts/header.php");

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
        $(".buttons").hide()
        var printContents = document.getElementById('printorder').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>


<div class="row mt-3">
    <div class="col-md-12">

        <h4>Challan logs</h4>
        <button class="noPrint btn bg-white btn-light mx-1px text-95 float-right" onclick="printcontent()" data-title="Print">
            <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
            Print
        </button>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Challan Id</th>
                        <th scope="col">User</th>
                        <th scope="col">Particulars</th>
                        <th scope="col">Created Date</th>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sno = 1;
                    $stmt = $mysqli->prepare("SELECT  a.*,b.name,c.name as user from  challan_logs a join  products b on a.product_id=b.id join  admin_users c on a.user_id=c.id ");
                    //   $stmt->bind_param('i', $res['id']);
                    $stmt->execute();
                    $products = $stmt->get_result();
                    while ($row = $products->fetch_assoc()) {
                        echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            <td>' . $row['name'] . '</td>
                                            <td>' . $row['qty'] . '</td>
                                            <td>' . $row['challan_id'] . '</td>
                                            <td>' . $row['user'] . '</td>
                                            <td>' . $row['particulars'] . '</td>
                                            <td>' . $row['created_at'] . '</td>
                                            </tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>


    </div>


</div>




<?php include("../layouts/footer.php"); ?>