<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$userType = $request->usertype;//สิทธิ์ของผู้ใช้งาน
@$departIduser = $request->departid_user;//หน่วยงานของ user

@$departId = $request->departid;//หน่วยงาน
@$edocTypeId = $request->edoctype;
@$dateReceiveStart = $request->dateReceiveStart;
@$dateReceiveEnd = $request->dateReceiveEnd;
@$edocDateStart = $request->edocDateStart;
@$edocDateEnd = $request->edocDateEnd;
// ความลับ
if($request->secrets_1 != '') $Secrets1='1';
if($request->secrets_2 != '') $Secrets2='2';
if($request->secrets_3 != '') $Secrets3='3';
if($request->secrets_4 != '') $Secrets4='4';
//ความเร่งด่วน
if($request->rapid_1 != '') $Rapid1='1';
if($request->rapid_2 != '') $Rapid2='2';
if($request->rapid_3 != '') $Rapid3='3';
if($request->rapid_4 != '') $Rapid4='4';

@$numberReceive = $request->numberReceive;
@$docNo = $request->docNo;
@$Headline = $request->headline;
@$Receiver = $request->receiver;
@$Sender = $request->sender;
@$Comment = $request->comment;
@$departFromId = $request->departFromId;
@$departRetireId = $request->departRetireId;
@$positionId = $request->positionId;
@$tpositionId = $request->tpositionId;

if($departId){
    $condition .= "AND rc.depart_id = '$departId'";
}

if($dateReceiveStart){
    $dateReceiveStart_array = explode("/",$dateReceiveStart);
    $dateReceiveStart_new = $dateReceiveStart_array[2].'-'.$dateReceiveStart_array[1].'-'.$dateReceiveStart_array[0];
    $condition .= "AND rc.receive_date >= '$dateReceiveStart_new'";
}

if($dateReceiveEnd){
    $dateReceiveEnd_array = explode("/",$dateReceiveEnd);
    $dateReceiveEnd_new = $dateReceiveEnd_array[2].'-'.$dateReceiveEnd_array[1].'-'.$dateReceiveEnd_array[0];
    $condition .= "AND rc.receive_date <= '$dateReceiveEnd_new'";
}

if($edocDateStart){
    $edocDateStart_array = explode("/",$edocDateStart);
    $edocDateStart_new = $edocDateStart_array[2].'-'.$edocDateStart_array[1].'-'.$edocDateStart_array[0];
    $condition .= "AND e.doc_date >= '$edocDateStart_new'";
}

if($edocDateEnd){
    $edocDateEnd_array = explode("/",$edocDateEnd);
    $edocDateEnd_new = $edocDateEnd_array[2].'-'.$edocDateEnd_array[1].'-'.$edocDateEnd_array[0];
    $condition .= "AND e.doc_date <= '$edocDateEnd_new'";
}

if($edocTypeId != ''){
    $condition .= "AND e.edoc_type_id = '$edocTypeId'";
}

if($numberReceive){
    $condition .= "AND rc.receive_no like '%$numberReceive%'";
}

if($docNo){
    $condition .= "AND e.doc_no like '%$docNo%'";
}

if($Headline){
    $condition .= "AND e.headline LIKE '%$Headline%'";
}

if($Receiver){
    $condition .= "AND e.receiver LIKE '%$Receiver%'";
}

if($Sender){
    $condition .= "AND (u.user_fname LIKE '%$Sender%' OR u.user_lname LIKE '%$Sender%' OR e.sender LIKE '%$Sender%')";
}

if($Comment){
    $condition .= "AND e.comment LIKE '%$Comment%'";
}

if($departFromId){
    $condition .= "AND rc.depart_id_send = '$departFromId'";
}

if($departRetireId){
    $condition .= "AND p.depart_id = '$departRetireId'";
}

if($positionId){
    $condition .= "AND p.position_id = '$positionId'";
}
if($tpositionId){
    $condition .= "AND rt.tposition_id = '$tpositionId'";
}

$condition2 .= " AND e.secrets in ('$Secrets1','$Secrets2', '$Secrets3', '$Secrets4')";

$condition2 .= " AND e.rapid in ('$Rapid1','$Rapid2', '$Rapid3','$Rapid4')";

$numrow = 0;

$objsqldept = "SELECT
    rc.depart_id,
    dr.depart_name
    FROM edoc_receive rc
    LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
    LEFT JOIN department dr ON rc.depart_id = dr.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON rc.user_id_send = u.user_id
    LEFT JOIN retire rt ON e.edoc_id = rt.edoc_id
    LEFT JOIN takeposition tp ON rt.tposition_id = tp.tposition_id
    LEFT JOIN position p ON tp.position_id = p.position_id
    WHERE rc.status in('1','C')
    AND dr.depart_id IS NOT null $condition
    $condition2
    GROUP BY rc.depart_id, dr.depart_name
    ORDER BY dr.depart_name ASC";
    
