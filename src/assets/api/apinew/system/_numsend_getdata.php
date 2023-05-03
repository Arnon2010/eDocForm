<?php
include "../db.php";

@$departId = $_GET['departid'];
@$doctypeId = $_GET['doctypeid'];
@$yearNow = $_GET['year_now'];

$sql = "SELECT sent_no, status FROM edoc_sent_no
    WHERE depart_id = '$departId'
    AND edoc_type_id = '$doctypeId'
    AND sent_year = '$yearNow'";
$result = mysqli_query($con, $sql);

$rows = mysqli_fetch_array($result);

if($rows['sent_no'] == '')
    @$sendNo = 1;
else{
    @$sendNo = $rows['sent_no'] + 1;
}
$data[] = array(
        "sendNum_next"=>$sendNo,
        "sendNum_status"=>$rows['status'],
        "resp"=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
