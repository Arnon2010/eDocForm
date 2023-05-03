<?php
include_once "../db.php";

//@$departId = $_GET['departid'];

@$userId = $_GET['userid'];

// $sql = "SELECT univ_no_status, univ_number_allow 
//     FROM department WHERE depart_id = '$departId'";

$sql = "SELECT 
    d.version_new,
    d.univ_no_status,
    d.get_univ_no,
    u.univ_number_allow
    FROM edoc_user u 
    LEFT JOIN department d ON u.depart_id = d.depart_id
    WHERE u.user_id = '$userId'";

$result = mysqli_query($con, $sql);
$rows = mysqli_fetch_array($result);
    
// $permiss_number_univ = $rows['univ_no_status'];
    
$data[] = array(
        "permiss_number_univ"=> $rows['univ_no_status'],
        "permiss_getnumber_univ_allow"=> $rows['univ_number_allow'],
        "permiss_getnumber_univ_allow_dept"=> $rows['get_univ_no'],
        "resp"=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
