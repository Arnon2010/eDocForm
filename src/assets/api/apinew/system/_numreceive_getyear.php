<?php
include "../db.php";

@$departId = $_GET['departid'];

$sql = "SELECT receive_year, status FROM edoc_receive_no WHERE depart_id = '$departId'";
$result = mysqli_query($con, $sql);

while($rows = mysqli_fetch_array($result)){
    if($rows['status'] == '1'){
        $yearUse = $rows['receive_year'];
    }
    $data[] = array(
        "receiveYear"=>$rows['receive_year'],
        "yearUse"=>$yearUse
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
