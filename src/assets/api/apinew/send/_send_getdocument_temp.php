<?php
require('../db.php');

@$edocId = $_GET['edocid'];
@$Time = $_GET['time'];
/*
*/
$objsql = "SELECT t.temp_id, t.pdf_name, t.pdf_path, d.depart_name, t.receive_dept
    FROM edoc_sent_temp t
    LEFT JOIN department d ON t.depart_id = d.depart_id
    WHERE t.edoc_id='$edocId' AND t.temp_timewrite = '$Time'
    ORDER BY t.temp_run_id ASC
    ";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'edocId'=>$edocId,
        'tempId'=>$objdata['temp_id'],
        'fileName'=>$objdata['pdf_name'],
        'filePath'=>$objdata['pdf_path'],
        'departName'=>$objdata['depart_name'],
        'receiveDept'=>$objdata['receive_dept']
    );
}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>