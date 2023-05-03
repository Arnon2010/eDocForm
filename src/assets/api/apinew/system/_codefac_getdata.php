<?php
include "../db.php";

$departId = $_GET['departid'];
$sql = "SELECT depart_code FROM department WHERE depart_id = '$departId'";
$result = mysqli_query($con, $sql);
while($rows = mysqli_fetch_assoc($result))
{
    $data[] = array(
        "departCode"=> $rows['depart_code']
    );
}
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
