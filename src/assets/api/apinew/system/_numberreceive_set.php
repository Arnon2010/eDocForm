<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$departId = $request->departId;
@$numberReceive = $request->numberreceive;
@$Year = $request->year_now;

$numberReceive = $numberReceive-1;

$objsql = "SELECT receive_no FROM edoc_receive_no WHERE depart_id='$departId' AND receive_year = '$Year'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
$receiveNo = $objdata['receive_no'];

if($receiveNo == ''){
    $sql = "INSERT INTO edoc_receive_no (depart_id, receive_no, receive_year) values('$departId', '$numberReceive', '$Year')";
}else{
    $sql = "UPDATE edoc_receive_no SET receive_no = '$numberReceive' WHERE depart_id='$departId' AND receive_year = '$Year'";
}

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