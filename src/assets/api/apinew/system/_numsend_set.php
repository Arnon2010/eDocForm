<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$departId = $request->departid;
@$doctypeId = $request->doctypeid;
@$numberSend = $request->numbersend;
@$Year = $request->year_now;

$numberSend = $numberSend-1;

// if($numberSend <= 0)
//     $numberSend = 0;

$objsql = "SELECT sent_no FROM edoc_sent_no 
    WHERE sent_year = '$Year' 
    AND depart_id='$departId' 
    AND edoc_type_id='$doctypeId'";

$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);

$sendNo = $objdata['sent_no'];

if($sendNo == ''){
    $sql = "INSERT INTO edoc_sent_no (edoc_type_id, sent_no, sent_year, depart_id) VALUES('$doctypeId', '$numberSend', '$Year', '$departId')";
}else{
    $sql = "UPDATE edoc_sent_no SET sent_no='$numberSend', sent_year='$Year' 
    WHERE sent_year='$Year' 
    AND depart_id='$departId' 
    AND edoc_type_id='$doctypeId'";
}

// update status
// $objUpdStatus = "UPDATE edoc_sent_no SET status = '0' WHERE sent_year <> '$Year' 
//     AND depart_id='$departId' 
//     AND edoc_type_id='$doctypeId'";
// mysqli_query($con, $objUpdStatus);

if(mysqli_query($con, $sql)){
    $data[] = array(
        'status'=>'true'
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>