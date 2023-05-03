<?php
require('../db.php');
$departId = $_GET['departid'];
$docNo = $_GET['docno'];
$userId = $_GET['userid'];
$dateWrite = $_GET['datewrite'];

$objsql = "SELECT d.edoc_id,
        r.rapid_name,
        s.secrets_name,
        d.doc_no,
        d.doc_date,
        d.headline,
        d.receiver,
        d.comment,
        t.edoc_type_id,
        t.edoc_type_name,
        t.edoc_type,
        dept.depart_name,
        concat(u.user_fname, ' ', u.user_lname) as sender_name
    FROM edoc  d 
    LEFT JOIN secrets s ON d.secrets = s.secrets_id 
    LEFT JOIN rapid r ON d.rapid = r.rapid_id 
    LEFT JOIN edoc_type t ON d.edoc_type_id = t.edoc_type_id 
    LEFT JOIN department dept ON d.depart_id = dept.depart_id
    LEFT JOIN edoc_user u ON d.user_id = u.user_id 
    LEFT JOIN department deptu ON u.depart_id = deptu.depart_id
    WHERE d.depart_id = '$departId' 
    AND d.doc_no = '$docNo' 
    AND d.user_id = '$userId' 
    AND d.edoc_datewrite = '$dateWrite'";
$objrs = mysqli_query($con, $objsql);
$status = 'true';
while($objdata = mysqli_fetch_assoc($objrs) ){
    $dateArray = explode("-",$objdata['doc_date']);
    $docdate = $dateArray[2].'/'.$dateArray[1].'/'.($dateArray[0]+543);
    $data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'docNo'=>$objdata['doc_no'],
        'docDate'=>$docdate,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'comment'=>$objdata['comment'],
        'rapidName'=>$objdata['rapid_name'],
        'secretsName'=>$objdata['secrets_name'],
        'typeName'=>$objdata['edoc_type_name'],
        'type'=>$objdata['edoc_type'],
        'typeId'=>$objdata['edoc_type_id'],
        'departName'=>$objdata['depart_name'],
        'senderName'=>$objdata['sender_name'],
    );
}

$data[] = array('status'=>$objsql);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>