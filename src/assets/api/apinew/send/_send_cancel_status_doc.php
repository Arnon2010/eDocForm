<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$edocId = $request->edoc_id;
@$itemData = $request->item_data;
/* Location */

$objsql = "UPDATE edoc SET status = '1' WHERE edoc_id='$edocId'";
if(mysqli_query($con, $objsql)){
    
    foreach($itemData as $item){
        $objsql_r = "SELECT * FROM edoc_receive WHERE edoc_id = '$edocId' AND depart_id = '".$item->departId."'";
        $objres_r = mysqli_query($con, $objsql_r);
        $numrow_r = mysqli_num_rows($objres_r);
        
        if($numrow_r == 1)
            $itemStatus = '2';//กรณีที่มีการลงรับแล้ว
        else{
            $itemStatus = '1';//กรณียังไม่ลงรับ
            $objsql_s = "SELECT * FROM edoc_sent WHERE edoc_id = '$edocId' AND depart_id = '".$item->departId."' AND return_status = 'Y'";
            $objres_s = mysqli_query($con, $objsql_s);
            $numrow_s = mysqli_num_rows($objres_s);
            if($numrow_s == 1){
                $itemStatus = 'R';//ส่งหนังสือกลับ  
            }
        }
        
        $objsql1 = "UPDATE edoc_sent SET sent_status = '$itemStatus' WHERE edoc_id = '$edocId'
            AND depart_id = '".$item->departId."'";
        mysqli_query($con, $objsql1);
        
        $objsql2 = "UPDATE edoc_receive SET status = '1' WHERE edoc_id = '$edocId' AND depart_id = '".$item->departId."'";
        mysqli_query($con, $objsql2);
        
        
    }
    
    $status = 1; 
}else{
    $status = 0;
}

$data[] = array(
    'status'=> $status,
    'item'=>$itemData
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));