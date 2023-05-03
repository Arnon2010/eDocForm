<?php
require('../db.php');

$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year']-543);

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
    
$objsql = "SELECT count(s.sent_id) as amount_order
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    WHERE s.depart_id = '$departIduser'
    AND s.sent_status in ('1')
    ";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
$objdata = mysqli_fetch_assoc($objrs);
$amount = $objdata['amount_order'];
    
    $data[] = array(
        'amountOrder'=>$amount,
        'resp sql:'=>''
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
