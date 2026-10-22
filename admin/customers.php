<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {
    $ids = implode(', ', $_POST['division']);


    $city = "";

    if (!empty($_POST['city'])) {
        $city = implode(',', array_filter(array_map('trim', $_POST['city']), 'strlen'));
    }


    if (!empty($_POST['id'])) {
        $error = '';

        if (trim($_POST['name']) == '')
            $error = 'name is required.';

        if (trim($_POST['mobile']) == '')
            $error = 'Mobile no. is required.';

        if (!validate_phone($_POST['mobile']))
            $error = 'Invalid mobile number.';


        try {
            //code...



            if ($error == '') {

                $stmt = $mysqli->prepare("UPDATE lead SET company_id=?, user_type=?, mobile=?, address=?, gst=?, name=?,state=?,city=?,pincode=?,country=?,password=?,dob=?,doa=?,address=?,state=?,city=?,pincode=?,country=?,order_type=?,mode_of_transport=?,division=?,food_license=?,alternate_phone=?,drug_license_1=?,drug_license_2=?,rating=?,active=?,due_days=? where id=?;");
                $stmt->bind_param("issssssssssssssssssisssssiisi", $_POST['company_id'], $_POST['user_type'], $_POST['mobile'], $_POST['address'], $_POST['gst'], $_POST['name'], $_POST['state'], $city, $_POST['pincode'], $_POST['country'], $_POST['password'], $_POST['dob'], $_POST['doa'], $_POST['address'], $_POST['state'], $city, $_POST['pincode'], $_POST['country'], $_POST['order_type'], $_POST['mode_of_transport'], $ids, $_POST['food_license'], $_POST['alternate_number'], $_POST['drug_license_1'], $_POST['drug_license_2'], $_POST['rating'], $_POST['active'], $_POST['due_days'], $_POST['id']);
                $stmt->execute();
                $stmt->close();
                alert('Details updated successfully', 'success');
            } else {
                echo $error;
                alert($error, 'error', 'ERROR');
            }
        } catch (\Throwable $th) {
                    alert($th->getMessage(), "error");
        redirect("?limit=20&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
           
        }
    } else {
        $error = '';
        if (trim($_POST['name']) == '')
            $error = 'name is required.';

        if (trim($_POST['mobile']) == '')
            $error = 'Mobile no. is required.';

        if (!validate_phone($_POST['mobile']))
            $error = 'Invalid mobile number.';

        if ($error == '') {
            try {
                $status = 4;
                $stmt = $mysqli->prepare("INSERT into lead (company_id,user_type,mobile,gst,name,password,dob,doa,address,state,city,pincode,country,order_type,mode_of_transport,division,status,food_license,alternate_phone,drug_license_1,drug_license_2,rating,due_days) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
                $stmt->bind_param("isssssssssssssisissssis", $_POST['company_id'], $_POST['user_type'], $_POST['mobile'], $_POST['gst'], $_POST['name'],   $_POST['password'], $_POST['dob'], $_POST['doa'], $_POST['address'], $_POST['state'], $city, $_POST['pincode'], $_POST['country'], $_POST['order_type'], $_POST['mode_of_transport'], $ids, $status, $_POST['food_license'], $_POST['alternate_number'], $_POST['drug_license_1'], $_POST['drug_license_2'], $_POST['rating'], $_POST['due_days']);
                $stmt->execute();
                $mst_id = $mysqli->insert_id;
                $stmt->close();
                alert('Details saved successfully', 'success');
            } catch (Exception $ex) {
                if ($ex->getCode() == 1062) {
                    alert("Mobile Number is already exists.", 'error', 'ERROR');
                } else {
                    alert($ex->getMessage(), 'error', 'ERROR');

                    die;
                }
            }

            // Prepare to insert items
            $prod_list = json_decode($_POST['address_LIST'], true);

            foreach ($prod_list as $item) {
                $stmt = $mysqli->prepare("INSERT INTO lead_address (lead_id, address, state, city, pincode, country) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $mst_id, $item['address'], $item['state'], $item['city'], $item['pincode'], $item['country']);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            echo $error;
            alert($error, 'error', 'ERROR');
        }
    }
    redirect("customers");
}


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


if (!empty($_GET['rating'])) {
    $r_con = "and";
    $rating = $_GET['rating'];
} else {
    $r_con = "or";
    $rating = -1;
}

$totalRecordsQuery = $mysqli->prepare("SELECT COUNT(*) AS total FROM lead WHERE (name LIKE ? or mobile LIKE ? or alternate_phone LIKE ?)  and active=? AND division = ? AND status = 4 " . $r_con . " rating=? ");
$totalRecordsQuery->bind_param("ssssii", $search, $search, $search, $_GET['active'], $division, $rating); // Bind search parameter
$totalRecordsQuery->execute();
$totalRecordsResult = $totalRecordsQuery->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Determine the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Calculate the starting record for the current page
$startFrom = ($currentPage - 1) * $recordsPerPage;
foreach ($ids as $key => $value) {



    // $stmt1 = $mysqli->prepare("SELECT a.*, b.company_name as company_name,c.name as division_name FROM lead a
    //  left JOIN company b ON a.company_id = b.id left join  brand c on a.division=c.id WHERE
    //   (a.name like ? or a.mobile LIKE ? or a.alternate_phone LIKE ? or a.division LIKE ? or b.company_name LIKE ?) 
    //   and a.active=?  and FIND_IN_SET(?, a.division) AND a.status = 4 " . $r_con . " a.rating=? limit ?,?");
    // $stmt1->bind_param("sssssiiiii", $search, $search, $search, $search, $search, $_GET['active'], $value, $rating, $startFrom, $recordsPerPage);
    // $stmt1->execute();

    $stmt1 = $mysqli->prepare("SELECT a.*, b.company_name as company_name,c.name as division_name FROM lead a left JOIN company b ON a.company_id = b.id left join brand c on a.division=c.id WHERE (a.name like ? or a.mobile LIKE ? or a.alternate_phone LIKE ? or a.division LIKE ? or b.company_name LIKE ?) and a.active=? and FIND_IN_SET(?, a.division) AND a.status = 4 " . $r_con . " a.rating=? limit ?,?");
    $stmt1->bind_param("sssssiiiii", $search, $search, $search, $search, $search, $_GET['active'], $division, $rating, $startFrom, $recordsPerPage);
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


if (isset($_POST['btnImport'])) {
    $upload_dir = 'temp/';
    $file_url = "";
    if (isset($_FILES['file']['name'])) {
        $pic_name = $_FILES["file"]["name"];
        $pic_tmp_name = $_FILES["file"]["tmp_name"];
        $error = $_FILES["file"]["error"];
        if ($error <= 0) {
            $random_name = rand(1000, 1000000) . "-" . $pic_name;
            $upload_name = $upload_dir . strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);
            try {
                $res = move_uploaded_file($pic_tmp_name, $upload_name);
                $file_url = $upload_name;
            } catch (Exception $e) {
                alert($e->getMessage(), "error", "error");
            }
        }
    }

    if ($file_url !== '') {
        $file = $file_url;
        $handle = fopen($file, 'r');
        $i = 1;
        $success = 0;
        $duplicate = 0;
        $error = "";
        $count = 0;
        while (($filesop = fgetcsv($handle, 1000, ',')) !== false) {

            if ($i >= 1 && !empty($filesop[0])) {


                $company_name = $filesop[0];
                $name = $filesop[1];
                $mobile = $filesop[2];
                $order_type = $filesop[3];
                $division_name = $filesop[4];
                $address = $filesop[5];
                $state = $filesop[6];
                $city = $filesop[7];
                $pincode = $filesop[8];
                $drug_license_1 = $filesop[9];
                $drug_license_2 = $filesop[10];
                $food_license = $filesop[11];
                $alternate_number = $filesop[12];
                $gst = $filesop[13];


                $count++;

                // $city = "";
                // $district = "";

                // if (!empty($filesop[7])) {
                //     $city = explode('/', $filesop[7]);
                // }
                // foreach ($city as $key => $value) {
                //     $stmt = $mysqli->prepare("SELECT * from state_district where District=? limit 1");
                //     $stmt->bind_param('s', $value);
                //     $stmt->execute();
                //     $state_district = $stmt->get_result()->fetch_assoc();
                //     $stmt->close();
                //     $ar[] = $state_district['District'];
                //     $district = implode(",", $ar);
                // }




                $stmt = $mysqli->prepare("SELECT * from company where company_name=? limit 1");
                $stmt->bind_param('s', $company_name);
                $stmt->execute();
                $company = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $stmt = $mysqli->prepare("SELECT * from brand where name=? limit 1");
                $stmt->bind_param('s', $division_name);
                $stmt->execute();
                $division = $stmt->get_result()->fetch_assoc();
                $stmt->close();


                if ($company) {
                    if ($division) {




                        // $stmt = $mysqli->prepare("SELECT * from lead where mobile=?   limit 1");
                        // $stmt->bind_param('s', $mobile);
                        // $stmt->execute();
                        // $res = $stmt->get_result()->fetch_assoc();
                        // $stmt->close();
                        // if ($res) {
                        //     $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                        //     $duplicate++;
                        // } else

                        {

                            try {
                                $stmt = $mysqli->prepare("INSERT INTO lead (company_id,name,mobile,address,state,city,pincode,order_type,division,user_id,status,alternate_phone,drug_license_1,drug_license_2,food_license,gst,due_days) VALUES (?,?,?,?,?,?,?,?,?,?,4,?,?,?,?,?,?)");
                                $stmt->bind_param("isssssssiissssss",  $company['id'], $name, $mobile, $address, $state, $city, $pincode, $order_type, $division['id'], $user['id'], $alternate_number, $drug_license_1, $drug_license_2, $food_license, $gst, $due_days);
                                $stmt->execute();
                                $success++;

                                $error .= "Lead  row " . $i . " added successfully..<br>";
                            } catch (Exception $e) {
                                alert($e->getMessage(), "error", "error");
                                echo $e->getMessage();
                            }
                        }
                    } else {
                        $error .= 'Division not found.' . $i . ' <br>';
                    }
                } else {
                    $error .= 'Company Not Found.' . $i . ', ' . $company_name . ' <br>';
                }
                $i++;
            }
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        echo $error;
        fclose($handle);
    }
    // redirect('?');
}


if (isset($_POST['btnFollowUp'])) {

    $stmt = $mysqli->prepare("UPDATE lead set remarks=? where id=? ");
    $stmt->bind_param("si", $_POST['remarks'], $_POST['id']);
    $stmt->execute();

    $stmt = $mysqli->prepare("INSERT INTO lead_follow_up (lead_id,date,amount,remarks) VALUES (?,?,?,?)");
    $stmt->bind_param("isds", $_POST['id'], $_POST['date'], $_POST['amount'], $_POST['remarks']);
    $stmt->execute();
    alert("Save successfully", "success");
    redirect("?limit=20&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
}
if (isset($_POST['btnGift'])) {
    $stmt = $mysqli->prepare("INSERT INTO lead_gift (lead_id,date,remarks) VALUES (?,?,?)");
    $stmt->bind_param("iss", $_POST['id'], $_POST['date'], $_POST['remarks']);
    $stmt->execute();
    alert("Save successfully", "success");
    redirect("?limit=20&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
}

if (isset($_POST['btnSavePayment'])) {
    try {

        $stmt = $mysqli->prepare("INSERT into lead_pay_followup(lead_id,amount,pay_date,pay_mode,remarks,user_id) values (?,?,?,?,?,?) ;");
        $stmt->bind_param("idsssi", $_POST['id'], $_POST['amount'],  $_POST['pay_date'], $_POST['pay_mode'], $_POST['remarks'], $user["id"]);
        $stmt->execute();
        $stmt->close();

        alert("Save successfully.", "success", "success");
        redirect("?limit=20&rating=" . $_GET['rating'] . "&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
    } catch (\Throwable $th) {
        alert($th->getMessage(), 'error');
        redirect("?limit=20&rating=" . $_GET['rating'] . "&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
    }
}


$per = permissions(8, $mysqli, $user);

if (isset($_POST["btnGetProduct"])) {

    try {
        $stmt = $mysqli->prepare("UPDATE lead set cron_status=1 where id=? ");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        alert("Save successfully", "success");
        redirect("?limit=20&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
    } catch (\Throwable $th) {
        alert($th->getMessage(), "error");
        redirect("?limit=20&division=" . $_GET['division'] . "&search=" . $_GET['search'] . "");
    }
}
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
            <h4>Customer List</h4>
        </div>
        <div class="">

            <?php
            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company"><i class="fa fa-plus"></i> Add Customer</button>
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#ImportCustomer">
                    Import Customer
                </button>
            <?php
            }
            ?>


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
                    <select name="rating" id="rating" class="from-control mx-2">
                        <option value="">Select Rating</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from `rating` ");
                        // $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                        $company = $stmt->get_result();

                        while ($row = $company->fetch_assoc()) {
                            $sl = "";
                            if ($_GET['rating'] == $row['id']) {
                                $sl = "selected";
                            }
                            echo '<option value="' . $row['id'] . '" ' . $sl . '>' . $row['name'] . '</option>';
                        }

                        ?>
                    </select>
                    <select name="division" class="from-control">
                        <option value="">Select</option>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from `brand` ");
                        // $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                        $company = $stmt->get_result();

                        while ($row = $company->fetch_assoc()) {
                            $sl = "";
                            if ($_GET['division'] == $row['id']) {
                                $sl = "selected";
                            }

                            echo '<option value="' . $row['id'] . '" ' . $sl . '>' . $row['name'] . '</option>';
                        }

                        ?>
                    </select>
                    <select name="active" class="from-control">

                        <option value="1" <?= ($_GET['active'] ?? '') == 1 ? "selected" : "" ?>>Active</option>
                        <option value="0" <?= ($_GET['active'] ?? '') == 0 ? "selected" : "" ?>>IActive</option>



                    </select>
                    <input type="text" class="float-end form-control" value="<?php if (isset($_GET['search'])) echo $_GET['search'] ?>" name="search" id="search" placeholder="Search by keyword">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>
        <table class="table    mydataTable">
            <thead class="">
                <tr>
                    <th>#</th>

                    <th>Company Name</th>

                    <th>Mobile</th>
                    <th>Alternate Mobile</th>
                    <th>City</th>
                    <th> Name</th>




                    <th>Address</th>

                    <th>GST No.</th>

                    <th>DOB</th>
                    <th>DOA</th>
                    <th>Active</th>
                    <?php
                    if ($per['edit'] == 1) {
                    ?>
                        <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($cleanedJson as $key => $value) {
                    echo '<tr>
                                <td scope="row">' . $count++ . '</td>
                              
                                <td>' . $value['company_name'] . '</td>
                              
                                  <td>' . $value['mobile'] . '</td>
                                  <td>' . $value['alternate_phone'] . '</td>
                                  <td>' . $value['city'] . '</td>
                             
                                <td>' . $value['name'] . '</td>
                              
                                
                              
                                <td>' . substr($value['address'], 0, 50) . '</td>
                      
                                <td>' . $value['gst'] . '</td>';

                    echo '<td>' . $value['dob'] . '</td>';

                    echo '<td>' . $value['doa'] . '</td>';
                    if ($value['active'] == 1) {
                        echo '<td><span class="badge bg-success">Active</span> </td>';
                    } else {
                        echo '<td><span class="badge bg-danger">InActive</span> </td>';
                    }

                    if ($per['edit'] == 1) {


                        echo ' <td><button type="button" class="btn btn-primary  btn-sm edit_company" id="edit_company"  data-id="' . $value['id'] . '" data-company_id="' .
                            $value['company_id'] . '" 
                                        data-user_type="' . $value['user_type'] . '"
                                        data-name="' . $value['name'] . '"
                                        data-address="' . $value['address'] . '"
                                        data-gst="' . $value['gst'] . '"
                                        data-mobile="' . $value['mobile'] . '"
                                        data-state="' . $value['state'] . '"
                                        data-city="' . $value['city'] . '"
                                        data-pincode="' . $value['pincode'] . '"
                                        data-country="' . $value['country'] . '"
                                        data-password="' . $value['password'] . '"
                                        data-dob="' . $value['dob'] . '"
                                        data-doa="' . $value['doa'] . '"
                                        data-order_type="' . $value['order_type'] . '"
                                        data-mode_of_transport="' . $value['mode_of_transport'] . '"
                                        data-division="' . $value['division'] . '"
                                        data-alternate_number="' . $value['alternate_phone'] . '"
                                        data-food_license="' . $value['food_license'] . '"
                                        data-drug_license_1="' . $value['drug_license_1'] . '"
                                        data-drug_license_2="' . $value['drug_license_2'] . '"
                                        data-rating="' . $value['rating'] . '"
                                        data-active="' . $value['active'] . '"
                                        data-dueday="' . $value['due_days'] . '"
                                        
                                        ><i class="fa fa-pencil"></i></button>
                                        <a href="customer-products?id=' . $value['id'] . '" class="btn btn-primary btn-sm">Products</a>';

                                    if ($value["cron_status"]==0) {
                                     echo '    <button class="btn btn-dark btn-sm getProducts" type="button" value="' . $value['id'] . '" >Get Products</button>';
                                    }else{
                                        echo '<span class="badge bg-danger">Processing...</span>';
                                    }

                                      
                                     

                                       echo '<button class="btn btn-dark btn-sm followUp" type="button" value="' . $value['id'] . '" >Follow Up</button>

                                            <a href="follow-up-history?id=' . $value['id'] . '" class="btn btn-info btn-sm">Follow Up History</a>

                                               <button class="btn btn-success btn-sm Gifts" type="button" value="' . $value['id'] . '" >Annual Gift</button>
                                                  <a href="gift-report-customer-wise?customer_id=' . $value['id'] . '" class="btn btn-info btn-sm">Gift History</a>
                                                   <button class="btn btn-dark btn-sm payfollowUp" type="button" value="' . $value['id'] . '" >Payment Follow Up</button> 
                                                  <button class="btn btn-dark btn-sm paymentHistory" type="button" value="' . $value['id'] . '" >Payment History</button>
                                        </td>';
                    }
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
                        echo ($page == $currentPage) ? '<strong class="badge bg-success p-2 m-2">' . $page . '</strong> ' : '<a href="?page=' . $page . '&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . ' &active=' . $_GET['active'] . '" class="badge bg-primary p-2 m-2">' . $page . '</a> ';
                    }

                    // Add ellipses if there are more pages
                    if ($currentPage - $range > 1) {
                        echo '... ';
                    }

                    // First page link
                    if ($currentPage - $range > 2) {
                        echo '<a href="?page=1&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . '&active=' . $_GET['active'] . '"   class="badge bg-warning p-2 m-2">1</a> ';
                    }

                    // Last page link
                    if ($currentPage + $range < $totalPages - 1) {
                        echo '... <a href="?page=' . $totalPages . '&limit=' . $_GET['limit'] . '&division=' . $_GET['division'] . '&active=' . $_GET['active'] . '"  class="badge bg-secondary p-2 m-2">' . $totalPages . '</a>';
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
                        <div class="col-md-4 mt-3">
                            <label>Order Type</label>
                            <select name="order_type" id="order_type" class="form-control" required>
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>

                        </div>
                        <div class="col-md-4 mt-3">
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

                        <div class="col-md-4 mt-3">
                            <label>Due Days</label>
                            <input name="due_days" id="due_days" class="form-control" value="">
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
                        <div class="col-md-6 mt-3">
                            <label for="validationCustom01" class="form-label"> Rating</label>
                            <select class="form-control" id="rating" name="rating" value="">
                                <option value="">Select</option>
                                <?php
                                $stmt = $mysqli->prepare("SELECT * from `rating` ");
                                // $stmt->bind_param("i", $_POST['id']);
                                $stmt->execute();
                                $company = $stmt->get_result();

                                while ($row = $company->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }

                                ?>
                            </select>

                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="validationCustom01" class="form-label"> Active</label>
                            <select class="form-control" id="active" name="active" value="">

                                <option value="1">Active</option>
                                <option value="0">InActive</option>

                            </select>

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


<form method="post" class="needs-validation" novalidate>
    <div
        class="modal fade"
        id="payfollowUpModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"

        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Received Payment
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="payfollowId" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Pay Date</label>
                            <input type="date" name="pay_date" class="form-control" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Pay Mode</label>
                            <select name="pay_mode" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Net Banking">Net Banking</option>
                                <option value="Gpay">Gpay</option>

                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnSavePayment" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

</form>
<form method="POST">
    <div
        class="modal fade"
        id="getProductModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"

        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div
            class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Get Products
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="id" id="getProductID" hidden>
                    Get Products
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnGetProduct" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



<div
    class="modal fade"
    id="paymentHistoryModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"

    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Payment History
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="paymentHistoryTable">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>



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
        $("#due_days").val($(this).data("dueday"));


        var str = $(this).data("city");
        var selectedCities = [];

        if (str !== false && str !== 0 && str !== "") {
            selectedCities = str.toString().split(',').map(function(item) {
                return item.trim(); // Trim any extra whitespace
            }).filter(Boolean);
        }

        loadHomeCities($(this).data("state"), selectedCities);

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
        $("#rating").val($(this).data("rating"));
        $("#active").val($(this).data("active"));
        var divisionValues = $(this).data("division").toString().split(', ');

        $("#division").val(divisionValues);


        $("#modal_name").text("Update Customer");
        $("#exampleModal").modal("show");
    });
    $(".add-company").on("click", function() {
        $('#exampleModal input').val('');
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
        loadHomeCities($("#hstate").val(), []);

        $("#exampleModal").modal("show");
    })

    $("#state").on("change", function() {
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
        loadHomeCities($(this).val(), []);
    });

    // Fill the multi-select city list for a state and tick the given (saved) cities
    function loadHomeCities(state, selected) {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {
                $("#hcity").html(data);
                $("#hcity option[value='']").remove();
                // keep saved cities that are missing from the state's master list
                selected.forEach(function(city) {
                    if ($("#hcity option").filter(function() {
                            return this.value === city;
                        }).length === 0) {
                        $("#hcity").append($("<option>").val(city).text(city));
                    }
                });
                $("#hcity").val(selected).trigger("change");
            }
        });
    }



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
    });


    $(document).on("click", ".payfollowUp", function() {
        $("#payfollowId").val($(this).val())
        $("#payfollowUpModal").modal("show");
    })

    $(document).on("click", ".paymentHistory", function() {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-customer-payment-history.php',
            data: {
                id: $(this).val()
            },
            success: function(data) {
                data = JSON.parse(data)
                console.log(data);
                var html = "";
                var sno = 1;
                console.log(data);
                data.forEach(element => {
                    html += `<tr>
                            <td>${sno++}</td>
                            <td>${element.pay_date}</td>
                            <td>${element.amount}</td>
                            <td>${element.pay_mode}</td>
                            <td>${element.remarks}</td>
                        </tr>`;
                });
                $("#paymentHistoryTable").html(html)
                $("#paymentHistoryModal").modal("show");

            }
        });
    });

    $(document).on("click", ".getProducts", function() {
        $("#getProductID").val($(this).val())
        $("#getProductModal").modal("show")
    })
</script>