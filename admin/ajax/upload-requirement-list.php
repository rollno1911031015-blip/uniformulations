<?php
require "../../config.php";


$upload_dir = 'temp/';
$file_url = "";



// Check if the file parameter exists in the POST request
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Specify the directory where you want to store the uploaded files
    $uploadDirectory = 'temp/';

    // Create the upload directory if it doesn't exist
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Generate a unique file name to avoid overwriting existing files
    $fileName = uniqid() . '_' . $file['name'];
    $targetPath = $uploadDirectory . $fileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $file_url = $targetPath;
    } else {
        echo 'Error uploading file.';
    }
} else {
    echo 'No file received.';
}
$data = [];


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
          
         
          
            $product_name = $filesop[0];
          
            $qty = $filesop[1];

            $count++;

            try {
                $stmt = $mysqli->prepare("SELECT * from products where  name=?");
                $stmt->bind_param('s', $product_name);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            } catch (\Throwable $th) {
                $data['error']=$th->getMessage();
            }


            if ($res) {
                try {
                    $res['qty'] = $qty;
                   

                    $data[] = $res;
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                }
            } else {
                try {
                    // $stmt = $mysqli->prepare("INSERT INTO products (brand,category,sub_category,name,price,min_stock,warranty_days,is_active,uom,product_code,color) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                    // $stmt->bind_param("ssssdiiisss",  $brand, $category_name, $sub_category_name, $product_name, $price, $min_stock, $warranty_days, $active, $uom, $product_code, $color);
                    // $stmt->execute();


                    // $success++;
                    // $error .= "Lead  row " . $i . " added successfully..<br>";
                    // alert("Save Successfully", "success", "success");



                } catch (Exception $e) {
                    alert($e->getMessage(), "error", "error");
                }
            }
        } else {
            $error .= 'Invalid format <br>';
        }
        $i++;
    }
    echo json_encode($data);

    fclose($handle);
}
