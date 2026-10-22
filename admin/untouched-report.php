<?php
include("../layouts/header.php");



$res1 = [];
$ids = explode(", ", $user['division']);
$search = '';
$division = '';
if (!empty($_GET['search'])) {
    $search = '%' . $mysqli->real_escape_string($_GET['search']) . '%';
} else {
    $search = "%";
}

if (!empty($_GET['division'])) {
    $division = $_GET['division'];
} else {
    $division = "%";
}

if (empty($_GET['limit'])) {
    $_GET['limit'] = 20;
}
$recordsPerPage = $_GET['limit'];

// Determine the current page
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1;
}

if (!empty($_GET['days'])) {
    $days = $_GET['days'];
} else {
    $days = 7;
}


if (!empty($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "customer";
}

if ($type == "customer") {
    $tp = "=";
} else {
    $tp = "!=";
}

$toDate = date("Y-m-d", strtotime("-$days days"));


$totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM lead WHERE (name LIKE ? or mobile LIKE ? or alternate_phone LIKE ?) AND division = ? ");
$totalRecordsQuery->bind_param("ssss", $search, $search, $search, $division); // Bind search parameter
$totalRecordsQuery->execute();
$totalRecordsResult = $totalRecordsQuery->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Determine the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Calculate the starting record for the current page
$startFrom = ($currentPage - 1) * $recordsPerPage;
foreach ($ids as $key => $value) {

    $stmt1 = $mysqli->prepare("SELECT a.*, b.company_name as company_name,c.name as user,d.name as division FROM lead a left JOIN company b ON a.company_id = b.id join admin_users as c on a.user_id=c.id join brand d on FIND_in_set(d.id,a.division) WHERE  (a.name like ? or a.mobile LIKE ? or a.alternate_phone LIKE ? or a.division LIKE ? or b.company_name LIKE ?)  AND a.status " . $tp . " 4 and a.active=1 and date(a.updated_at)<=date(?) limit ?,?");
    $stmt1->bind_param("ssssssii", $search, $search, $search, $search, $search,  $toDate, $startFrom, $recordsPerPage);
    $stmt1->execute();

    $result = $stmt1->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $res1[] = $row; // Append each row to the result array
        }
    }
}



$json = json_encode($res1);
$array = json_decode($json, true);

$array = array_filter($array);

// Step 3: Optionally remove duplicates (based on 'id' or another unique field)
$uniqueArray = [];
foreach ($array as $item) {
    if (!isset($uniqueArray[$item['id']])) {
        $uniqueArray[$item['id']] = $item;
    }
}

// Step 4: Re-encode to JSON
$cleanedJson = (array_values($uniqueArray));




