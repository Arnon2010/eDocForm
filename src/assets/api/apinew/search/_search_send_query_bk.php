<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$departId = $request->departid;//หน่วยงาน
@$departIduser = $request->departid_user;//รหัสหน่วยงานของเจ้าของหนังสือ
@$userType = $request->usertype;//ประเภทของผู้ใช้งาน

@$edocTypeId = $request->edoctype;
@$dateSendStart = $request->dateSendStart;
@$dateSendEnd = $request->dateSendEnd;
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

@$docNo = $request->docNo;
@$Headline = $request->headline;
@$Sender = $request->sender;
@$Comment = $request->comment;
@$departSearch = $request->departSearch;

$condition = '';

if($departId){
    $condition .= "AND s.depart_id_send = '$departSearch'";
}

if($dateSendStart){
    $dateStart_array = explode("/",$dateSendStart);
    $dateStart_new = $dateStart_array[2].'-'.$dateStart_array[1].'-'.$dateStart_array[0];
    $condition .= "AND s.date_sendto >= '$dateStart_new'";
}

if($dateSendEnd){
    $dateEnd_array = explode("/",$dateSendEnd);
    $dateEnd_new = $dateEnd_array[2].'-'.$dateEnd_array[1].'-'.$dateEnd_array[0];
    $condition .= "AND s.date_sendto <= '$dateEnd_new'";
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
    $condition .= "AND s.sent_no like '%$numberReceive%'";
}

if($docNo){
    $condition .= "AND e.doc_no like '%$docNo%'";
}

if($Headline){
    $condition .= "AND e.headline LIKE '%$Headline%'";
}

if($Sender){
    $condition .= "AND e.sender LIKE '%$Sender%'";
}

if($Comment){
    $condition .= "AND e.comment LIKE '%$Comment%'";
}

$condition2 .= " AND e.secrets in ('$Secrets1','$Secrets2', '$Secrets3', '$Secrets4')";

$condition2 .= " AND e.rapid in ('$Rapid1','$Rapid2', '$Rapid3','$Rapid4')";

$numrow = 0;

$objsqldept = "SELECT
        d.depart_name,
        d.depart_id
        FROM edoc_sent s 
        LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
        LEFT JOIN department d ON s.depart_id_send = d.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON e.user_id = u.user_id
        WHERE d.depart_id IS NOT NULL
        $condition
        $condition2
        GROUP BY d.depart_id, d.depart_name
        ORDER BY d.depart_name ASC";
    
$objrsdept = mysqli_query($con, $objsqldept);

