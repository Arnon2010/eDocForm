<?php
require('../db.php');

$univId = $_GET[univ];

$objsql = "SELECT depart_id, depart_name FROM department
    WHERE depart_cancel = '0'
    AND depart_parent = '0'
    AND univ_id='$univId'
    ORDER BY depart_name ASC";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['depart_id'],
        'Name'=>$objdata['depart_name']
    );
    
    $objsql2 = "SELECT depart_id, depart_name
        FROM department
        WHERE depart_parent = '$objdata[depart_id]'
        ORDER BY depart_name ASC";
    $objrs2 = mysqli_query($con, $objsql2);
    while($objdata2 = mysqli_fetch_array($objrs2)){
        $data[] = array(
            'Id'=>$objdata2['depart_id'],
            'Name'=>'=='.$objdata2['depart_name']
        );
    }
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>