$per = permissions(8, $mysqli, $user);
?>

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: green !important;
        border: 1px solid #aaa Im !important;
        border-radius: 4px;
        cursor: default;
        float: left;
        margin-right: 5px;
        margin-top: 5px;
        padding: 0 5px;
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice,
    .select2-container--default .select2-selection--multiple .select2-selection__choice,
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        background-color: none !important;
        border-color: #17a2b8;
        color: #ffffff;
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Untouched Report</h4>
        </div>
        <div class="">




        </div>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="d-flex justify-content-between">
                <div class="">
                    <select name="limit" id="limit" onchange="this.form.submit()" class="form-control">
                        <option value="20" <?php echo isset($_GET['limit']) && $_GET['limit'] == 20 ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo isset($_GET['limit']) && $_GET['limit'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['limit']) && $_GET['limit'] == 100 ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo isset($_GET['limit']) && $_GET['limit'] == 200 ? 'selected' : ''; ?>>200</option>
                        <option value="300" <?php echo isset($_GET['limit']) && $_GET['limit'] == 300 ? 'selected' : ''; ?>>300</option>
                        <option value="500" <?php echo isset($_GET['limit']) && $_GET['limit'] == 500 ? 'selected' : ''; ?>>500</option>
                    </select>

                </div>
                <div class="d-flex">
                    <select name="type" id="type" class="form-control">
                        <option value="customer" <?= $_GET['type'] == "customer" ? "selected" : "" ?>>Customer</option>
                        <option value="lead" <?= $_GET['type'] == "lead" ? "selected" : "" ?>> Lead</option>
                    </select>
                    <select name="days" id="days" class="from-control mx-2">
                        <option value="">Select Days</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from `days` ");
                        // $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                        $company = $stmt->get_result();

                        while ($row = $company->fetch_assoc()) {
                            $sl = "";
                            if ($_GET['days'] == $row['days']) {
                                $sl = "selected";
                            }

                            echo '<option value="' . $row['days'] . '" ' . $sl . '>' . $row['days'] . ' Days</option>';
                        }

                        ?>
                    </select>


                    <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>
        <table class="table    mydataTable">
            <thead class="">
                <tr>
                    <th>#</th>

                    <th>Division</th>
                    <th>Company Name</th>
                    <th>Last Update</th>
                    <th>Mobile</th>
                    <th>Alternate Mobile</th>
                    <th>City</th>
                    <th> Name</th>




                    <th>Address</th>

                    <th>GST No.</th>

                    <th>DOB</th>
                    <th>DOA</th>
                    <th>User</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($cleanedJson as $key => $value) {
                    echo '<tr>
                                <td scope="row">' . $count++ . '</td>
                              
                                <td>' . $value['division'] . '</td>
                                <td>' . $value['company_name'] . '</td>
                                <td>' . $value['updated_at'] . '</td>
                                  <td>' . $value['mobile'] . '</td>
                                  <td>' . $value['alternate_number'] . '</td>
                                  <td>' . $value['city'] . '</td>
                             
                                <td>' . $value['name'] . '</td>
                              
                                
                              
                                <td>' . substr($value['address'], 0, 50) . '</td>
                      
                                <td>' . $value['gst'] . '</td>';

                    echo '<td>' . $value['dob'] . '</td>';

                    echo '<td>' . $value['doa'] . '</td>';
                    echo '<td>' . $value['user'] . '</td>';


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
                    for ($page = max(1, $currentPage - $range); $page <= min($totalPages, $currentPage + $range); $page++) {
                        echo ($page == $currentPage) ? '<strong class="badge bg-success p-2 m-2">' . $page . '</strong> ' : '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . '" class="badge bg-primary p-2 m-2">' . $page . '</a> ';
                    }

                    // Add ellipses if there are more pages
                    if ($currentPage - $range > 1) {
                        echo '... ';
                    }

                    // First page link
                    if ($currentPage - $range > 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . '"  class="badge bg-warning p-2 m-2">1</a> ';
                    }

                    // Last page link
                    if ($currentPage + $range < $totalPages - 1) {
                        echo '... <a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . '" class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
                    }
                }


                ?>



            </ul>
        </nav>
    </div>

</div>






