<?php
include("../layouts/header.php");

 
 
 
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
                </div>
            </form>

        



        </div>
    </div>
    <div class="card-body">
        <div class="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Division</th>
                        <th>Name</th>
                        <th>mobile</th>

                        <th>Source</th>
                     

                        <th>Status</th>
                        <th>Remind Date</th>
                        <th>Remind Time</th>
                        <th>Remarks</th>

                        <th>User</th>
                        <th>Created at</th>

                     

                    </tr>
                </thead>
                <tbody>
                    <?php

                    $where = "1=1";
                    $type = "";
                    $ar = array();

                    

                    if (!empty($_GET['user_id'])) {
                        $where .= ' and a.user_id = ?';
                        $type .= 'i';
                        array_push($ar, $_GET['user_id']);
                    }

                    try {
                        $stmt = $mysqli->prepare("SELECT a.*,b.name as division_name,c.name as user_name,d.name as status_name from lead a left join brand b on a.division=b.id  join admin_users c on a.user_id=c.id join status d on a.status=d.id where a.user_id=? and date(a.updated_at)=date(now())  order by a.id desc");
                        $stmt->bind_param("i", $_GET['user_id']);
                        $stmt->execute();
                    } catch (\Throwable $th) {
                        echo $th->getMessage();
                    }

                    $company = $stmt->get_result();
                    $no = 1;
                    while ($row = $company->fetch_assoc()) {
                        echo '<tr>
                                        <td>' . $row['id'] . '</td>
                                        <td>' . $row['division_name'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['mobile'] . '</td>
                                      
                                        <td>' . $row['source'] . '</td>
                                    
                                     
                                        <td>' . $row['status_name'] . '</td>
                                          <td>' . $row['remind_date'] . '</td>
                                        <td>' . $row['remind_time'] . '</td>
                                        <td>' . $row['remarks'] . '</td>
                                      
                                        <td>' . $row['user_name'] . '</td>
                                        <td>' . $row['created_at'] . '</td>
                                        ';


                       
                        echo  '</tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </div>

</div>




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
    $("#city").select2()
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
                    if (i == "city") {
                        $("#city").html("<option value=" + o + ">" + o + "</option>")
                    }
                })
                $("#modal_name").text("Update Lead");
                $("#exampleModal").modal("show");

            }
        });



    });
    $(".add-company").on("click", function() {
        $("#modal_name").text("Add Lead");
        $("#category").val("");

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