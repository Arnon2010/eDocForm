<?php
require('../db.php');
require('../fn.php');
//$departId = $_GET['departid'];
@$edocId = $_GET['edocid'];
$objsql = "SELECT r.retire_id, r.retire_date, r.retire_text, p.position_name, t.tposition_name, p.depart_id, d.depart_name
    FROM retire r
    LEFT JOIN takeposition t ON r.tposition_id = t.tposition_id
    LEFT JOIN position p ON t.position_id = p.position_id
    LEFT JOIN department d ON p.depart_id = d.depart_id
    WHERE r.edoc_id = '$edocId' ORDER BY retire_id ASC";
    
$objrs = mysqli_query($con, $objsql);
$numrow = mysqli_num_rows($objrs);
while($objdata = mysqli_fetch_assoc($objrs) ){
    
    $retireDate = explode("-",$objdata['retire_date']);
    $retireDateNew = $retireDate[2].'/'.$retireDate[1].'/'.($retireDate[0]+543);

    // แสดงผู้ได้รับมอบหมาย
    $objDeleg = "SELECT du.deleg_user_id, du.person_name,du.citizen_id 
        FROM delegate_retire dr 
        LEFT JOIN delegate_user du ON dr.deleg_user_id = du.deleg_user_id
        WHERE dr.retire_id = '$objdata[retire_id]'";

    $resDeleg = mysqli_query($con, $objDeleg);
    while($dataDeleg = mysqli_fetch_array($resDeleg)) {
        $data_deleg[] = array(
            'retireId'=>$objdata['retire_id'],
            'delegUserId'=>$dataDeleg['deleg_user_id'],
            'personName'=>$dataDeleg['person_name']
        );
    }
    
    $data[] = array(
        'retireId'=>$objdata['retire_id'],
        'retireText'=>$objdata['retire_text'],
        'retireDate'=>$retireDateNew,
        'positionName'=>$objdata['position_name'],
        'tpositionName'=>$objdata['tposition_name'],
        'departId'=>$objdata['depart_id'],
        'departName'=>getDepartmentClass($objdata['depart_id']),
        'dataDelegate'=>$data_deleg
    );
}

$rows[] = array(
    'numrow'=>$numrow  
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "rows"=>$rows));
