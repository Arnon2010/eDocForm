<?php
session_start();
require('../db.php');
$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year'] - 543);
$userType = $_GET['usertype'];
$edocType = $_GET['doctype'];

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  

$condition = '';
if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleSend = 'Y';//Can edit to send books.
}else{
    $condition = " AND e.secrets = '1'";
    $roleSend = 'N';//cannot edit to send books.
}

if($edocType == 'all' || $edocType == 'undefined')
    $condition .= "";
else{
    $condition .= " AND e.edoc_type_id = '$edocType'";
}
    
/*
if($departIduser == '35')  //หน่ายงานมหาลัย
    $condition .= "OR e.edoc_id IN (
        SELECT edoc_id FROM edoc_univ_no
    )";
*/

function moveActivity($con, $main_id){
    // max move id
    $objsql_max = "SELECT MAX(move_id) as max_move_id 
        FROM sign_move 
        WHERE main_id = '$main_id'";
    $objrs_max = mysqli_query($con, $objsql_max);
    $data_max = mysqli_fetch_array($objrs_max);
    $move_id_max = $data_max['max_move_id'];

    $objsql = "SELECT mv.activity, t.tposition_id, t.tposition_name FROM sign_move mv 
        LEFT JOIN takeposition t ON mv.tposition_id = t.tposition_id
        WHERE main_id = '$main_id' 
        AND move_id = '$move_id_max'";
    $objrs = mysqli_query($con, $objsql);
    $data = mysqli_fetch_array($objrs);

    return array($data['activity'], $data['tposition_id'], $data['tposition_name']);
}

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
    t.edoc_type_name,
    e.edoc_datewrite,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname,
    m.main_id,
    m.main_status
    FROM sign_main m
    LEFT JOIN edoc e ON m.edoc_id = e.edoc_id
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE e.depart_id = '$depart_id' AND year(e.sent_date) = '$Year'
    AND e.edoc_id NOT IN (
        SELECT edoc_id FROM edoc_receive WHERE receive_type = '2'
    )
    $condition
    ORDER BY e.edoc_id DESC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    list($activity, $tposition_id, $tposition_name) = moveActivity($con, $objdata['main_id']);

    if($activity == 'Upload')
        $move_status = 'เสนอลงนาม';
    else if($activity == 'Signatured')
        $move_status = 'ลงนามแล้ว';
    else if($activity == 'TranferTo')
        $move_status = 'เสนอถึง';
    

    $data[] = array(
        'No'=>$i,
        'mainId'=>$objdata['main_id'],
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocDate'=>$edocDateNew,
        'edocTypeName'=>$objdata['edoc_type_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'Sender'=>$objdata['sender'],
        'dateWrite'=>$objdata['edoc_datewrite'],
        'approvStatus'=>$objdata['main_status'],
        'roleSend'=> $roleSend,
        'moveActivity'=> array('status'=>$move_status, 'tpositionId'=>$tposition_id, 'tpositionName'=>$tposition_name)
    );
}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
