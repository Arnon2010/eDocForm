<?php
require('../db.php');
$departId = $_GET['departid'];

$objsql = "SELECT * FROM delegate_user 
    WHERE depart_id = '$departId'
    GROUP BY citizen_id";

// $objsql = "SELECT u.citizen_id, u.person_name
//     FROM delegate_user u
//     LEFT JOIN delegate_group g ON u.deleg_group_id = g.deleg_group_id
//     WHERE u.depart_id = '$departId' AND g.deleg_group_id IS NULL";

$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['citizen_id'],
        'Name'=>$objdata['person_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>