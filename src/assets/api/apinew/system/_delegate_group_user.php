<?php
require('../db.php');
$delegateGroupId = $_GET['deleg_group_id'];

$objsql = "SELECT gu.deleg_groupuser_id, gu.citizen_id, u.person_name 
    FROM delegate_group_user gu
    LEFT JOIN delegate_user u ON gu.citizen_id = u.citizen_id
    WHERE gu.deleg_group_id = '$delegateGroupId'
    ORDER BY person_name";
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'citizenId'=>$objdata['citizen_id'],
        'personName'=>$objdata['person_name'],
        'delegGroupUserId'=>$objdata['deleg_groupuser_id']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>