<?php
include "../db.php";

@$departId = $_GET['departid'];
@$yearNow= $_GET['year_now'];

$sql = "SELECT receive_no FROM edoc_receive_no WHERE depart_id = '$departId' AND receive_year = '$yearNow'";
$result = mysqli_query($con, $sql);
$rows = mysqli_fetch_array($result);

if($rows['receive_no'] == '')
    @$receiveNo = 1;
else{
    @$receiveNo = $rows['receive_no'] + 1;
}
$data[] = array(
        "receiveNum_next"=>$receiveNo
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
