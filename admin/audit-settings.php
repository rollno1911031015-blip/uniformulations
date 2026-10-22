<?php
include("../layouts/header.php");

if (isset($_POST['BtnSave'])) {


    $stmt = $mysqli->prepare("SELECT count(*) as p_count from   products ");
    //$stmt->bind_param("s", $_POST['audit_count']);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($products['p_count'] < $_POST['audit_count']) {
        alert("Audit count can not be more then products.", "error", "error");
        redirect("?");
    }


    if (empty($_POST['audit_count'])) {
        alert("Enter audit count.", "error", "error");
        redirect("?");
    } else {
        if(!empty($_POST['product_ids'])){
            $ids = "";
            $ids = implode(', ', $_POST['product_ids']);

        }else{
            $_POST['product_ids']="";
        }

 
    

        $stmt = $mysqli->prepare("UPDATE   audit_settings set audit_count=?,type=?,product_ids=? where id=1");
        $stmt->bind_param("sss", $_POST['audit_count'], $_POST['type'], $ids);
        $stmt->execute();
        $stmt->close();
        alert('Save successfully', 'success');
        redirect("?");
    }
}

$stmt = $mysqli->prepare("SELECT * from   audit_settings  where id=1");
//$stmt->bind_param("s", $_POST['audit_count']);
$stmt->execute();
$audit_setting = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Audit</h4>
        </div>
        <div class="">





        </div>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-2">
                    <label for="">Audit Count</label>
                    <input type="number" class="form-control" name="audit_count" id="audit_count" value="<?= $audit_setting['audit_count'] ?>">
                </div>
                <div class="col-md-3">
                    <label for="">Select Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="automatic">Automatic</option>
                        <option value="manual">Manual</option>

                    </select>

                </div>
                <div class="col-md-2 mt-4">
                    <button class="btn btn-primary" name="BtnSave">Save</button>

                </div>

                <table class="table mt-5 " id="product_table">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th><input type="checkbox" id="all_check"> </th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Color</th>


                            <th>Price</th>



                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $stmt = $mysqli->prepare("SELECT * from products order by id desc");
                        // $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                        $company = $stmt->get_result();
                        $no = 1;
                        while ($row = $company->fetch_assoc()) {
                            echo '<tr>
                                        <td>' . $no++ . '</td>
                                        <td><input type="checkbox" name="product_ids[]"  value="' . $row['id'] . '" class="product_ids"></td>
                                        <td>' . $row['category'] . '</td>
                                        <td>' . $row['sub_category'] . '</td>
                                        <td>' . $row['name'] . '</td>
                                        <td>' . $row['product_code'] . '</td>
                                        <td>' . $row['color'] . '</td>
                                       
 
                                        <td>' . $row['price'] . '</td>
                                
                                        
                                        
                                        
                                    </tr>';
                        }
                        ?>

                    </tbody>
                </table>

            </div>
        </form>

    </div>

</div>










<?php include("../layouts/footer.php"); ?>

<script>
    $("#type").val("<?= $audit_setting['type'] ?>");

    if ($("#type").val() == "manual") {
        $("#product_table").show()
    } else {
        $("#product_table").hide()
    }

    $("#type").on("change", function() {
        if ($(this).val() == "manual") {
            $("#product_table").show(500)
        } else {
            $("#product_table").hide(500)
        }

    });
    $("#all_check").on("click", function() {
        if ($(this).prop("checked")) {
            $(".product_ids").prop("checked", true);
        } else {
            $(".product_ids").prop("checked", false);
        }
    });
 
</script>