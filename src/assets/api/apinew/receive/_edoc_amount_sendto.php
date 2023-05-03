<?php
require('../db.php');

$departIduser = $_GET['depart_iduser'];

$objsql = "SELECT count(s.sent_id) as amount_sendto
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    WHERE s.depart_id = '$departIduser'
    AND s.sent_status = '3'
    AND s.process_status IN ('1','P')
    ";

$objrs = mysqli_query($con, $objsql);
$i = 0;
$objdata = mysqli_fetch_assoc($objrs);
$amount = $objdata['amount_sendto'];

$data[] = array(
        'amountSendto'=>$amount
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
