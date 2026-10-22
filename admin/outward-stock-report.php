<?php
include("../layouts/header.php");


if (isset($_POST['btnOut'])) {

    $stmt = $mysqli->prepare("UPDATE stock_outward_mst set status='out for delivery',gr_no=?,gr_date=?,transport_name=?,est_delivery_date=? where id =? ");
    $stmt->bind_param("ssssi", $_POST['gr_no'], $_POST['gr_date'], $_POST['transport_name'], $_POST['est_delivery_date'], $_POST['id']);
    $stmt->execute();
    $stmt->close();
    alert("Save Successfully", "success");
    redirect("?status=" . $_GET['status'] . "&loc_id=" . $_GET['loc_id'] . "&getrecord&fromDt=" . $_GET['fromDt'] . "&toDt=" . $_GET['toDt'] . "");
}

if (isset($_POST['btnDelivered'])) {

    $stmt = $mysqli->prepare("UPDATE stock_outward_mst set status='delivered' where id =? ");
    $stmt->bind_param("i", $_POST['btnDelivered']);
    $stmt->execute();
    $stmt->close();
    alert("Save Successfully", "success");
    redirect("?status=" . $_GET['status'] . "&loc_id=" . $_GET['loc_id'] . "&getrecord&fromDt=" . $_GET['fromDt'] . "&toDt=" . $_GET['toDt'] . "");
}

if (isset($_POST['BtnEstDateChange'])) {
    $stmt = $mysqli->prepare("UPDATE stock_outward_mst set est_delivery_date=? where id =? ");
    $stmt->bind_param("si", $_POST['est_delivery_date'], $_POST['id']);
    $stmt->execute();
    $stmt->close();
    alert("Save Successfully", "success");
    redirect("?status=" . $_GET['status'] . "&loc_id=" . $_GET['loc_id'] . "&getrecord&fromDt=" . $_GET['fromDt'] . "&toDt=" . $_GET['toDt'] . "");
}

