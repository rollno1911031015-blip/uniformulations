 <?php include("header.php");
$stmt = $mysqli->prepare("SELECT a.*,b.name,b.mobile,b.address,b.state,b.city,b.pincode,b.gst,c.company_name from order_mst a join lead b on a.customer_id=b.id join company c on b.company_id=c.id where a.id=?");
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


 <div class="page-content space-top">

     <div class="container p-0 mb-5">

         <div class="card">
             <div class="card-header">
                 <div class=" float-end">
                     <button class="btn btn-outline-success " onclick="printcontent()" data-title="Print">
                         <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                         Print
                     </button>
                 </div>

             </div>



             <div class="card-body" id="printorder">




                 <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                     <div>
                         <h6>DM PHARMA (GLOBAL) PRIVATE LIMITED</h6>
                         <p>Office : SCO-176, 2ND Floor, Sector-38c Chandigarh
                             <br>
                             Godown Khewat no 313, Khatuni No 441 Khasra No 30/27/1, Village Maloya CHD <br>
                             Phone : 7717304942

                         </p>


                     </div>


                     <div>
                         <div style="text-align: right;">
                             GST No : 04AJCD0060D1ZC <br>
                             State Code : 04
                             <br>
                             LF20B2024CH000148 & WLF21B2024CH000149<BR>
                             FASSAI No : 13022999000046 <br>
                             E-Mail : paxfomation@dmpharma.org
                         </div>
                     </div>
                 </div>


                 <div style="display: flex; justify-content: space-between;">
                     <div class="" style="border: solid 1px; width: 50%; padding: 8px;">
                         To <br>

                         M/s <?= $res['company_name'] ?><br>
                         <?= $res['address'] ?>, <?= $res['city'] ?>, <?= $res['state'] ?>, <?= $res['pincode'] ?><br>

                         Contact: <?= $res['mobile'] ?>
                     </div>
                     <div class="" style="border: solid 1px; width: 50%; padding: 8px;">
                         Bill No: <?= $res['id'] ?><br>


                         Dated : <?= $res['created_at'] ?> <br>
                         GST: <?= $res['gst'] ?><br>

                     </div>
                     <div></div>

                 </div>
                 <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Particulars</th>
                                    <th scope="col">HSN Code</th>

                                    <th scope="col">Pack</th>
                                    <th scope="col">Batch No</th>
                                    <th scope="col">Exp.</th>


                                    <th scope="col">MRP</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Rate</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">IGST % </th>
                                    <th scope="col">GST </th>
                                    <th scope="col">Dis % </th>
                                    <?php
                                    if (($res['status'] == "pending"))
                                        echo '<th scope="col">Pending Qty</th>';
                                    ?>




                                    <?php
                                    if (!empty($_GET['edit'])) {
                                        echo '<th>
                                    <button type="button" id="btn_success" class="btn btn-success btn-sm Add_Product buttons"><i class="fa fa-plus"></i> </button>
                                </th>';
                                    }

                                    ?>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                $stmt = $mysqli->prepare("SELECT  a.*,b.name as product,a.price as price,b.uom,b.brand,b.hsn_code,b.packing,b.igst,b.price as mrp from order_det a JOIN products b on a.product_id=b.id where a.mst_id=?");
                                $stmt->bind_param('i', $res['id']);
                                $stmt->execute();
                                $products = $stmt->get_result();
                                $gst_total=0;
                                $sub_total=0;
                                while ($row = $products->fetch_assoc()) {
                                    $gst_amt=0;
                                    $gst_amt =  ($row['price'] * $row['qty'])*$row['gst_per']/100;
                                    $gst_total += $gst_amt;
                                    $sub_total += $row['price'] * $row['qty'];
                                    echo '<tr>
                                                        <td>' . $sno++ . '</td>
                                                        
                                                        <td>' . $row['product'] . '</td>
                                                        <td>' . $row['hsn_code'] . '</td>
                                                        <td>' . $row['packing'] . '</td>
                                                        <td>' . $row['batch_no'] . '</td>
                                                        <td>' . $row['expiry_date'] . '</td>
                                                       
                                                        ';

                                    if ($res['status'] == "pending") {
                                        echo '<td>' . $row['qty'] - $row['out_qty'] . '</td>';
                                    }



                                    echo  '<td>' . $row['mrp'] . '</td>
                                                        <td>' . $row['qty'] . '</td>
                                                       
                                                        <td>' . ($row['price']) - (($row['price']) / 100 * $row['discount']) . '</td>
                                                        
                                                         <td>' . ($row['price'] * $row['qty']) - (($row['price'] * $row['qty']) / 100 * $row['discount']) . '</td>
                                                           <td>' . $row['gst_per'] . '</td>
                                                           <td>' . $gst_amt . '</td>
                                                           <td>' . $row['discount'] . '</td>
                                                        ';

                                    if (!empty($_GET['edit'])) {
                                        echo   '<td class="buttons">
                                                                            <button type="button" class="btn btn-info btn-sm EDIT"     
                                                                            data-id="' . $row['id'] . '"
                                                                            data-qty="' . $row['qty'] . '"
                                                                            data-product_id="' . $row['product_id'] . '"
                                                                            
                                                                            ><i class="fa fa-pencil"
                                                                        
                                                                            ></i></button>
                                                                            <button type="button" class="btn btn-danger btn-sm Delete" data-id="' . $row['id'] . '">
                                                                            <i class="fa fa-trash"></i></button>';
                                    }




                                    echo  '</td>
                                                      
                                                    </tr>';
                                }
                                ?>

                            </tbody>
                            <tbody>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>Sub Total</th>
                                    <th><?=$sub_total?></th>
                                </tr>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>GST</th>
                                    <th><?=$gst_total?></th>
                                </tr>
                                <tr>
                                    <th colspan="11">

                                    </th>
                                    <th>Grand Total</th>
                                    <th><?=$gst_total+$sub_total?></th>
                                </tr>
                            </tbody>
                        </table>

                 <div class="d-flex mt-4 justify-content-between">
                     <div>
                         <p><b><u><i>Terms & Conditions</i></u></b></p>
                         <ol style="list-style:number;">
                             <li>All disputes are subject to chandigarh jurisdiction.</li>
                             <li>We declare that this invoice shows the actual price of the goods described and that all particulars are true.</li>
                             <li>No responsibility for damage/pilferage in transit. </li>
                             <li>Goods once sold will be not taken back.</li>
                             <li>Interest @18% will charged after due date.</li>

                         </ol>
                     </div>
                     <div>
                         <h6 class="float-end">For DM PHARMA (GLOBAL) PRIVATE LIMITED</h6>

                         <p class="mt-5">Authorized Signatory</p>
                     </div>

                 </div>


             </div>
         </div>
     </div>
 </div>


 <?php


    include("footer.php");


    ?>