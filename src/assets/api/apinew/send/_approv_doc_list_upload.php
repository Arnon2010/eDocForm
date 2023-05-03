<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];
$mainStatus = $_GET['main_status'];

if($mainStatus == '1')
    $condition = "AND dt.sign_status = '0'";
else if($mainStatus == '3' || $mainStatus == '4')
    $condition = "AND dt.sign_status IN ('2')";

$objsql = "SELECT dt.main_id, dt.detail_id, dt.file_name, dt.file_path, dt.sign_status, dt.upload_date, dt.sign_date, dt.remark, dt.detail_status, 
    t.tposition_name, p.position_name
    FROM  sign_detail dt 
    LEFT JOIN takeposition t ON dt.tposition_id = t.tposition_id 
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE dt.main_id = '$mainId'  
    AND dt.detail_status = '1'
    $condition
    ORDER BY dt.detail_id ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    if($objdata['tposition_name'] != '')
        $tposition_name = $objdata['tposition_name'];
    else
        $tposition_name = "รอเสนอ";

    $data[] = array(
        'No'=>$i,
        'detailId'=>$objdata['detail_id'],
        'mainId'=>$objdata['main_id'],
        'fileName'=>$objdata['file_name'],
        'filePath'=>$objdata['file_path'],
        'remarkComment'=>$objdata['remark'],
        'uploadDate'=>$objdata['upload_date'],
        'signDate'=>$objdata['sign_date'],
        'detailStatus'=>$objdata['detail_status'],
        'tpositionName'=>$tposition_name,
        'positionName'=>$objdata['position_name'],
        'signStatus'=>$objdata['sign_status'],

    );

}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