while($objdatadept = mysqli_fetch_array($objrsdept)){ 
    
    $dept[] = array('departNameSend'=>$objdatadept['depart_name'],
                    'departId_send'=>$objdatadept['depart_id']
                    );
    // Select doctype
    $objsqldoctype = "SELECT
        t.edoc_type_name,
        t.edoc_type_id
        FROM edoc_sent s
        LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
        LEFT JOIN department d ON s.depart_id_send = d.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON e.user_id = u.user_id
        WHERE s.depart_id_send = '$objdatadept[depart_id]'
        $condition
        $condition2
        GROUP BY t.edoc_type_id, t.edoc_type_name
        ORDER BY t.edoc_type_name ASC";
    
    $objrsdoctype = mysqli_query($con, $objsqldoctype);
    
    while($objdatadoctype = mysqli_fetch_array($objrsdoctype)){
        
        $doctype[] = array('edocTypeName'=>$objdatadoctype['edoc_type_name'],
                           'edocTypeId'=>$objdatadoctype['edoc_type_id'],
                           'departId_send'=>$objdatadept['depart_id']
                    );
        //กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  
        if($objdatadept['depart_id'] == $departIduser || $userType == 'SA'){
            //$condition = "AND e.secrets IN ('$Secrets1','$Secrets2','$Secrets3','$Secrets4')";
            $roleSend = 'Y';//Can edit to send books.
        }else{
            //$condition .= "AND e.secrets = '$Secrets1'";
            $roleSend = 'N';//cannot edit to send books.
        }
    
        $objsql = "SELECT e.edoc_id,
            e.edoc_type_id,
            e.doc_no,
            e.doc_date,
            e.headline,
            e.receiver,
            e.comment,
            e.status,
            s.sent_status,
            rp.rapid_name,
            e.secrets,
            sc.secrets_name,
            s.depart_id_send,
            d.depart_name,
            s.pdf_path,
            s.pdf_name,
            e.sender,
            CONCAT(u.user_fname,' ',u.user_lname) as userFullname
            FROM edoc_sent s
            LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
            LEFT JOIN department d ON s.depart_id_send = d.depart_id
            LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
            LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
            LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
            LEFT JOIN edoc_user u ON e.user_id = u.user_id
            WHERE s.depart_id_send = '$objdatadept[depart_id]'
            AND t.edoc_type_id = '$objdatadoctype[edoc_type_id]'
            $condition
            $condition2
            GROUP BY e.edoc_id
            ORDER BY s.date_sendto DESC, s.time_sendto DESC";
        $objrs = mysqli_query($con, $objsql);
        $rows = mysqli_num_rows($objrs);
        $i = 0;
        while($objdata = mysqli_fetch_assoc($objrs)){ $numrow++;;
        
            $edocDate = explode("-",$objdata['doc_date']);
            $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
            
            if($objdata['secrets'] == '1'){
                $data[] = array(
                    'No'=>$i,
                    'edocId'=>$objdata['edoc_id'],
                    'edocTypeId'=>$objdata['edoc_type_id'],
                    'edocNo'=>$objdata['doc_no'],
                    'edocDate'=>$edocDateNew,
                    'filePath'=>$objdata['pdf_path'],
                    'fileName'=>$objdata['pdf_name'],
                    'Headline'=>$objdata['headline'],
                    'Receiver'=>$objdata['receiver'],
                    'Comment'=>$objdata['comment'],
                    'Rapid'=>$objdata['rapid_name'],
                    'Secrets'=>$objdata['secrets_name'],
                    'departName'=>$objdata['depart_name'],
                    'Sender'=>$objdata['sender'],
                    'edocStatus'=>$objdata['status'],
                    'sendStatus'=>$objdata['sent_status'],
                    'roleSend'=>$roleSend,
                    'departId_send'=>$objdata['depart_id_send'],
                    'departIdUser'=>$departIduser,
                    'userType'=>$userType
                );
                
            }else{
                
                if(($objdata['depart_id_send'] == $departIduser) || ($userType == 'SA')){
                    $data[] = array(
                        'No'=>$i,
                        'edocId'=>$objdata['edoc_id'],
                        'edocTypeId'=>$objdata['edoc_type_id'],
                        'edocNo'=>$objdata['doc_no'],
                        'edocDate'=>$edocDateNew,
                        'filePath'=>$objdata['pdf_path'],
                        'fileName'=>$objdata['pdf_name'],
                        'Headline'=>$objdata['headline'],
                        'Receiver'=>$objdata['receiver'],
                        'Comment'=>$objdata['comment'],
                        'Rapid'=>$objdata['rapid_name'],
                        'Secrets'=>$objdata['secrets_name'],
                        'departName'=>$objdata['depart_name'],
                        'Sender'=>$objdata['sender'],
                        'edocStatus'=>$objdata['status'],
                        'sendStatus'=>$objdata['sent_status'],
                        'roleSend'=>$roleSend,
                        'departId_send'=>$objdata['depart_id_send'],
                        'departIdUser'=>$departIduser,
                        'userType'=>$userType
                    );
                }
            }
        }
        
    }

    
}// end while department

$row[] = array('rowReceive'=>$numrow, 'resp'=>'');

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data, "row"=>$row, "dept"=>$dept, "doctype"=>$doctype));
?>