$per = permissions(9, $mysqli, $user);
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
<div class="row">
    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="pull-left">Out Stock</h4>
            <div class="pull-right">
                <?php 
                if($per['edit']==1){
                    echo '   <a href="outward-stock" class="btn btn-success float-end">Out Stock</a>';
                }
                ?>
             

            </div>
            <a class="btn <?php if ($_GET['status'] == "pending") echo "btn-success";
                            else echo  "btn-primary" ?> mt-4" href="?loc_id=<?= $_GET['loc_id'] ?>&status=pending&fromDt=<?= $_GET['fromDt'] ?>&toDt=<?= $_GET['toDt'] ?>">Pending</a>
            <a class="btn <?php if ($_GET['status'] == "out for delivery") echo "btn-success";
                            else echo  "btn-primary" ?> mt-4" href="?loc_id=<?= $_GET['loc_id'] ?>&status=out for delivery&fromDt=<?= $_GET['fromDt'] ?>&toDt=<?= $_GET['toDt'] ?>">Dispatched</a>
            <a class="btn <?php if ($_GET['status'] == "delivered") echo "btn-success";
                            else echo  "btn-primary" ?> mt-4" href="?loc_id=<?= $_GET['loc_id'] ?>&status=delivered&fromDt=<?= $_GET['fromDt'] ?>&toDt=<?= $_GET['toDt'] ?>">Delivered</a>
        </div>
        <div class="card-body table-responsive">
            <div class="form-group mb-3">

                <form method="get">
                    <div class="d-flex">
                        <div class="mx-1">
                            <input type="date" class="form-control" value="" name="fromDt" id="fromDt">
                        </div>
                        <div class="mx-1">
                            <input type="date" class="form-control" value="" name="toDt" id="toDt">
                        </div>
                        <div class="mx-1">

                            <input type="hidden" value="<?= $_GET['status'] ?>" name="status">


                            <select name="loc_id" id="loc_id" class="form-control" required>
                                <option value="">Select Location</option>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '">' . $row['name'] . ' ' . $row['address'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" name="getrecord" class="btn btn-success mx-1">Search</button>


                    </div>

                </form>



            </div>

            <table class="table  dataTable ">
                <thead class="">
                    <tr>
                        <th>S.no</th>
                        <th>Challan ID</th>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Est. Delivery Date</th>
                        <th>Customer</th>

                        <th>GR No.</th>
                        <th>GR Date.</th>
                        <th>Transport.</th>
                        <th>No. of Carton.</th>
                        <th>User</th>
                        <th>Action</th>


                    </tr>
                </thead>
                <tbody>
                    <?php {
                        //$loc_id = implode(',', $_GET['loc_id']);


                        $stmt = $mysqli->prepare("SELECT a.*,b.name,c.name as loc,d.name as user_name,e.company_name,b.mobile as number,f.contact_person,f.mobile_no,f.address as transport_address from stock_outward_mst a JOIN lead b on a.lead_id=b.id JOIN locations c on a.loc_id=c.id JOIN admin_users d on a.user_id=d.id left join company e on b.company_id=e.id left join mode_of_transport as f on a.transport_name=f.name  where a.status=? and a.loc_id =  ? and date(a.invoice_date)>=? and date(a.invoice_date)<=? order by a.id desc ");
                        $stmt->bind_param('siss', $_GET['status'], $_GET['loc_id'], $_GET['fromDt'], $_GET['toDt']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $count = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<tr>
                                  <td scope="row">' . $count++ . '</td>
                                  <td>' . $row['id'] . '</td>
                                  <td>' . $row['order_id'] . '</td> 
                                  <td>' . $row['created_date'] . '</td> 
                                  <td>' . $row['est_delivery_date'] . '</td> 
                              
                                    <td>' . $row['name'] . ' (' . $row['company_name'] . ')</td>
                                    <td>' . $row['gr_no'] . '  </td>
                                    <td>' . $row['gr_date'] . '  </td>
                                    <td>' . $row['transport_name'] . '  </td>
                                    <td>' . $row['no_of_curtains'] . '  </td>
                                    <td>' . $row['user_name'] . '</td>
                                    <td><form method="post"><a class="btn btn-primary btn-sm" href="outward-report?id=' . $row['id'] . '"><i class="fa fa-eye"></i></a>';
                                    if($per['edit']==1){

                                    

                            if ($row['status'] == "pending") {
                                echo ' <button type="button" name="btnSave" value="' . $row['id'] . '" data-gr_no="' . $row['gr_no'] . '"
                                 data-gr_date="' . $row['gr_date'] . '"
                                 data-transport_name="' . $row['transport_name'] . '"
                                 data-name="' . $row['name'] . '"
                                 data-company_name="' . $row['company_name'] . '"
                                 data-boxes="' . $row['no_of_curtains'] . '"
                                 data-address="' . $row['address'] . '"
                                 data-state="' . $row['state'] . '"
                                 data-city="' . $row['city'] . '"
                                 data-pincode="' . $row['pincode'] . '"
                                 data-contact_person="' . $row['contact_person'] . '"
                                 data-mobile_no="' . $row['mobile_no'] . '"
                                 data-transport_address="' . $row['transport_address'] . '"
                                
                                class="btn btn-dark btn-sm mx-1 OutForDelivery">Dispatched</button></form></td>';
                            } else if ($row['status'] == "out for delivery") {
                                echo ' <button type="submit" name="btnDelivered"  value="' . $row['id'] . '" data-number="' . $row['number'] . '" class="btn btn-dark btn-sm mx-1  btnDelivered">Delivered</button>
                                        <button type="button" class="btn btn-success btn-sm ChangeDate" data-id="' . $row['id'] . '" ><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                </form>
                        
                                </td>';
                            } else {
                                echo '<span class="badge bg-success mx-2">Delivered</span></form></td>';
                            }
                        }

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
    function printcontent() {
        $(".Buttons").hide();
        var printContents = document.getElementById('gatepass').innerHTML;
        var printWindow = window.open('', '', 'height=600,width=1200');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<style>@media print {@page { size: A6; margin: 0; } body { margin: 20px; }}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();

    }
</script>

<div class="modal fade" id="outForDelivery">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">


                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Out for Delivery
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <input type="hidden" name="id" id="oid">
                            <h4 class="mb-3">Are you sure you want to out this order?</h4>
                            <div class="col-md-12">
                                <label for="">GR No.</label>
                                <input type="text" name="gr_no" id="gr_no" class="form-control">

                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="">GR Date.</label>
                                <input type="date" name="gr_date" class="form-control" id="gr_date">

                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="">Transport .</label>
                                <select name="transport_name" id="transport_name" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT * from  mode_of_transport  ;");
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="">Estimate Delivery Date.</label>
                                <input type="date" name="est_delivery_date" class="form-control" id="est_delivery_date">

                            </div>

                        </div>

                        <div>
                            <button class="btn btn-primary btn-sm float-end mt-5" type="button" onclick="printcontent()">Print</button>
                        </div>
                        <div class="row mt-5" id="gatepass">
                            <div class="col-md-12 text-center">
                                <img src="../public/logo.png" width="175px">
                                <h3 class="mt-3">GATE PASS</h3>
                                <hr>
                            </div>

                            <div class="col-md-12">
                                Customer Name: <span id="namee"></span>
                            </div>

                            <div class="col-md-12 mt-3">
                                Transport Name : <span id="transport"></span>
                            </div>
                            <div class="col-md-12 mt-3">
                                Transport Contact Name : <span id="contact_person"></span>
                            </div>
                            <div class="col-md-12 mt-3">
                                Contact : <span id="mobile_no"></span>
                            </div>
                            <div class="col-md-12 mt-3">
                                Transport address : <span id="transport_address"></span>
                            </div>
                            <div class="col-md-12 mt-3">
                                No of Boxes: <span id="boxes"></span>
                            </div>
                            <div class="col-md-12 mt-3">
                                Delivery Address: <span id="address"></span>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnOut" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<form method="POST" class="needs-validation" novalidate>
    <div class="modal fade" id="ChangeDate">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Estimate Delivery Date
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="est_date_id" name="id">
                    <label for="">Estimate Delivery Date</label>
                    <input type="date" name="est_delivery_date" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary" name="BtnEstDateChange">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



<script>
    var modalId = document.getElementById('modalId');

    modalId.addEventListener('show.bs.modal', function(event) {
        // Button that triggered the modal
        let button = event.relatedTarget;
        // Extract info from data-bs-* attributes
        let recipient = button.getAttribute('data-bs-whatever');

        // Use above variables to manipulate the DOM
    });
</script>

<script>
    $(document).ready(function() {
        //change selectboxes to selectize mode to be searchable
        //  $("#loc_id").select2();

    });

    $(document).ready(function() {
        $('#customersTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    $(document).on("click", ".OutForDelivery", function() {
        $("#oid").val($(this).val())
        $("#gr_no").val($(this).data("gr_no"))
        $("#gr_date").val($(this).data("gr_date"))
        $("#transport_name").val($(this).data("transport_name"))

        $("#transport_address").html($(this).data("transport_address"))
        $("#mobile_no").html($(this).data("mobile_no"))
        $("#contact_person").html($(this).data("contact_person"))

        $("#namee").text($(this).data("name"))
        $("#boxes").text($(this).data("boxes"))
        $("#address").text($(this).data("address") + ' ' + $(this).data("city") + ' ' + $(this).data("state") + ' ' + $(this).data("pincode"))


        $("#outForDelivery").modal("show")
    });

    $(document).on("click", ".btnDelivered", function() {

    });

    $(document).on("click", ".ChangeDate", function() {
        $("#est_date_id").val($(this).data("id"))
        $("#ChangeDate").modal("show")
    });

    $("#loc_id").val("<?= $_GET['loc_id'] ?>")
    $("#status").val("<?= $_GET['status'] ?>")
    $("#fromDt").val("<?= $_GET['fromDt'] ?>")
    $("#toDt").val("<?= $_GET['toDt'] ?>")
</script>