<?php
require('../db.php');
$deteg_group_id = $_GET['deteg_group_id'];

$objsql = "SELECT gu.deleg_groupuser_id, gu.citizen_id, u.person_name, u.depart_id 
    FROM delegate_group_user gu
    LEFT JOIN delegate_user u ON gu.citizen_id = u.citizen_id
    WHERE gu.deleg_group_id = '$deteg_group_id'
    ORDER BY person_name";

$objrs = mysqli_query($con, $objsql);
$i=0;

while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['citizen_id'],
        'PersonName'=>$objdata['person_name'],
        'DepartId'=>$objdata['depart_id']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>