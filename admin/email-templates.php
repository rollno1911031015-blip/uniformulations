<?php
include("../layouts/header.php");





if (isset($_POST['btnadd'])) {



    try {

        $stmt = $mysqli->prepare("INSERT into category (name,active)values (?,?)");
        $stmt->bind_param("si", $_POST['name'],  $_POST['active']);
        $stmt->execute();
        alert("Save Successfully", "success", "success");
        redirect("category");
    } catch (Exception $e) {
        alert($e->getMessage(), "error", "error");
        redirect("category");
    }
}


?>


<div class="card">

    <div class="card-body">
        <div class="m-2" style="display: flex; justify-content:space-between;">
            <div>
                <h4>Email Template </h4>
            </div>
            <div>
                <a type="button" href="add-email-template" class="btn btn-dark" id="">Add Template</a>


            </div>


        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table dataTable">
                    <thead>
                        <th>S.No</th>
                        <th>Template Name</th>
                        <th>Active</th>
                        <th>Action</th>

                    </thead>
                    <tbody>
                        <?php
                        $stmt = $mysqli->prepare("SELECT * from  email_templates ");
                         
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $sno = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo '<tr>
                                <td>' . $sno++ . '</td>
                                <td>' . $row['template_name'] . '</td>
                                <td>';
                            if ($row['active'] == 1) {
                                echo '<i class="badge bg-success">Active</i>';
                            } else {
                                echo '<i class="badge bg-danger">In Active</i>';
                            }

                            echo  '</td>
                                <td><a href="edit-email-template?id='.$row['id'].'" class="btn btn-primary btn-sm text-white"><i class="fa fa-pen"></i></a></td>
                            </tr>';
                        }

                        ?>
                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>




<?php include("../layouts/footer.php"); 
?>