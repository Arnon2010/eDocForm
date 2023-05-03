<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];

$objsql = "SELECT sd.detail_id, sd.detail_id, sd.main_id, sd.file_name, sd.file_path, sd.detail_status, t.tposition_name
    FROM  sign_detail sd 
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
    WHERE sd.main_id = '$mainId'
    ORDER BY sd.detail_id ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    $data[] = array(
        'No'=>$i,
        'detailId'=>$objdata['detail_id'],
        'mainId'=>$objdata['main_id'],
        'fileName'=>$objdata['file_name'],
        'filePath'=>$objdata['file_path'],
        'detailStatus'=>$objdata['detail_status'],
        'tpositionName'=>$objdata['tposition_name']
    );

}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
