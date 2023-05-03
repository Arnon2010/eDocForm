<?php
include_once "../db.php";

$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

function get_status_sent_doc($edoc_id, $depart_id_send, $depart_id) {
    global $con;
    $row_status = mysqli_fetch_array(mysqli_query($con, "SELECT status_sent_doc 
        FROM edoc_sent 
        WHERE depart_id_send = '$depart_id_send' 
        AND depart_id = '$depart_id' 
        AND edoc_id = '$edoc_id' 
        AND sent_status <> 'C'"));
    
    return $row_status['status_sent_doc'];
}

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
@$departOutside = $request->departOutside;
@$departOutsideText = $request->departOutsideText;
@$departRetireId = $request->departRetireId;
@$positionId = $request->positionId;
@$tpositionId = $request->tpositionId;

$row = $request->row;
$rowperpage = $request->rowperpage;

$perpage_cond = $request->perpage_cond;
$perpage_cond2 = $request->perpage_cond2;

$total_row = $request->total_row;

$numrow = 0;

if($row > 0) { // กรณี่ที่มีการดูเพิ่มเติม
    $condition = $perpage_cond;
    $condition2 = $perpage_cond2;
    $numrow = $total_row;

} else { //ครั้งแรกที่ค้นหาข้อมูล

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
        $condition .= "AND rc.receive_no = '$numberReceive'";
        //$condition .= "AND (SUBSTRING_INDEX(rc.receive_no,'/',-1) = '$numberReceive')";
    }

    if($docNo){
        $condition .= "AND e.doc_no like '%$docNo%'";
        //$condition .= "AND (SUBSTRING_INDEX(e.doc_no,'/',-1) = '$docNo')";
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

    if($departOutsideText){
        $condition .= "AND e.depart_id = '1' AND e.sender_depart LIKE '%$departOutsideText%'";
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

    $objsql_row = "SELECT e.edoc_id
        FROM edoc_receive rc
        LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
        LEFT JOIN department ds ON e.depart_id = ds.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON rc.user_id_send = u.user_id
        LEFT JOIN retire rt ON e.edoc_id = rt.edoc_id
        LEFT JOIN takeposition tp ON rt.tposition_id = tp.tposition_id
        LEFT JOIN position p ON tp.position_id = p.position_id
        WHERE rc.status in('1','c')
        $condition 
        $condition2
        GROUP BY rc.edoc_id, rc.depart_id_send
        ";
    $objrs_row = mysqli_query($con, $objsql_row);
    
    $numrow = mysqli_num_rows($objrs_row);

}
    
    $objsql = "SELECT e.edoc_id,
        e.doc_no,
        e.doc_date,
        e.headline,
        e.receiver,
        e.sender_depart,
        e.comment,
        e.depart_id,
        rc.receive_no,
        rc.status,
        rc.depart_id_send,
        rc.depart_id AS depart_id_receive,
        rp.rapid_name,
        sc.secrets_name,
        sc.secrets_id,
        ds.depart_name as depart_name_send,
        rc.pdf_path,
        rc.pdf_name,
        e.sender
        FROM edoc_receive rc
        LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
        LEFT JOIN department ds ON e.depart_id = ds.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON rc.user_id_send = u.user_id
        LEFT JOIN retire rt ON e.edoc_id = rt.edoc_id
        LEFT JOIN takeposition tp ON rt.tposition_id = tp.tposition_id
        LEFT JOIN position p ON tp.position_id = p.position_id
        WHERE rc.status in('1','c')
       
        $condition 
        $condition2
        GROUP BY rc.edoc_id, rc.depart_id_send
        ORDER BY rc.receive_date DESC, rc.receive_time DESC 
        limit $row, $rowperpage
        ";
    $objrs = mysqli_query($con, $objsql);
    //$rows = mysqli_num_rows($objrs);
    
    while($objdata = mysqli_fetch_assoc($objrs)){

        //กรณีชั้นความที่มากกว่าปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
        //$condition2 = '';
        if(($objdata['depart_id_receive'] == $departIduser) || ($userType == 'SA')){
            //$condition2 .= "AND e.secrets in ('$Secrets1','$Secrets2', '$Secrets3', '$Secrets4') AND e.rapid in ('$Rapid1','$Rapid2', '$Rapid3','$Rapid4')";    
            $roleRetire = 'Y';//Can retire to receive books.
        }else{
            //$condition2 .= "AND e.secrets = '$Secrets1' AND e.rapid = '$Rapid1'";
            $roleRetire = 'N';//Can't retire to receive books.
        }

        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);

        // แสดงสถานะส่งหนังสือแบบปกติ หรือส่งฉบับจริง
        $status_sent_doc = get_status_sent_doc($objdata['edoc_id'], $objdata['depart_id_send'], $objdata['depart_id_receive']);
        
        if($objdata['secrets_id'] == '1'){
            
            $data[] = array(
                'No'=>$objdata['receive_no'],
                'edocId'=>$objdata['edoc_id'],
                'edocNo'=>$objdata['doc_no'],
                'edocType'=>$objdata['edoc_type'],
                'edocDate'=>$edocDateNew,
                'filePath'=>$objdata['pdf_path'],
                'fileName'=>$objdata['pdf_name'],
                'Headline'=>$objdata['headline'],
                'Receiver'=>$objdata['receiver'],
                'Comment'=>$objdata['comment'],
                'Rapid'=>$objdata['rapid_name'],
                'Secrets'=>$objdata['secrets_name'],
                'departId_doc'=>$objdata['depart_id'],
                'departId_receive'=>$objdata['depart_id_receive'],
                'departId_send'=>$objdata['depart_id_send'],
                'departId_user'=>$departIduser,
                'departNameSend'=>$objdata['depart_name_send'],
                'senderDepart'=>$objdata['sender_depart'],
                'Sender'=>$objdata['sender'],
                'Status'=>$objdata['status'],
                'roleRetire'=>$roleRetire,
                'status_sent_doc'=> $status_sent_doc
            );
            
        }else{
            
            if(($objdata['depart_id_receive'] == $departIduser) || ($objdata['depart_id_send'] == $departIduser) || ($userType == 'SA')){
                
                $data[] = array(
                    'No'=>$objdata['receive_no'],
                    'edocId'=>$objdata['edoc_id'],
                    'edocType'=>$objdata['edoc_type'],
                    'edocNo'=>$objdata['doc_no'],
                    'edocDate'=>$edocDateNew,
                    'filePath'=>$objdata['pdf_path'],
                    'fileName'=>$objdata['pdf_name'],
                    'Headline'=>$objdata['headline'],
                    'Receiver'=>$objdata['receiver'],
                    'Comment'=>$objdata['comment'],
                    'Rapid'=>$objdata['rapid_name'],
                    'Secrets'=>$objdata['secrets_name'],
                    'departId_receive'=>$objdata['depart_id_receive'],
                    'departId_send'=>$objdatadept['depart_id_send'],
                    'departId_user'=>$departIduser,
                    'departNameSend'=>$objdata['depart_name_send'],
                    'Sender'=>$objdata['sender'],
                    'Status'=>$objdata['status'],
                    'roleRetire'=>$roleRetire,
                    'status_sent_doc'=> $status_sent_doc
                );
            }
        }
        
    }

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

if(count($data) > 0){

    $row_data[] = array('rowReceive'=>$numrow, 'resp'=>'', 
        'perpage_cond'=>$condition, 
        'perpage_cond2'=>$condition2,
        'resp'=>''
    );
    @print json_encode(array("data"=>$data, "row_data"=>$row_data));

}   
exit;
