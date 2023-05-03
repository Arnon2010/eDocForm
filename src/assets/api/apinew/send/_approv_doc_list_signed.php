<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];

$objsql = "SELECT sd.detail_id, 
    sd.detail_id, 
    sd.main_id, 
    sd.file_name, 
    sd.file_path, 
    sd.detail_status, 
    t.tposition_name
    FROM  sign_detail sd 
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
    WHERE sd.main_id = '$mainId' AND sd.sign_status = '2'
    ORDER BY sd.detail_id ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

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
        'shotFileName'=>$shot_file_name
    );

}

//ตรวจสอบ กรณีมีการเสนอฉบับจริง
$row_main = mysqli_fetch_array(mysqli_query($con, "SELECT apply_number_univ, edoc_id, depart_id
        FROM sign_main 
        WHERE main_id = '$mainId' 
        
    "));

//หากมีกรณีขอเลขหนังสือมหาลัย (เสนอฉบับจริง)
if($row_main['apply_number_univ'] == '2'){
    // Get data from edoc_univ_no
    $row_univno = mysqli_fetch_array(mysqli_query($con, "SELECT file_path, file_name 
        FROM edoc_univ_no 
        WHERE edoc_id = '$row_main[edoc_id]' 
        AND depart_id = '$row_main[depart_id]'
    "));

    $fileName = $row_univno['file_name'];
    $filePath = $row_univno['file_path'];

    $data[] = array(
        'No'=>$i,
        'detailId'=>'',
        'mainId'=>$mainId,
        'fileName'=>$fileName,
        'filePath'=>$filePath,
        'detailStatus'=>'',
        'tpositionName'=>'เสนอฉบับจริง',
        'shotFileName'=>$fileName
    );

}
//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
