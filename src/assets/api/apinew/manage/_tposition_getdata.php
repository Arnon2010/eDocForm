<?php
require('../db.php');
$facId = $_GET['facid'];
$objsql = "SELECT * FROM tbtakeposition t
    LEFT JOIN tbposition p ON t.position_id = p.position_id
    WHERE t.status = '1' AND t.fac_id = '$facId'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'takepId'=>$objdata['tposition_id'],
        'takepName'=>$objdata['tposition_name'],
        'positionName'=>$objdata['position_name'],
        'facultyId'=>$objdata['fac_id'],
        'status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
