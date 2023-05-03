<?php
require('../db.php');

@$edocId = $_GET['edocid'];
@$univId = $_GET['univid'];

/***/
$objsql = "SELECT depart_id, depart_name 
        FROM department 
        WHERE depart_id NOT IN
            (
                SELECT depart_id
                FROM edoc_sent_temp
                WHERE edoc_id='$edocId'
            )
        AND univ_id = '$univId'
        AND depart_parent = '0'
        ORDER BY depart_name ASC";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['depart_id'],
        'Name'=>$objdata['depart_name']
    );
    
    $objsql_under = "SELECT d.depart_id, d.depart_name
        FROM department_hier h
        LEFT JOIN department d ON h.under_depart_id = d.depart_id
        WHERE d.depart_id NOT IN
            (
                SELECT depart_id
                FROM edoc_sent_temp
                WHERE edoc_id='$edocId'
            )
        AND d.univ_id = '$univId'
        AND h.head_depart_id = '$objdata[depart_id]'
        ORDER BY d.depart_name ASC";
    
    $objres_under = mysqli_query($con, $objsql_under);
    while($objdata_under = mysqli_fetch_array($objres_under)){
        $data[] = array(
        'Id'=>$objdata_under['depart_id'],
        'Name'=>'===='.$objdata_under['depart_name']
        );
    }
    
}


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>