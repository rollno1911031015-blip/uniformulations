<?php
require '../layouts/header.php';
if (isset($_POST['btnSubmit'])) {
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

            if ($i >= 1 && !empty($filesop[3])) {


                $company = $filesop[0];
                $brand = $filesop[1];
                $category = $filesop[2];
                $product_name = $filesop[3];
                $composition = $filesop[4];
                $uom = $filesop[5];
                $packing = $filesop[6];
                $mrp = $filesop[7];
                $sale_rate = $filesop[8];
                $cgst = $filesop[9];
                $sgst = $filesop[10];
                $igst = $filesop[11];
                $min_stock = $filesop[12];
                $hsn_code = $filesop[13];

                $count++;




                $stmt = $mysqli->prepare("SELECT * from brand where name=? limit 1");
                $stmt->bind_param('s', $brand);
                $stmt->execute();
                $brands = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $stmt = $mysqli->prepare("SELECT * from categories where name=? limit 1");
                $stmt->bind_param('s', $category);
                $stmt->execute();
                $categories = $stmt->get_result()->fetch_assoc();
                $stmt->close();
 

             
                if ($brands) {
                    // if($categories){

                 


                    $stmt = $mysqli->prepare("SELECT * from products where name=?  and brand=? limit 1");
                    $stmt->bind_param('ss', $product_name, $brand_name);
                    $stmt->execute();
                    $res = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    if ($res) {
                        $error .= 'Duplicate lead Found (row-' . $i . ').Above this row ' . $success . ' leads are uploaded<br>';
                        $duplicate++;
                    } else {
                        $product_code=rand(11111111,99999999);
                        try {
                            $stmt = $mysqli->prepare("INSERT INTO products (company,brand,category,name,composition,uom,packing,price,sale_rete,cgst,sgst,igst,min_stock,hsn_code) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                            $stmt->bind_param("sssssssdddddis",  $company,$brands['name'], $categories['name'], $product_name, $composition, $uom,$packing,$mrp,$sale_rate,$cgst,$sgst,$igst,$min_stock,$hsn_code);
                            $stmt->execute();
                            $success++;

                            $error .= "Lead  row " . $i . " added successfully..<br>";
                        } catch (Exception $e) {
                            alert($e->getMessage(), "error", "error");
                            echo $e->getMessage();
                            die;
                        }
                    }
                // }else{
                //     $error .= 'Category not found '.$category.'<br>';

                // }
                } else {
                    $error .= 'Division not found '.$brand.'<br>';
                }
                $i++;
            }
        }
        $msg = 'Total : ' . $count . ' Success: ' . $success . ' Duplicate : ' . $duplicate . ' Failed: ' . ($count - $success - $duplicate);
        alert($msg, 'info');
        echo $error;
        fclose($handle);
    }
   // redirect('products.php');
}
?>

<div class="content-wrap">

    <main id="content" class=" " role="main">
        <div class="container">
            <div class="row" id="profile">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="display: flex; justify-content: space-between;">
                            <div>Bulk Import </div>


                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p>Click here to download Sample file : <a href="import-product-sample.csv" download="import-product-sample.csv" class="btn btn-info btn-sm">Download</a> </p>
                            </div>
                            <div class="table-responsive">
                                <label for="">Dummy Content and Order of the Excel Data</label>
                                <table class="table">
                                    <thead>
                                        <tr>





                                            <th>Category Name <span>*</span></th>
                                            <th>Sub Category Name <span>*</span></th>
                                            <th>Product Name <span>*</span></th>
                                            <th>Price <span>*</span></th>
                                            <th>Min Stock <span>*</span></th>
                                         

                                            <th>UOM <span>*</span></th>
                                         







                                        </tr>
                                    </thead>
                                  
                                </table>

                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="">Select an excel file(.csv format)</label>
                                    <br>
                                    <input type="file" name="file" accept=".csv">
                                </div>
                                <button type="submit" name="btnSubmit" class="btn btn-info mt-3">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>



<?php
require '../layouts/footer.php';
?>