<?php
include("../layouts/header.php");





if (isset($_POST['btnAdd'])) {



    try {

        $stmt = $mysqli->prepare("INSERT into email_templates (template_type,template_name,template,active,subject)values (?,?,?,?,?)");
        $stmt->bind_param("sssis", $_POST['template_type'],  $_POST['template_name'], $_POST['template'], $_POST['active'],$_POST['subject']);
        $stmt->execute();
        alert("Save Successfully", "success", "success");
        redirect("add-email-template");
    } catch (Exception $e) {
        alert($e->getMessage(), "error", "error");
        redirect("add-email-template");
    }
}


?>


<div class="card">

    <div class="card-body">
        <div class="m-2" style="display: flex; justify-content:space-between;">
            <div>
                <h4>Add Email Template</h4>
            </div>



        </div>
        <div class="row">
            <div class="col-md-12">
                <form class="row g-3 needs-validation" method="post" novalidate>
                    <div class="col-md-4 d-none">
                        <label for="validationCustom01" class="form-label">Template Type <span class="text-danger">* Template type can not be changed.</span></label>
                        <select type="text" class="form-control" name="template_type" value="Mark">
                            <option value="">Select Template</option>
                            <option value="Production">Production</option>
                            <option value="Packing">Packing</option>
                            <option value="Delivery">Delivery</option>
                            <option value="New Order">New Order</option>
                            <option value="Purchase Order">Purchase Order</option>
                        </select>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Template Name <span class="text-danger">* Template name can not be changed.</span></label>
                        <input type="text" class="form-control" id="" placeholder="Enter template name..." value="" name="template_name" required>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="" placeholder="Enter subject name..." value="" name="subject" required>

                    </div>
                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Active</label>
                        <select class="form-control" name="active" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                    </div>
                   

                    <div class="col-md-12">
                        <label for="validationCustom02" class="form-label">Template</label>
                        <textarea class="form-control" id="template" value="" name="template" required></textarea>

                    </div>

                    <div class="col-12 text-center">
                        <button class="btn btn-primary" type="submit" name="btnAdd">Add Template</button>
                    </div>
                </form>


            </div>

        </div>

    </div>
</div>




<?php include("../layouts/footer.php"); ?>


<script>
    var editor1 = new RichTextEditor("#template");
    //editor1.setHTMLCode("Use inline HTML or setHTMLCode to init the default content.");

    $(document).on("click", ".copytext", function() {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).text()).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.success("Copy text")
    })
</script>