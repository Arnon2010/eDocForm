<?php
session_start();
require('../db.php');
$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year'] - 543);
$userType = $_SESSION['userType'];

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  
if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleSend = 'Y';//Can edit to send books.
}else{
    $condition = "AND e.secrets = '1'";
    $roleSend = 'N';//cannot edit to send books.
}

if($departIduser != '35')// กรณีไม่ใช่ หน่วยงาน มหาวิทยาลัยเทคโนโลยีราชมงคลศรีวิชัย
    $condition .= "AND e.depart_id = '$depart_id'";

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    r.rapid_name,
    s.secrets_name,
    d.depart_name,
    e.status,
    e.sender,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM  edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE  year(e.sent_date) = '$Year' 
    AND e.edoc_id IN (
        SELECT edoc_id FROM edoc_univ_no
    )
    $condition
    ORDER BY e.edoc_id DESC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    if($objdata['doc_no'] == '') {
        $docNo = 'รออนุมัติ';
        $edocDateNew = '-';
    }
    else{
        $docNo = $objdata['doc_no'];
        //วันที่หนังสือ
        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    }


    
    $data[] = array(
        'No'=>$i,
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$docNo,
        'edocDate'=>$edocDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'Sender'=>$objdata['sender'],
        'edocStatus'=>$objdata['status'],
        'roleSend'=>$roleSend
    );
}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
