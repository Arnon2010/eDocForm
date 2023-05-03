<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$tpositionName = $request->tpositionName;
$positionId = $request->positionId;
$positionNew = $request->positionNew;
$facId = $request->facId;

if($positionNew != '' && $positionId == '0'){
    $objsql = "INSERT INTO tbposition(position_id, position_name, fac_id, status)
            VALUES(null, '$positionNew','$facId','1')";
            
    if(mysqli_query($con, $objsql)){
        $objsql = "SELECT MAX(position_id) as position_id_max FROM tbposition WHERE fac_id = '$facId'";
        $objres = mysqli_query($con, $objsql);
        $objdata = mysqli_fetch_assoc($objres);
        
        $positionnewId = $objdata['position_id_max'];
        $positionId = $positionnewId;
    }
    
}

$objsql2 = "INSERT INTO tbtakeposition(tposition_id, tposition_name, position_id, fac_id, status)
            VALUES(null, '$tpositionName', '$positionId', '$facId','1')";
            
if(mysqli_query($con, $objsql2))
    $status = "true";
else
    $status = "false";
    
$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

?>