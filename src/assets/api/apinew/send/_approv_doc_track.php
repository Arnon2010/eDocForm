<?php
session_start();
require('../db.php');
$mainId = $_GET['main_id'];

// get data last activity

$objsql_move = "SELECT mv.activity, mv.move_status, t.tposition_id, t.tposition_name, sd.file_name, sd.file_path, p.position_name,
            mx.moveidMax, date(mv.time) as date_activity, time(time) as time_activity
        FROM (

            SELECT main_id, MAX(move_id) as moveidMax FROM sign_move 
            WHERE main_id = '$mainId' 
            GROUP BY main_id
            
        ) mx
        INNER JOIN sign_move mv ON mv.main_id = mx.main_id AND mv.move_id = mx.moveidMax
        LEFT JOIN sign_detail sd ON mv.detail_id = sd.detail_id
        LEFT JOIN takeposition t ON mv.tposition_id = t.tposition_id 
        LEFT JOIN position p ON t.position_id = p.position_id
        WHERE mv.main_id = '$mainId'  
        GROUP BY mv.main_id
        ";
$objrs_move = mysqli_query($con, $objsql_move);
$dataMove = mysqli_fetch_array($objrs_move);

if($dataMove['user_id'] != '') {
    $operator_user_last = $dataMove['user_fulname'];
} 
else {
    //$operator_user = $objdata['tposition_name'];
    $operator_user_last = "";
}

if($dataMove['activity'] == 'Upload')
    $move_status = 'เสนอถึง';
else if($dataMove['activity']  == 'Signatured')
    $move_status = 'ลงนามแล้ว';
else if($dataMove['activity']  == 'TranferTo')
    $move_status = 'เสนอถึง';
else if($dataMove['activity']  == 'Done')
    $move_status = 'เสร็จสิ้น';
else if($dataMove['activity']  == 'ApplyNumber')
    $move_status = 'ขอเลขมหาลัย';
else if($dataMove['activity']  == 'GetNumber')
    $move_status = 'ออกเลขหนังสือ';
else if($dataMove['activity']  == '')
    $move_status = 'รอเสนอหนังสือ';
else if($dataMove['activity']  == 'Remark')
    $move_status = 'ถูกส่งกลับ';

if($dataMove['move_status'] == 'D')
    $move_status .= ' (ถูกยกเลิก)';


$dateAct = explode("-",$dataMove['date_activity']);
$dateActNew = $dateAct[2].'/'.$dateAct[1].'/'.($dateAct[0]+543);
    

$data_track_last[] = array(
    'filePath'=>$dataMove['file_path'],
    'Activity'=>$dataMove['activity'],
    'trackActivity'=>$move_status,
    'tpositionName'=>$dataMove['tposition_name'],
    'trackTime'=>$dataMove['time_activity'],
    'trackDate'=> $dateActNew,
    'operatorUser'=>$operator_user_last,
    'Position'=>$dataMove['position_name']
);


// get data activity

$objsql = "SELECT 
    sm.detail_id,
    date(sm.time) as date_move,
    time(sm.time) as time_move,
    sm.main_id,
    sm.activity,
    sm.user_id,
    sm.move_status,
    sd.file_name, 
    sd.file_path, 
    sd.detail_status, 
    t.tposition_name,
    CONCAT(u.user_fname,' ',u.user_lname) as user_fulname
    FROM  sign_move sm 
    LEFT JOIN sign_detail sd ON sm.detail_id = sd.detail_id
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id 
    LEFT JOIN edoc_user u ON sm.user_id = u.user_id
    WHERE sm.main_id = '$mainId'
    ORDER BY sm.move_id DESC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    $dateMove = explode("-",$objdata['date_move']);
    $dateMoveNew = $dateMove[2].'/'.$dateMove[1].'/'.($dateMove[0]+543);

    if($objdata['user_id'] != '') {
        $operator_user = $objdata['user_fulname'];
    
    } 
    else {
        //$operator_user = $objdata['tposition_name'];
        $operator_user = "";
    }

    $move_status = '';
    $move_status_cancel = '';

    if($objdata['activity'] == 'Upload')
        $move_status = 'เสนอถึง';
    else if($objdata['activity']  == 'Signatured')
        $move_status = 'ลงนามแล้ว';
    else if($objdata['activity']  == 'TranferTo')
        $move_status = 'เสนอถึง';
    else if($objdata['activity']  == 'Done')
        $move_status = 'เสร็จสิ้น';
    else if($objdata['activity']  == '')
        $move_status = 'รอเสนอหนังสือ';
    else if($objdata['activity'] == 'DoneNotnumber')
        $move_status = 'ไม่ออกเลข';
    else if($objdata['activity'] == 'DoneSent')
        $move_status = 'ส่งหนังสือแล้ว';
    else if($objdata['activity']  == 'ApplyNumber')
        $move_status = 'ขอเลขมหาลัย';
    else if($objdata['activity']  == 'GetNumber')
        $move_status = 'ออกเลขหนังสือ';
    else if($objdata['activity']  == 'Remark')
        $move_status = 'หนังสือถูกส่งกลับ';


    if($objdata['move_status'] == 'D')
        $move_status_cancel .= ' (ถูกยกเลิก)';

    $data[] = array(
        'No'=>$i,
        'detailId'=>$objdata['detail_id'],
        'mainId'=>$objdata['main_id'],
        'fileName'=>$objdata['file_name'],
        'filePath'=>$objdata['file_path'],
        'trackActivity'=>$move_status,
        'tpositionName'=>$objdata['tposition_name'],
        'trackTime'=>$objdata['time_move'],
        'trackDate'=> $dateMoveNew,
        'operatorUser'=>$operator_user,
        'moveStatus'=>$objdata['move_status'],
        'moveStatusCancel'=>$move_status_cancel,
        'activity'=>$objdata['activity']
    );

}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "data_track_last"=>$data_track_last));
