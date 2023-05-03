<?php
include "../db.php";

@$departId = $_GET['departid'];

$sql = "SELECT sent_year, status FROM edoc_sent_no WHERE depart_id = '$departId' AND status = '1'";
$result = mysqli_query($con, $sql);

$rows = mysqli_fetch_array($result);
    
$yearUse = $rows['sent_year'];
    
$data[] = array(
        "yearUse"=>$yearUse,
        "sql"=>''
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