<div class="modal fade" id="exampleModal">
    <div class="modal-dialog modal-lg">
        <form class=" needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Customer</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input id="id1" name="id" type="hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <label> User Type</label>
                            <select name="user_type" id="user_type" class="form-control" required>
                                <option value="Customer" selected>Customer</option>

                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="" class="">Select Company Name</label>
                            <select class="form-control " id="company_id" name="company_id" value="" required>
                                <option value="">---Select Company---</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from company order by company_name asc");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['company_name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Name</label>
                            <input type="text" name="name" id="name1" class="form-control" required>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Contact No.</label>
                            <input type="" name="mobile" id="mobile" class="form-control" onkeypress="if(this.value.length==10) return false" required>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Alternate No.</label>
                            <input type="" name="alternate_number" id="alternate_number" class="form-control" onkeypress="if(this.value.length==10) return false">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>DOB</label>
                            <input type="date" name="dob" id="dob" class="form-control">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>DOA</label>
                            <input type="date" name="doa" id="doa" class="form-control">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>GST</label>
                            <input type="text" name="gst" id="gst" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Drug License 1</label>
                            <input type="text" name="drug_license_1" id="drug_license_1" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Drug License 2</label>
                            <input type="text" name="drug_license_2" id="drug_license_2" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Food License </label>
                            <input type="text" name="food_license" id="food_license" class="form-control">

                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Password</label>
                            <input name="password" id="password" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Order Type</label>
                            <select name="order_type" id="order_type" class="form-control" required>
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="" class="">Mode of Transport</label>
                            <select class="form-control" id="mode_of_transport" name="mode_of_transport" value="">
                                <option value="">---Select---</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from mode_of_transport");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>


                        <div class="col-md-12 mt-3">
                            <label for="validationCustom01" class="form-label"> Division</label>
                            <select class="form-control" id="division" name="division[]" value="" required multiple>

                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `brand` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();

                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Delivery Address</label>
                            <textarea name="address" id="haddress" class="form-control"></textarea>

                        </div>


                        <div class="col-md-6 mt-3">
                            <label for="" class="form-label">Select State</label>
                            <select class="form-control state" id="hstate" name="state" value="">
                                <?php
                                $stmt = $mysqli->prepare("SELECT DISTINCT state from  state_district");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['state'] . '">' . $row['state'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="" class="form-label">Select City</label>
                            <select class="form-control" id="hcity" name="city[]" multiple value="">
                                <option value="">Select City</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Pin-code</label>
                            <input name="pincode" id="hpincode" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Country</label>
                            <input name="country" id="hcountry" class="form-control" value="India" required>

                        </div>

                        <hr>
                        <div class="col-md-12 mt-4">
                            <input type="checkbox" name="" id="same_address">
                            <label for="same_address">Same as Address</label>
                            <button class="btn btn-primary btn-sm float-end" onclick="addItem()" type="button">Add Address</button>

                        </div>
                        <div class="col-md-12">
                            <label>Delivery Address</label>
                            <textarea name="" id="address" class="form-control"></textarea>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="" class="form-label">Select State</label>
                            <select class="form-control state" id="state" name="" value="">
                                <?php
                                $stmt = $mysqli->prepare("SELECT DISTINCT state from  state_district");
                                $stmt->execute();
                                $state = $stmt->get_result();
                                while ($row = $state->fetch_assoc()) {
                                    echo '<option value="' . $row['state'] . '">' . $row['state'] . '</option>';
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="" class="form-label">Select City</label>
                            <select class="form-control" id="city" name="" value="">
                                <option value="">Select City</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Pin-code</label>
                            <input name="" id="pincode" class="form-control">

                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Country</label>
                            <input name="country" id="country" class="form-control" value="India" required>

                        </div>

                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Pincode</th>
                                        <th>Country</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="addressList">

                                </tbody>
                                <input type="hidden" id="address_LIST" name="address_LIST">
                            </table>

                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave" id="">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="POST">
    <div class="modal fade" id="Sale">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Allocate User
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="">Select </label>
                    <select name="user_id" id="user_id" class="form-control">

                    </select>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



<form method="POST" enctype="multipart/form-data">
    <div class="modal fade" id="ImportCustomer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Import Customer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <input type="file" class="form-control" name="file">

                        </div>
                        <div class="col-6">
                            <a class="btn btn-dark btn-sm float-end" href="import-customers.csv" download="import-customers.csv">Download Sample</a>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="btnImport" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>



<form method="POST" class="needs-validation" novalidate>
    <div class="modal fade"
        id="followUpModal">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Follow Up
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" id="followId" name="id">
                    <div class="col-md-6">
                        <label for="">Date</label>
                        <input type="date" name="date" class="form-control" required>

                    </div>
                    <div class="col-md-6">
                        <label>Order Value</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea name="remarks" id="" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnFollowUp" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form method="POST" class="needs-validation" novalidate>
    <div class="modal fade"
        id="GiftModal">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Annual Gift
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" id="gift_id" name="id">
                    <div class="col-md-12">
                        <label for="">Date</label>
                        <input type="date" name="date" class="form-control" required>

                    </div>

                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea name="remarks" id="" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnGift" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include("../layouts/footer.php"); ?>
<script>
    $(document).ready(function() {
        $("#hcity").select2();
    })
    $(document).on("click", ".edit_company", function() {
        $("#id1").val($(this).data("id"));
        $("#company_id").val($(this).data("company_id"));
        $("#name1").val($(this).data("name"));
        $("#mobile").val($(this).data("mobile"));
        $("#haddress").val($(this).data("address"));
        $("#gst").val($(this).data("gst"));
        $("#hstate").val($(this).data("state"));


        var str = $(this).data("city")

        if (str !== false && str !== 0) {
            var arr = str.toString().split(',').map(function(item) {
                return item.trim(); // Trim any extra whitespace
            });
        }

        var state = $(this).data("state");
        $("#hcity").html("<option value=" + str + " selected>" + str + "</option>")
        $("#hstate").append("<option value=" + state + " selected>" + state + "</option>")

        $("#hpincode").val($(this).data("pincode"));
        $("#hcountry").val($(this).data("country"));
        $("#password").val($(this).data("password"));
        $("#dob").val($(this).data("dob"));
        $("#doa").val($(this).data("doa"));
        $("#alternate_number").val($(this).data("alternate_number"));
        $("#food_license").val($(this).data("food_license"));
        $("#drug_license_1").val($(this).data("drug_license_1"));
        $("#drug_license_2").val($(this).data("drug_license_2"));
        $("#mode_of_transport").val($(this).data("mode_of_transport"));
        $("#order_type").val($(this).data("order_type"));
        var divisionValues = $(this).data("division").toString().split(', ');

        $("#division").val(divisionValues);
        $("#modal_name").text("Update Customer");
        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {

        $("#modal_name").text("Add Customer");
        $("#company_id").val('');
        $("#name1").val('');
        $("#mobile").val('');
        $("#address").val('');
        $("#gst").val('');
        $("#state").val("");
        $("#city").val("");
        $("#pincode").val("");
        $("#password").val("");

        $("#exampleModal").modal("show");
    })

    $(".state").on("change", function() {
        state = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {

                $("#city").html(data);

            }
        });
    });

    $("#hstate").on("change", function() {
        state = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {

                $("#hcity").html(data);

            }
        });
    });



    $("#same_address").on("click", function() {
        if ($(this).is(":checked")) {
            $("#address").val($("#haddress").val())
            $("#state").val($("#hstate").val())
            $("#city").html("<option value='" + $("#hcity").val() + "'>" + $("#hcity").val() + "</option>")
            $("#pincode").val($("#hpincode").val())
            $("#country").val($("#hcountry").val())
        }
    })


    let products = [];
    let id = 0;

    function addItem() {
        id++;
        const address = $('#address').val();
        const state = $('#state').val();
        const city = $('#city').val();
        const pincode = $('#pincode').val();
        const country = $('#country').val();




        const row = `
            <tr class="prod${id}">
                <td>${id}</td>
                <td>${address}</td>
                <td>${city}</td>
                <td>${state}</td>
               
                <td>${pincode}</td>
                <td>${country}</td>
              
                <td><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button">Delete</button></td>
            </tr> `;
        $('#addressList').append(row);

        products.push({
            id,
            address,
            state,
            city,
            pincode,
            country,
        });

        // Clear input fields
        $('#address').val('');
        $('#state').val('');
        $('#city').val('');
        $('#pincode').val('');



    }

    function removeItem(id) {
        $(`.prod${id}`).remove();
        products = products.filter(item => item.id !== id);
    }
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        $('#address_LIST').val(JSON.stringify(products));

                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    $(document).on("click", ".Sale", function() {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-sale-user.php',
            data: {
                id: $(this).data("id")
            },
            success: function(data) {

                data = JSON.parse(data);
                var html = "";
                data.forEach(element => {
                    html += `
                                <option value="${element.id}">${element.name}</option>
                            `;

                });

                $("#user_id").html(html);
                $("#Sale").modal("show");

            }
        });

    });

    $(document).on("click", ".followUp", function() {
        $("#followId").val($(this).val())
        $("#followUpModal").modal("show");
    })

    $(document).on("click", ".Gifts", function() {
        $("#gift_id").val($(this).val())
        $("#GiftModal").modal("show");
    })
</script>