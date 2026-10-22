<?php
include("../layouts/header.php");

// Pin flag on leads (column is created on first load if missing)
$res = $mysqli->query("SHOW COLUMNS FROM lead LIKE 'is_pinned'");
if ($res->num_rows == 0) {
    $mysqli->query("ALTER TABLE lead ADD COLUMN is_pinned TINYINT(1) NOT NULL DEFAULT 0");
}

if (isset($_POST['toggle_pin'])) {
    $out = ['ok' => false];

    if (!empty($_POST['id'])) {
        $stmt = $mysqli->prepare("UPDATE lead SET is_pinned = 1 - is_pinned, updated_at = updated_at WHERE id=? AND user_id IN (" . $userIds . ")");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT is_pinned AS value FROM lead WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $out = ['ok' => true, 'value' => (int) $row['value']];
    }

    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

if (isset($_POST['BtnSave'])) {

    $city = "";
    
    if (empty($_POST['user_id'])) {
        alert("User ID Not Found", "error", "error");
        redirect("?");
    }

    if (!empty($_POST['city'])) {
        $city = implode(',', array_filter(array_map('trim', $_POST['city']), 'strlen'));
    }
    if (empty($_POST['date'])) {
        $_POST['date'] = "0000-00-00";
    }
    if (empty($_POST['remind_time'])) {
        $_POST['remind_time'] = "00:00:00";
    }
    if (empty($_POST['remind_date'])) {
        $_POST['remind_date'] = "0000-00-00";
    }

    if (!empty($_POST['id'])) {

        $stmt = $mysqli->prepare("UPDATE  lead set division=?,name=?,mobile=?,email=?,source=?,state=?,city=?,address=?,date=?,remarks=?,status=?,remind_time=?,remind_date=?,user_id=?,alternate_phone=?,amount=? where id=?");
        $stmt->bind_param("isssssssssissisdi", $_POST['division'], $_POST['name'], $_POST['mobile'], $_POST['email'], $_POST['source'], $_POST['state'], $city, $_POST['address'], $_POST['date'], $_POST['remarks'], $_POST['status'], $_POST['remind_time'], $_POST['remind_date'], $_POST['user_id'], $_POST['alternate_phone'], $_POST['amount'], $_POST['id']);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("INSERT into leads_comment (lead_id,user_id,status,remarks,remind_time,remind_date) values (?,?,?,?,?,?) ;");
        $stmt->bind_param("iiisss", $_POST['id'], $user['id'], $_POST['status'], $_POST['remarks'], $_POST['remind_time'], $_POST['remind_date']);
        $stmt->execute();

        $stmt->close();
    } else {
        try {
            $stmt = $mysqli->prepare("INSERT into lead (division,name,mobile,email,source,state,city,address,date,remarks,status,remind_time,remind_date,user_id,alternate_phone) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ;");
            $stmt->bind_param("isssssssssissis", $_POST['division'], $_POST['name'], $_POST['mobile'], $_POST['email'], $_POST['source'], $_POST['state'], $city, $_POST['address'], $_POST['date'], $_POST['remarks'], $_POST['status'], $_POST['remind_time'], $_POST['remind_date'], $_POST['user_id'], $_POST['alternate_phone']);
            $stmt->execute();
            $lead_id = $mysqli->insert_id;
            $stmt->close();


            $stmt = $mysqli->prepare("INSERT into leads_comment (lead_id,user_id,status,remarks,remind_time,remind_date) values (?,?,?,?,?,?) ;");
            $stmt->bind_param("iiisss", $lead_id, $user['id'], $_POST['status'], $_POST['remarks'], $_POST['remind_time'], $_POST['remind_date']);
            $stmt->execute();

            $stmt->close();
        } catch (\Throwable $th) {
            echo $th->getMessage();
            die;
        }
    }
    alert("Save successfully.", "success", "success");
    redirect("?status=" . $_GET['status'] . "&user_id=" . $_GET['user_id']);
}


if (isset($_POST['btnDelete'])) {

    try {
        $stmt = $mysqli->prepare("DELETE from lead where id=?   ;");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();

        $stmt->close();
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }


    alert("Save successfully.", "success", "success");
    redirect("?status=" . $_GET['status'] . "&user_id=" . $_GET['user_id']);
}


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

            if ($i >= 1 && !empty($filesop[1])) {

                $name = $filesop[0];
                $number = $filesop[1];
                $alternate_phone = $filesop[2];
                $address = $filesop[3];
                $state = $filesop[4];

                $division_name = $filesop[6];
                $status = $filesop[7];
                $remarks = $filesop[8];
                $user_id = $filesop[9];
                $source = $filesop[10];




                $count++;

                $city = "";
                $district = "";

                if (!empty($filesop[5])) {
                    $city = explode('/', $filesop[5]);
                }
                foreach ($city as $key => $value) {
                    $stmt = $mysqli->prepare("SELECT * from state_district where District=? limit 1");
                    $stmt->bind_param('s', $value);
                    $stmt->execute();
                    $state_district = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    $ar[] = $state_district['District'];
                    $district = implode(",", $ar);
                }


                $stmt = $mysqli->prepare("SELECT * from status where name=? limit 1");
                $stmt->bind_param('s', $status);
                $stmt->execute();
                $status1 = $stmt->get_result()->fetch_assoc();
                $stmt->close();


                $stmt = $mysqli->prepare("SELECT * from brand where name=? limit 1");
                $stmt->bind_param('s', $division_name);
                $stmt->execute();
                $division = $stmt->get_result()->fetch_assoc();
                $stmt->close();


                // $stmt = $mysqli->prepare("SELECT * from lead where mobile=? limit 1");
                // $stmt->bind_param('s', $number);
                // $stmt->execute();
                // $res = $stmt->get_result()->fetch_assoc();
                // $stmt->close();
                // if ($res && $status) {
                //     $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                //     $duplicate++;
                // } else 

                {
                    try {
                        $stmt = $mysqli->prepare("INSERT INTO lead (name,mobile,status,user_id,alternate_phone,address,state,city,division,remarks,source) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                        $stmt->bind_param("ssiisssssss",   $name, $number, $status1['id'], $user_id, $alternate_phone, $address, $state, $district, $division['id'], $remarks, $source);
                        $stmt->execute();
                        $mst_id = $mysqli->insert_id;

                        $stmt = $mysqli->prepare("INSERT INTO  leads_comment (lead_id,user_id,status,remarks) VALUES (?,?,?,?)");
                        $stmt->bind_param("iiis",    $mst_id, $user_id, $status1['id'], $remarks);
                        $stmt->execute();

                        $success++;
                        $error .= "Lead  row " . $i . " added successfully..<br>";
                        alert("Save Successfully", "success", "success");
                    } catch (Exception $e) {
                        alert($e->getMessage(), "error", "error");
                    }
                }
            } else {
                $error .= 'Invalid format <br>';
            }
            $i++;
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        alert($msg, 'info');

        fclose($handle);
    }
    redirect('?');
}
$per = permissions(7, $mysqli, $user);

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
    <div class="card-header">
        <div class="page-title">
            <h4>Lead </h4>
        </div>
        <div class="">
            <form method="GET" id="statusForm">
                <div class="row">
                    <div class="col-2 mt-3">

                        <select name="status" id="gstatus" class="form-control">
                            <option value="0">All lead</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from status");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();
                            $no = 1;
                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-2 mt-3">

                        <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                            <option value="0">--All User-- </option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from admin_users where id in (" . $userIds . ")");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();
                            $no = 1;
                            while ($row = $company->fetch_assoc()) {
                                $selected = "";
                                if (($_GET['user_id']) == $row['id']) {
                                    $selected = "selected";
                                }
                                echo '<option value="' . $row['id'] . '" ' . $selected . ' >' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-2 mt-3">

                        <select name="view" id="view" class="form-control" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="pinned" <?= ($_GET['view'] ?? '') == 'pinned' ? 'selected' : '' ?>>Pinned</option>
                        </select>

                    </div>
                </div>
            </form>

            <?php

            if ($per['edit'] == 1) {
            ?>
                <button type="button" class="btn btn-primary add-company float-end"><i class="fa fa-plus"></i> Add Lead</button>
                <button type="button" class="btn btn-dark  float-end mx-2 bulk_import"><i class="fa fa-download"></i> Bulk Import</button>

            <?php
            }
            ?>




        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table lead-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pin</th>
                        <th>Division</th>
                        <th>Name</th>
                        <th>mobile</th>
                        <th>Alternate</th>

                        <th>Source</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>

                        <th>Status</th>
                        <th>Remind Date</th>
                        <th>Remind Time</th>

                        <th>Amount</th>
                        <th>User</th>
                        <th>Created at</th>
                        <th>Updated at</th>

                        <?php

                        if ($per['edit'] == 1) {
                        ?>
                            <th>Action</th>
                        <?php } ?>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $where = "1=1";
                    $type = "";
                    $ar = array();

                    if (!empty($_GET['status'])) {
                        $where .= ' and a.status = ?';
                        $type .= 'i';
                        array_push($ar, $_GET['status']);
                    }

                    if (!empty($_GET['user_id'])) {
                        $where .= ' and a.user_id = ?';
                        $type .= 'i';
                        array_push($ar, $_GET['user_id']);
                    }

                    $view = $_GET['view'] ?? '';
                    $orderBy = "a.is_pinned DESC, a.id DESC";

                    if ($view == 'pinned') {
                        $where .= " and a.is_pinned = 1";
                    }

                    try {
                        $stmt = $mysqli->prepare("SELECT a.*,b.name as division_name,c.name as user_name,d.name as status_name from lead a left join brand b on a.division=b.id  join admin_users c on a.user_id=c.id join status d on a.status=d.id where $where    and  user_id in (" . $userIds . ") order by $orderBy");
                        if ($type) {
                            $stmt->bind_param($type, ...$ar);
                        }
                        $stmt->execute();
                    } catch (\Throwable $th) {
                        echo $th->getMessage();
                    }

                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        $pinClass = $row['is_pinned'] ? 'text-primary' : 'text-muted';
                        $pinButton = '<button type="button" class="btn btn-link p-0 toggle-pin" data-id="' . $row['id'] . '" title="Pin to top (follow-up)"><i class="fa fa-thumbtack ' . $pinClass . '"></i></button>';

                        echo '<tr' . ($row['is_pinned'] ? ' class="table-primary"' : '') . '>
                                        <td>' . $row['id'] . '</td>
                                        <td data-order="' . (int) $row['is_pinned'] . '">' . $pinButton . '</td>
                                        <td>' . $row['division_name'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['mobile'] . '</td>
                                        <td>' . $row['alternate_phone'] . '</td>
                                      
                                        <td>' . $row['source'] . '</td>
                                        <td>' . $row['state'] . '</td>
                                        <td>' . $row['city'] . '</td>
                                        <td>' . $row['address'] . '</td>
                                     
                                        <td>' . $row['status_name'] . '</td>
                                          <td>' . $row['remind_date'] . '</td>
                                        <td>' . $row['remind_time'] . '</td>
                                      
                                        <td>' . $row['amount'] . '</td>
                                        <td>' . $row['user_name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        <td>' . $row['updated_at'] . '</td>
                                        ';


                        if ($per['edit'] == 1) {



                            echo  '<td>
                                        <button type="button" class="btn btn-primary btn-sm edit_company" id="edit_company"  data-id="' . $row['id'] . '" ><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-success btn-sm GetComment"   data-id="' . $row['id'] . '" ><i class="fa fa-eye"></i></button>';

                            if ($user['user_type'] == "admin") {
                                echo '<button class="btn btn-danger btn-sm mx-2 delete" type="button" value="' . $row['id'] . '"> <i class="fa fa-trash" aria-hidden="true"></i> </button>';
                            }
                            echo  '</td>';
                        }
                        echo  '</tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </div>

</div>



<div class="modal fade" id="exampleModal">
    <div class="modal-dialog modal-lg">
        <form class="row g-3 needs-validation" novalidate method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Lead</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <input id="id" name="id" type="hidden">
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label"> Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="">

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label"> mobile</label>
                        <input type="text" class="form-control" id="mobile" list="datalistOptions" name="mobile" onkeypress="if(this.value.length==10) return false" value="" required>
                        <datalist id="datalistOptions">
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `lead` where status!=4");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['mobile'] . '">';
                            }
                            ?>



                        </datalist>


                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label">Alternate mobile</label>
                        <input type="text" class="form-control" id="alternate_phone" name="alternate_phone" value="">

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="">

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Division</label>
                        <select class="form-control" id="division" name="division" value="">
                            <option value="">--Select--</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `brand` order by id desc");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Source</label>
                        <select class="form-control" id="source" name="source" value="">
                            <option value="">--Select--</option>
                            <?php

                            $stmt = $mysqli->prepare("SELECT * from `source` order by id desc");
                            // $stmt->bind_param("i", $_POST['id']);
                            $stmt->execute();
                            $company = $stmt->get_result();

                            while ($row = $company->fetch_assoc()) {
                                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select State</label>
                        <select class="form-control state" id="state" name="state" value=""  >
                            <option value="">Select</option>
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
                    <div class="col-md-4 mt-3 ">
                        <label for="" class="form-label">Select City</label>
                        <select class="form-control city" id="city" multiple name="city[]" value="">
                            <option value="">Select City</option>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select User</label>
                        <select class="form-control" id="user_id" name="user_id" value="">
                            <option value="">Select</option>
                            <?php
                            $stmt = $mysqli->prepare("SELECT * from admin_users where id in (" . $userIds . ")");
                            $stmt->execute();
                            $state = $stmt->get_result();
                            while ($row = $state->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-4 mt-3 d-none">
                        <label for="" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="">


                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" value=""></textarea>


                    </div>



                    <div class="col-md-4 mt-3">
                        <label for="" class="form-label">Select Status</label>
                        <select class="form-control state" id="status" name="status" value="">

                            <?php
                            $stmt = $mysqli->prepare("SELECT * from status");
                            $stmt->execute();
                            $state = $stmt->get_result();
                            while ($row = $state->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="validationCustom01" class="form-label"> Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="">

                    </div>
                    <div class="col-md-4 mt-3 remind">
                        <label for="validationCustom01" class="form-label"> Remind Date</label>
                        <input type="date" class="form-control" id="remind_date" name="remind_date" value="">

                    </div>
                    <div class="col-md-4 mt-3 remind">
                        <label for="validationCustom01" class="form-label"> Remind Time</label>
                        <input type="time" class="form-control" id="remind_time" name="remind_time" value="">

                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" value=""></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="BtnSave" id="btn_save">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<div
    class="modal fade"
    id="commentModal">
    <div
        class="modal-dialog modal-xl "
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Comments
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>

                            <th>User</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Remind Time</th>
                            <th>Remind Date</th>
                            <th>Created at</th>
                        </tr>
                    </thead>
                    <tbody id="CommentList">

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



<form method="POST" enctype="multipart/form-data">
    <div class="modal fade" id="BulkImportModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Bulk Import
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="file" class="form-control" name="file">

                        </div>
                        <div class="col-md-6">
                            <a class="btn btn-dark btn-sm float-end" href="import-lead-sample.csv" download="import-lead-sample.csv"><i class="fa fa-download" aria-hidden="true"></i> Download Sample File</a>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnImport" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>


<form method="POST">
    <div
        class="modal fade"
        id="deleteModalId"
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
                        Delete Lead
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteId" name="id">
                    <h6>Are you sure you want to delete lead?</h6>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="btnDelete" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



<?php include("../layouts/footer.php"); ?>
<script>
    // order: [] keeps the server order (pinned leads first)
    $(".lead-table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": true,
        "order": [],
        "buttons": ["excel", 'csv'],
    }).buttons().container().appendTo('.col-md-6:eq()');

    $(document).on("click", ".toggle-pin", function() {
        $.post("", {
            toggle_pin: 1,
            id: $(this).data("id")
        }, function(res) {
            if (res.ok) {
                // reload so the pinned lead moves to the top
                location.reload();
            }
        }, "json");
    });

    $("#city").select2()
    $("#state").on("change", function() {
        loadCities($(this).val(), []);
    });

    // Fill the multi-select city list for a state and tick the given (saved) cities
    function loadCities(state, selected) {
        $.ajax({
            method: 'POST',
            url: 'ajax/get-city.php',
            data: {
                state: state
            },
            success: function(data) {
                $("#city").html(data);
                $("#city option[value='']").remove();
                // keep saved cities that are missing from the state's master list
                selected.forEach(function(city) {
                    if ($("#city option").filter(function() {
                            return this.value === city;
                        }).length === 0) {
                        $("#city").append($("<option>").val(city).text(city));
                    }
                });
                $("#city").val(selected).trigger("change");
            }
        });
    }

    $(document).on("click", ".edit_company", function() {
        var id = $(this).data("id");
        $("#id").val(id);

        $.ajax({
            method: 'POST',
            url: 'ajax/get-lead.php',
            data: {
                id: id
            },
            success: function(data) {

                data = JSON.parse(data);
                $.each(data, function(i, o) {
                    $('input[name=' + i + ']').val(o);
                    $('select[name=' + i + ']').val(o);
                    $('textarea[name=' + i + ']').val(o);
                })
                var savedCities = [];
                if (data.city) {
                    savedCities = data.city.toString().split(',').map(function(item) {
                        return item.trim();
                    }).filter(Boolean);
                }
                loadCities(data.state, savedCities);
                $("#modal_name").text("Update Lead");
                $("#exampleModal").modal("show");

            }
        });



    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Lead");
        $("#category").val("");
        $('input').val("");
        $('select').val("");
        $('textarea').val("");
        $("#city").empty().trigger("change");

        $("#exampleModal").modal("show");
    });
    $(document).on("click", ".GetComment", function() {
        var id = $(this).data("id");
        $("#id").val(id);

        $.ajax({
            method: 'POST',
            url: 'ajax/get-comments.php',
            data: {
                id: id
            },
            success: function(data) {
                var html = "";
                data = JSON.parse(data)
                var sno = 1;
                data.forEach(element => {
                    html += `<tr>
                        <td>${sno++}</td>
                       
                        <td>${element.user}</td>
                        <td>${element.remarks}</td>
                        <td>${element.status}</td>
                        <td>${element.remind_date}</td>
                        <td>${element.remind_time}</td>
                        <td>${element.created_at}</td>
                        </tr>`;
                });

                $("#CommentList").html(html)

                $("#commentModal").modal("show");

            }
        });
    });
    $("#gstatus").val("<?= $_GET['status'] ?>")
    $("#gstatus").on("change", function() {
        $("#statusForm").submit();
    });
    $(".bulk_import").on("click", function() {
        $("#BulkImportModal").modal("show")

    })
    $(".remind").hide();
    $("#status").on("change", function() {
        if ($(this).val() == 3 || $(this).val() == 2) {
            $(".remind").show(500);
        } else {
            $(".remind").hide(500);
        }
    });

    $(document).on("click", ".delete", function() {
        var id = $(this).val();
        $("#deleteId").val($(this).val())

        $("#deleteModalId").modal("show")

    });
</script>