<?php
include("../layouts/header.php");
$stmt = $mysqli->prepare("SELECT * from   requirement_mst where id=?  ;");
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

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Requirement List View</h4>
        </div>
        <div>
            <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
        </div>

    </div>
    <div class="card-body" id="printorder">
        <div class="row border-1">
            <div class="col-md-6" style="border: solid 1px; padding-top: 5px;">
                <h6><U> <b> BILLING & DELIVERY IN FAVOUR OF</b></U><br>
                    <b>DM PHARMA GLOBAL PRIVATE LIMITED</b>
                </h6>
                <p>Near Gurudwara Khewat No. 313 Khatuni no3 441 Khasra no 30/27/1,
                    <br> Village Maloya Chandigarh
                    <br>
                    Tel No : 9216955581, 9317503300
                    GST mo : 04AAJCD0060D1ZC <br>
                    D.L. NO : WLF20B2024CH000148 & WLF21B2024CH000149 <br>
                    FSSAI No : 13022999000046 <br>
                    E-Mail : paxformation@dmpharma.org


                </p>


            </div>
            <div class="col-md-6" style="border: solid 1px; padding-top: 5px;">
                <div class="text-center">
                    Requirement List
                </div>
                Name : <?= $res['name'] ?> <br>
                
                Description : <?= $res['description'] ?><br>


            </div>
           
             

        </div>
        <div class="">
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th>S.No</th>

                      
                        <th>Brand Name </th>
                        <th>Composition </th>
                        <th>Pack </th>
                        <th>Type </th>
                        <th>Category </th>
                        <th>Company </th>

                        <th>Qty</th>
                       
                        <th>MRP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $mysqli->prepare("SELECT a.*,b.name as product_name,b.brand,b.composition,b.packing,b.uom,b.category,b.price as mrp  from   requirement_det a join products b on a.product_id=b.id where a.mst_id=?  ;");
                    $stmt->bind_param('i', $_GET['id']);
                    $stmt->execute();
                    $po_det = $stmt->get_result();
                    $sno = 1;
                    $total = 0;

                    while ($row = $po_det->fetch_assoc()) {
                        $total += $row['qty'] * $row['mrp'];

                        echo '<tr>
                                            <td>' . $sno++ . '</td>
                                            
                                         
                                            <td>' . $row['name'] . '</td>
                                            <td>' . $row['composition'] . '</td>
                                            <td>' . $row['packing'] . '</td>
                                            <td>' . $row['uom'] . '</td>
                                            <td>' . $row['category'] . '</td>
                                            <td>DM Pharma</td>
                                            <td>' . $row['qty'] . '</td>
                                            
                                            <td>' . $row['mrp'] . '</td>
                                     </tr>';
                    }

                    ?>
                    <tr>
                        <td>E.&.O.E</td>
                        <td colspan="4" style="text-align: right;">Total Amount</td>
                        <td><?= $total ?></td>
                        <td colspan="5">
                            Payment Terms = 60 Days <br>
                            Foil cyclinder charges= <br>
                            Inventory charges= <br>
                            Approval charges= <br>
                        </td>
                    </tr>
                </tbody>

            </table>
            <div class="row mt-4">
                <div class="col-md-6">
                    <u> <b> <i> Terms & Conditions</i></b></u>
                    <ul style="font-size: 11px; margin-left: 10px;">
                        <li>
                            All disputes are subject ot chandigarh jurisdiction. </li>
                        <li>
                            Immediate supply will be highly appreciable </li>
                        <li>
                            Please use 300gsm. Endura Or ITC Board with Matt Finish </li>
                        <li>
                            You are also requested to arrange to send the copy of each & every batch along with invoice copy for our record. </li>
                        <li>
                            Kindly manufacture the mention qty. in PO, if it will increase then it will not be accepted.</li>
                        <li>
                            Please send the art work before of previous and new packing material before processing the order </li>
                        <li>
                            all material should be received before 5:00 PM otherwise fright will be bear by water.</li>
                    </ul>

                </div>
                <div class="col-md-6" style="text-align: right;">
                    For DM PHARMA (GLOBAL) PRIVATE LIMITED
                    <br>
                    <br>
                    <br>
                    <br>
                    Authorized Signatory
                </div>

            </div>

        </div>

    </div>

</div>






<?php include("../layouts/footer.php"); ?>