<?php
require "../../config.php";
 
try{
    if (!empty($_POST['name']) &&  !empty($_POST['stock'])) {
        $stmt = $mysqli->prepare("INSERT into products (category,sub_category,name,price,min_stock,warranty_days,is_active,uom) values (?,?,?,?,?,?,?,?) ;");
        $stmt->bind_param("sssdiiis", $_POST['category'], $_POST['subcategory'], $_POST['name'], $_POST['price'], $_POST['stock'], $_POST['warranty'], $_POST['active'], $_POST['unit_type']);
        $stmt->execute();
        $stmt->close();
        echo json_encode(array('msg'=>'Success','error'=>false));
    }else{
        echo json_encode(array('msg'=>'Enter All Field please','error'=>true));
    }
    
}catch(Exception $e){
    echo json_encode(array('msg'=>$e->getMessage(),'error'=>true));
}
