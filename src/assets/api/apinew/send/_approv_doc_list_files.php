<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];
$tpositionId = $_GET['tposition_id'];

$objsql = "SELECT sd.detail_id, sd.detail_id, sd.main_id, sd.file_name, sd.file_path, sd.sign_status, sd.detail_status, t.tposition_name
    FROM  sign_detail sd 
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
    WHERE sd.main_id = '$mainId' AND sd.sign_status IN ('0','2','3') AND sd.tposition_id = '$tpositionId'
    ORDER BY sd.detail_id ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    if($objdata['sign_status'] == '0')
        $activity = 'เสนอถถึง';
    else if($objdata['sign_status'] == '2')
        $activity = 'ลงนามแล้ว';
    else if($objdata['sign_status'] == '3')
        $activity = 'ส่งต่อหนังสือ';
    

    $arr_name_file = explode("_edoc_", $objdata['file_name']);
    $shot_file_name = $arr_name_file[1];

    $data[] = array(
        'No'=>$i,
        'detailId'=>$objdata['detail_id'],
        'mainId'=>$objdata['main_id'],
        'fileName'=>$objdata['file_name'],
        'filePath'=>$objdata['file_path'],
        'detailStatus'=>$objdata['detail_status'],
        'tpositionName'=>$objdata['tposition_name'],
        'shotFileName'=>$shot_file_name,
        'Activity'=>$activity
    );

}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
