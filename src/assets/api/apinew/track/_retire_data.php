<?php
require('../db.php');
require('../fn.php');
//$departId = $_GET['departid'];
@$edocId = $_GET['edocid'];

/*
$objsql_dept = "SELECT 
    p.depart_id,
    d.depart_name
    FROM retire r
    LEFT JOIN takeposition t ON r.tposition_id = t.tposition_id
    LEFT JOIN position p ON t.position_id = p.position_id
    LEFT JOIN department d ON p.depart_id = d.depart_id
    WHERE r.edoc_id = '$edocId'
    GROUP BY p.depart_id
    ORDER BY retire_id ASC";*/

$objsql_dept = "SELECT r.pdf_name_retire, r.pdf_path_retire, d.depart_id, d.depart_name
    FROM edoc_receive r
    LEFT JOIN department d ON r.depart_id = d.depart_id
    WHERE r.edoc_id = '$edocId'
    GROUP BY r.depart_id
    ORDER BY r.receive_id ASC ";
    
$objrs_dept = mysqli_query($con, $objsql_dept);
while($objdata_dept = mysqli_fetch_array($objrs_dept)){
    $objsql_er = "SELECT pdf_path_retire FROM edoc_receive
        WHERE edoc_id = '$edocId' AND depart_id = '$objdata_dept[depart_id]'";
    $objrs_er = mysqli_query($con, $objsql_er);
    $objdata_er = mysqli_fetch_array($objrs_er);
    
    $data_dept[] = array(
        'departId'=>$objdata_dept['depart_id'],
        'departName'=>$objdata_dept['depart_name'],
        'filePathRetire'=>$objdata_er['pdf_path_retire']
    );
    
    $objsql = "SELECT r.retire_id,
        r.retire_date,
        r.retire_text,
        p.position_name,
        t.tposition_name,
        p.depart_id,
        d.depart_name
        FROM retire r
        LEFT JOIN takeposition t ON r.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id
        LEFT JOIN department d ON p.depart_id = d.depart_id
        WHERE r.edoc_id = '$edocId' AND p.depart_id = '$objdata_dept[depart_id]'
        ORDER BY retire_id ASC";
        
    $objrs = mysqli_query($con, $objsql);
    $numrow = mysqli_num_rows($objrs);
    $No = 0;
    while($objdata = mysqli_fetch_assoc($objrs) ){ $No++;
        
        $retireDate = explode("-",$objdata['retire_date']);
        $retireDateNew = $retireDate[2].'/'.$retireDate[1].'/'.($retireDate[0]+543);
        
        $data[] = array(
            'No'=>$No,
            'retireId'=>$objdata['retire_id'],
            'retireText'=>$objdata['retire_text'],
            'retireDate'=>$retireDateNew,
            'positionName'=>$objdata['position_name'],
            'tpositionName'=>$objdata['tposition_name'],
            'departId'=>$objdata['depart_id'],
            'departName'=>getDepartmentClass($objdata['depart_id']),
            'numRow' =>$numrow
            
        );
    }
    
}



$rows[] = array(
    'numrow'=>$numrow  
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data_dept"=>$data_dept, "data"=>$data, "rows"=>$rows));
