<?php
require('../db.php');
$departId = $_GET['departid'];

// $objsql = "SELECT count(gu.deleg_user_id) as memberGroupTotal, g.deleg_group_id, g.deleg_group_name, g.status
//     FROM delegate_group g
//     LEFT JOIN delegate_user gu ON gu.deleg_group_id = g.deleg_group_id 
//     WHERE g.depart_id = '$departId'
//     GROUP BY gu.deleg_group_id 
//     ORDER BY g.deleg_group_name ASC
//     ";

$objsql = "SELECT count(gu.citizen_id) as memberGroupTotal, g.deleg_group_id, g.deleg_group_name, g.status
    FROM delegate_group g 
    LEFT JOIN delegate_group_user gu ON g.deleg_group_id = gu.deleg_group_id
    LEFT JOIN delegate_user u ON gu.citizen_id = u.citizen_id 
    WHERE g.depart_id = '$departId'
    GROUP BY gu.deleg_group_id 
    ORDER BY g.deleg_group_name ASC
    ";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'delegGroupId'=>$objdata['deleg_group_id'],
        'delegGroupName'=>$objdata['deleg_group_name'],
        'memberGroupTotal'=>$objdata['memberGroupTotal'],
        'departId'=>$objdata['depart_id'],
        'status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
