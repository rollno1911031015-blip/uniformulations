<?php
include("../layouts/header.php");

$stmt = $mysqli->prepare("SELECT * from  email_templates where id=?;");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$email_templates = $stmt->get_result()->fetch_assoc();




if (isset($_POST['btnAdd'])) {


    try {

        $stmt = $mysqli->prepare("UPDATE   email_templates set template=?,active=?,subject=? where id=?");
        $stmt->bind_param("sisi",    $_POST['template'], $_POST['active'], $_POST['subject'],$_GET['id']);
        $stmt->execute();
        alert("Save Successfully", "success", "success");
        redirect("edit-email-template?id=" . $_GET['id'] . "");
    } catch (Exception $e) {
        alert($e->getMessage(), "error", "error");
        redirect("edit-email-template?id=" . $_GET['id'] . "");
    }
}


?>


<div class="card">

    <div class="card-body">
        <div class="m-2" style="display: flex; justify-content:space-between;">
            <div>
                <h4>Update Email Template</h4>
            </div>



        </div>
        <div class="row">
            <div class="col-md-12">
                <form class="row g-3 needs-validation" method="post" novalidate>
                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label">Template Type</label>
                        <select type="text" class="form-control" id="template_type" value="<?= $email_templates['template_type'] ?>" disabled>
                            <option value="">Select Template</option>
                            <option value="Production">Production</option>
                            <option value="Packing">Packing</option>
                            <option value="Delivery">Delivery</option>
                            <option value="New Order">New Order</option>
                        </select>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Template Name</label>
                        <input type="text" class="form-control"  value="<?= $email_templates['template_name'] ?>"  disabled>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="" placeholder="Enter subject name..." value="<?= $email_templates['subject'] ?>" name="subject" required>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Active</label>
                        <select class="form-control" name="active" id="active" value="<?= $email_templates['active'] ?>" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                    </div>
                    <div class="col-md-12 mt-4">

                        <?php

                        $stmt = $mysqli->prepare("SELECT * from  email_variables ;");
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $sno = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<span class="badge bg-primary  m-1 copytext" style="text-transform: lowercase;font-size:16px"> {{' . $row['variable'] . '}}</span>';
                        }
                        ?>

                    </div>


                    <div class="col-md-12">
                        <label for="validationCustom02" class="form-label">Template</label>
                        <textarea class="form-control" id="template" name="template" required><?= $email_templates['template'] ?></textarea>

                    </div>

                    <div class="col-12 text-center">
                        <button class="btn btn-primary" type="submit" name="btnAdd">Update Template</button>
                    </div>
                </form>


            </div>

        </div>

    </div>
</div>




<?php include("../layouts/footer.php"); ?>

<script>
    var editor1 = new RichTextEditor("#template");
    $("#template_type").val("<?= $email_templates['template_type'] ?>")
    $("#active").val("<?= $email_templates['active'] ?>")

    $(document).on("click", ".copytext", function() {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).text()).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.success("Copy text")
    })
</script>