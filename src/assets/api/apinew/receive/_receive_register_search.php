<?php
require('../db.php');

$docNo = trim($_GET['docno']); //เลขหนังสือ
$departId = $_GET['depart_id']; // หน่วยงานรับหนังสือ

$docdateArray = explode("/", $_GET['docdate']);
$docDate = $docdateArray[2].'-'.$docdateArray[1].'-'.($docdateArray[0]); //วันที่หนังสือ

$objsql = "SELECT e.edoc_id,
        r.rapid_name,
        s.secrets_name,
        e.doc_no,
        e.secrets,
        e.rapid,
        e.doc_date,
        e.headline,
        e.receiver,
        e.comment,
        e.sender,
        e.depart_id,
        e.destroy_year,
        t.edoc_type_id,
        t.edoc_type_name,
        t.edoc_type,
        d.depart_name
    FROM edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id 
    LEFT JOIN rapid r ON e.rapid = r.rapid_id 
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    WHERE e.doc_no='$docNo' AND e.doc_date = '$docDate'";
$objrs = mysqli_query($con, $objsql);
$numrow = mysqli_num_rows($objrs);
$status = 'true';
$objdata = mysqli_fetch_assoc($objrs);

//ตรวจสอบว่ามีการรับหนังสือฉบับนี้แล้วยัง
$objsqlr= "SELECT count(edoc_id) as num_doc_received, receive_no
    FROM edoc_receive 
    WHERE edoc_id = '".$objdata['edoc_id']."' AND depart_id = '$departId'";
$objrsr = mysqli_query($con, $objsqlr);
$objdatar = mysqli_fetch_assoc($objrsr);
$num_received = $objdatar['num_doc_received'];
$receive_no = $objdatar['receive_no'];

//ตรวจสอบรายการหนังสือเข้า และหนังสือส่งต่อ
$objsqls= "SELECT count(edoc_id) as num_doc_order 
    FROM edoc_sent
    WHERE edoc_id = '".$objdata['edoc_id']."' 
    AND depart_id = '$departId' 
    AND sent_status IN ('1', '3')";

$objrss = mysqli_query($con, $objsqls);
$objdatas = mysqli_fetch_assoc($objrss);
$num_receive_order = $objdatas['num_doc_order'];

$dateArray = explode("-",$objdata['doc_date']);
$docdate = $dateArray[2].'/'.$dateArray[1].'/'.($dateArray[0]+543);
$data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'docNo'=>$objdata['doc_no'],
        'docDate'=>$docdate,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'comment'=>$objdata['comment'],
        'rapidId'=>$objdata['rapid'],
        'rapidName'=>$objdata['rapid_name'],
        'secretsId'=>$objdata['secrets'],
        'secretsName'=>$objdata['secrets_name'],
        'typeName'=>$objdata['edoc_type_name'],
        'type'=>$objdata['edoc_type'],
        'typeId'=>$objdata['edoc_type_id'],
        'destroyYear'=>$objdata['destroy_year'],
        'docDepartId'=>$objdata['depart_id'],
        'docDepartName'=>$objdata['depart_name'],
        'Sender'=>$objdata['sender'],
        'docNumrow'=>$numrow,
        'numReceived'=>$num_received,
        'numReceiveOrder'=>$num_receive_order,
        'receiveNo'=>$receive_no
    );


$data[] = array('status'=>'', 'docNumrow'=>$numrow);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>