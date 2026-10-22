<?php
require "../../config.php";
 
try{
    if (!empty($_POST['id'])) {
        $stmt = $mysqli->prepare("UPDATE po_det set is_received=? where id=?;");
        $stmt->bind_param("ii", $_POST['value'], $_POST['id']);
        $stmt->execute();
        $stmt->close();
        echo json_encode(array('msg'=>'Save Successfully','error'=>false));
    }else{
        echo json_encode(array('msg'=>'Enter All Field please','error'=>true));
    }
    
}catch(Exception $e){
    echo json_encode(array('msg'=>$e->getMessage(),'error'=>true));
}
