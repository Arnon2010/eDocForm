<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

@$submitType = $request->submit_type;
@$tpositionName = $request->tpositionName;
@$positionId = $request->positionId;
@$positionNew = $request->positionNew;
@$departId = $request->departId;
@$epassport = strtolower(trim($request->ePassport));

@$tpositionId = strtolower(trim($request->tposition_id));

if($positionNew != '' && $positionId == 'N'){
            
    $objsql2 = "INSERT INTO `position` (`position_id`, `position_name`, `depart_id`, `status`)
        VALUES (NULL, '$positionNew', '$departId', '1');";
            
    if(mysqli_query($con, $objsql2)){
        $objsql3 = "SELECT MAX(position_id) as position_id_max
            FROM position WHERE depart_id = '$departId'";
        $objres = mysqli_query($con, $objsql3);
        $objdata = mysqli_fetch_assoc($objres);
        
        $positionnewId = $objdata['position_id_max'];
        $positionId = $positionnewId;
    }
    
}

if($submitType == 'Insert'){
    $objsql = "INSERT INTO takeposition(tposition_id, tposition_name, e_passport, position_id, status)
            VALUES(null, '$tpositionName', '$epassport', '$positionId','1')";
}else{
    
    $objsql = "UPDATE takeposition SET
        tposition_name = '$tpositionName',
        e_passport = '$epassport',
        position_id = '$positionId'
        WHERE tposition_id = '$tpositionId'";
            
}
if(mysqli_query($con, $objsql))
    $status = "true";
else
    $status = "false";
    
$data[] = array(
    'status'=>$status,
    'departid'=>$departId
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

?>