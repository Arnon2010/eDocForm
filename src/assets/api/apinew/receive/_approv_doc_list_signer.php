<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];

$objsql = "SELECT s.sequ_no, s.main_id, t.tposition_id, t.tposition_name, p.position_name
    FROM  sign_sequence s 
    LEFT JOIN takeposition t ON s.tposition_id = t.tposition_id 
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE s.main_id = '$mainId'
    ORDER BY s.sequ_no ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    $data[] = array(
        'No'=>$i,
        'sequNo'=>$objdata['sequ_no'],
        'mainId'=>$objdata['main_id'],
        'signerName'=>$objdata['tposition_name'],
        'tpositionId'=>$objdata['tposition_id'],
        'positionName'=>$objdata['position_name']
    );

}

$objsqlMaxNo = "SELECT MAX(sequ_no) AS sequ_max_no FROM sign_sequence WHERE main_id = '$mainId'";
$objrsMaxNo = mysqli_query($con, $objsqlMaxNo);
$objdataMaxNo = mysqli_fetch_assoc($objrsMaxNo);

$sequMaxNo = $objdataMaxNo['sequ_max_no'];
$datamax_no[] = array('sequMaxNo'=>$sequMaxNo);

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "sequ_max"=>$datamax_no));
