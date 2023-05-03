<?php
require('../db.php');
$userType = $_GET['usertype'];
$departId = $_GET['departid'];
$univId = $_GET['univ'];

if($departId != 'null')
    $condition = "AND depart_id = '$departId'";
else
    $condition = '';
    
if($userType == 'SA'){
    $objsql = "SELECT * FROM department WHERE depart_cancel = '0'
    AND univ_id = '$univId'
    $condition
    ORDER BY depart_name ASC";
}else if($userType == 'A'){
    $objsql = "SELECT d.depart_id, d.depart_name, depart_code
    FROM department da
    LEFT JOIN department_hier dh ON d.depart_id = dh.under_depart_id
    WHERE dh.head_depart_id = '$departId' AND d.univ_id = '$univId'
    AND  d.depart_cancel = '0'";
}

$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'Id'=>$objdata['depart_id'],
        'Code'=>$objdata['depart_code'],
        'Name'=>$objdata['depart_name']
    );
}

//$data[] = array('resp'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>