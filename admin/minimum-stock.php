<?php
include("../layouts/header.php");

if (isset($_GET['category']) && isset($_GET['subcategory'])) {
    $stmt = $mysqli->prepare("SELECT a.*,b.name as loc,b.address as addres,p.name as pro_name,a.current_stock as quantity,p.uom from  current_stock a JOIN locations b on a.loc_id=b.id JOIN products p on a.prod_id=p.id  where a.current_stock<=10 and p.category=? and p.sub_category=? order by created_date desc");
    $stmt->bind_param('ss', $_GET['category'], $_GET['subcategory']);
    $stmt->execute();
    $current_stock = $stmt->get_result();
} else {
    $stmt = $mysqli->prepare("SELECT a.*,b.name as loc,b.address as addres,p.name as pro_name,a.current_stock as quantity,p.uom from  current_stock a JOIN locations b on a.loc_id=b.id JOIN products p on a.prod_id=p.id  where a.current_stock<=10 order by created_date desc");
    //  $stmt->bind_param('i', $_GET['product_id']);
    $stmt->execute();
    $current_stock = $stmt->get_result();
}
?>

 
            <div class="row">
                <div class="card col-lg-12">
                    <div class="card-header">
                        <h4 class=" ">Minimum Stock</h4>
                        <form>
                            <div class="row">


                                <div class="col-md-3">
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <?php
                                        $stmt = $mysqli->prepare("SELECT * from categories");
                                        //$stmt->bind_param('i', $_GET['loc_id']);
                                        $stmt->execute();
                                        $categories = $stmt->get_result();
                                        while ($row = $categories->fetch_assoc()) {
                                            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                        }
                                        ?>



                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="subcategory" name="subcategory" class="form-control">
                                        <option></option>
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary" name="" id="">Search</button>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body table-responsive">
                        <!-- <div class="form-group col-md-12">
                            <table class="table">
                                <form method="get">
                                    <tr>
                                        <td style="width: 15rem;">
                                            <select name="loc_id" id="loc_id" class="form-control"  >
                                                <option value="">--Select Locations--</option>
                                                <?php
                                                $stmt = $mysqli->prepare("SELECT * from  locations  ;");
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_assoc()) {
                                                    echo '<option value="' . $row['id'] . '" data-loc="' . $row['name'] . ' ' . $row['address'] . '">' . $row['name'] . ' ' . $row['address'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" name="getrecord" class="btn btn-success">Search</button>
                                        </td>


                                        <td style="width: 15rem;">
                                            <select name="product_id" id="product_id" class="form-control"  >
                                                <option value="">--Select product--</option>
                                                <?php
                                                $stmt = $mysqli->prepare("SELECT * from  products ;");
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_assoc()) {
                                                    echo '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" name="product_btn" class="btn btn-success">Search</button>
                                        </td>
                                    </tr>
                                </form>
                            </table>

                        </div> -->

                        <table class="table table-hover datatable ">
                            <thead class="bg-ligth text-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Location</th>

                                 

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($current_stock)) {


                                    $count = 1;
                                    while ($row = $current_stock->fetch_assoc()) {
                                        echo '<tr>
                                  <td scope="row">' . $count++ . '</td>';
                                        echo '<td>' . $row['pro_name'] . '</td>
                                 <td>' . $row['quantity'] . '</td>
                                 <td>' . $row['uom'] . '</td>
                                    <td>' . $row['loc'] . ' ' . $row['addres'] . '</td>
                              
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
include("../layouts/footer.php");
?>

<script>
    $(document).ready(function() {
        $('#customersTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
    $("#category").on("change", function() {
        let name = $(this).val();
        $.ajax({
            method: 'POST',
            url: 'ajax/get-subcategory.php',
            data: {
                name: name
            },
            success: function(data) {

                $("#subcategory").html(data);

            }
        });
    });
    let subcate = "<?php if (isset($_GET['subcategory'])) echo $_GET['subcategory'] ?>";

    $("#category").val("<?php if (isset($_GET['category'])) echo $_GET['category'] ?>")
    $("#subcategory").html("<option value=" + subcate + ">" + subcate + "</option>");
</script>