<?php
require('../db.php');
$edocId = $_GET['edocid'];

$objsql = "SELECT t.operation, d.depart_name, t.track_time, t.track_date, t.status, u.univ_name
    FROM  edoc_track t
    LEFT JOIN department d ON t.depart_id = d.depart_id
    LEFT JOIN univ u ON d.univ_id = u.univ_id
    WHERE t.edoc_id = '$edocId'
    ORDER BY t.track_date DESC, t.track_time DESC
    LIMIT 0,1";
    
$objrs = mysqli_query($con, $objsql);

$objdata = mysqli_fetch_assoc($objrs);
$trackDate = explode("-",$objdata['track_date']);
$trackDate_new = $trackDate[2].'/'.$trackDate[1].'/'.($trackDate[0]+543);
$data[] = array(
    'trackTime'=>$objdata['track_time'],
    'departName'=>$objdata['depart_name'],
    'univName'=>$objdata['univ_name'],
    'trackDate'=>$trackDate_new,
    'operationLast'=>$objdata['operation'],
    'operationStatus'=>$objdata['status']
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