$objrsdept = mysqli_query($con, $objsqldept);

while($objdatadept = mysqli_fetch_array($objrsdept)){
    
    //กรณีชั้นความที่มากกว่าปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
    //$condition2 = '';
    if(($objdatadept['depart_id'] == $departIduser) || ($userType == 'SA')){
        //$condition2 .= "AND e.secrets in ('$Secrets1','$Secrets2', '$Secrets3', '$Secrets4') AND e.rapid in ('$Rapid1','$Rapid2', '$Rapid3','$Rapid4')";    
        $roleRetire = 'Y';//Can retire to receive books.
    }else{
        //$condition2 .= "AND e.secrets = '$Secrets1' AND e.rapid = '$Rapid1'";
        $roleRetire = 'N';//Can't retire to receive books.
    }
    
    $dept[] = array('departNameReceive'=>$objdatadept['depart_name'], 'departId'=>$objdatadept['depart_id']);
    
    $objsql = "SELECT e.edoc_id,
        e.doc_no,
        e.doc_date,
        e.headline,
        e.receiver,
        e.sender_depart,
        e.comment,
        rc.receive_no,
        rc.status,
        rc.depart_id,
        rp.rapid_name,
        sc.secrets_name,
        sc.secrets_id,
        ds.depart_name as depart_name_send,
        rc.pdf_path,
        rc.pdf_name,
        rc.depart_id_send,
        e.sender
        FROM edoc_receive rc
        LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
        LEFT JOIN department ds ON rc.depart_id_send = ds.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON rc.user_id_send = u.user_id
        LEFT JOIN retire rt ON e.edoc_id = rt.edoc_id
        LEFT JOIN takeposition tp ON rt.tposition_id = tp.tposition_id
        LEFT JOIN position p ON tp.position_id = p.position_id
        WHERE rc.status in('1','c')
        AND rc.depart_id = '$objdatadept[depart_id]'  $condition 
        $condition2
        GROUP BY e.edoc_id
        ORDER BY rc.receive_date DESC, rc.receive_time DESC";
    $objrs = mysqli_query($con, $objsql);
    //$rows = mysqli_num_rows($objrs);
    
    while($objdata = mysqli_fetch_assoc($objrs)){ $numrow++;
        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
        if($objdata['secrets_id'] == '1'){
            
            $data[] = array(
                'No'=>$objdata['receive_no'],
                'edocId'=>$objdata['edoc_id'],
                'edocNo'=>$objdata['doc_no'],
                'edocDate'=>$edocDateNew,
                'filePath'=>$objdata['pdf_path'],
                'fileName'=>$objdata['pdf_name'],
                'Headline'=>$objdata['headline'],
                'Receiver'=>$objdata['receiver'],
                'Comment'=>$objdata['comment'],
                'Rapid'=>$objdata['rapid_name'],
                'Secrets'=>$objdata['secrets_name'],
                'departId_receive'=>$objdata['depart_id'],
                'departId_send'=>$objdata['depart_id_send'],
                'departId_user'=>$departIduser,
                'departNameSend'=>$objdata['depart_name_send'],
                'senderDepart'=>$objdata['sender_depart'],
                'Sender'=>$objdata['sender'],
                'Status'=>$objdata['status'],
                'roleRetire'=>$roleRetire,
                'resp'=>''
            );
            
        }else{
            
            if(($objdata['depart_id'] == $departIduser) || ($objdata['depart_id_send'] == $departIduser) || ($userType == 'SA')){
                
                $data[] = array(
                    'No'=>$objdata['receive_no'],
                    'edocId'=>$objdata['edoc_id'],
                    'edocNo'=>$objdata['doc_no'],
                    'edocDate'=>$edocDateNew,
                    'filePath'=>$objdata['pdf_path'],
                    'fileName'=>$objdata['pdf_name'],
                    'Headline'=>$objdata['headline'],
                    'Receiver'=>$objdata['receiver'],
                    'Comment'=>$objdata['comment'],
                    'Rapid'=>$objdata['rapid_name'],
                    'Secrets'=>$objdata['secrets_name'],
                    'departId_receive'=>$objdata['depart_id'],
                    'departId_send'=>$objdatadept['depart_id_send'],
                    'departId_user'=>$departIduser,
                    'departNameSend'=>$objdata['depart_name_send'],
                    'Sender'=>$objdata['sender'],
                    'Status'=>$objdata['status'],
                    'roleRetire'=>$roleRetire,
                    'sql'=>''
                );
            }
        }
        
    }
}//end while dept

$row[] = array('rowReceive'=>$numrow, 'resp'=>$objsqldept);

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "row"=>$row, "dept"=>$dept));
?>