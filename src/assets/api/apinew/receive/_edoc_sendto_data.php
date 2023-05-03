<?php
require('../db.php');
$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year']-543);

// page reload limit
$row = $_GET['row'];
$rowperpage = $_GET['rowperpage'];

$roleRegister = 'Y';//Can register to receive books.
$condition = '';



function checkdept_senddoc_return($edocid, $departid){
    require('../db.php');
    $objsql = "SELECT depart_id_send FROM edoc_sent WHERE edoc_id = '$edocid'
        AND depart_id_send = '$departid'";
    $objres = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_array($objres);
    return $objdata['depart_id_send'];
}


$objsql = "SELECT e.edoc_id,
    e.depart_id AS edoc_depart_id,
    e.edoc_type_id,
    t.edoc_type,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.status,
    r.rapid_name,
    sc.secrets_name,
    d.depart_name as departName_send,
    s.depart_id as receive_depart_id,
    s.sent_id,
    s.sent_status,
    s.pdf_path,
    s.pdf_name,
    s.sequence_status,
    s.sequence,
    s.date_sendto,
    s.time_sendto,
    e.sender_depart,
    e.sender,
    s.status_sent_doc,
    s.return_status,
    CONCAT(su.user_fname,' ',su.user_lname) as userFullname,
    eu.depart_id as user_depart_id
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    LEFT JOIN department d ON s.depart_id_send = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user eu ON e.user_id = eu.user_id
    LEFT JOIN edoc_user su ON s.user_id = su.user_id
    WHERE s.depart_id = '$departIduser'
    AND  sent_status = '3'
    AND process_status in ('1','P')
    $condition
    ORDER BY e.sent_date DESC, e.sent_time DESC 
    limit $row, $rowperpage";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
$continue = '1';//Can show rows.
while($objdata = mysqli_fetch_assoc($objrs)){

        //check more receive เคยลงรับมาแล้ว

        $row_receive = mysqli_fetch_array(mysqli_query($con, "SELECT count(receive_id) as count_row
                FROM edoc_receive WHERE edoc_id = '$objdata[edoc_id]'  AND depart_id = '$departIduser'
            "));
        $receive_exists = $row_receive['count_row'];
    
        $i++;
        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
        
        if($objdata['edoc_type_id'] == '1')
            $edoc_depart_id = $objdata['user_depart_id']; //หนังสือภายนอก หน่วยงานภายนอก
        else
            $edoc_depart_id = $objdata['edoc_depart_id'];
                
        $edocDepartId_return = checkdept_senddoc_return($objdata['edoc_id'], $departIduser);//ตรวจสอบการย้อยกลับของหนังสือ
        
        // status

        if($objdata['return_status'] == 'Y')
            $statusOrder = 'ถูกส่งกลับ';
        else if($objdata['status_sent_doc'] == '1')
            $statusOrder = 'ส่งฉบับจริง';
        else
            $statusOrder = '';
            
        $data[] = array(
            'No'=>$i,
            'sentId'=>$objdata['sent_id'],
            'edocId'=>$objdata['edoc_id'],
            'edocType'=>$objdata['edoc_type'],
            'edocNo'=>$objdata['doc_no'],
            'edocDate'=>$edocDateNew,
            'senderDepart'=>$objdata['sender_depart'],
            'Headline'=>$objdata['headline'],
            'Receiver'=>$objdata['receiver'],
            'filePath'=>$objdata['pdf_path'],
            'fileName'=>$objdata['pdf_name'],
            'Comment'=>$objdata['comment'],
            'Rapid'=>$objdata['rapid_name'],
            'Secrets'=>$objdata['secrets_name'],
            'departName_send'=>$objdata['departName_send'],
            'Sender'=>$objdata['userFullname'],
            'Status'=>$objdata['sent_status'],
            'edocStatus'=>$objdata['status'],
            'sendDate'=>$objdata['date_sendto'],
            'sendTime'=>$objdata['time_sendto'],
            'roleRegister'=>$roleRegister,
            'edocDepartId'=>$edoc_depart_id,
            'receiveDepartId'=>$objdata['receive_depart_id'],
            'statusSentDoc'=>$objdata['status_sent_doc'],
            'edocDepartId_return'=>$edocDepartId_return,
            'returnStatus'=>$objdata['return_status'],
            'statusOrder'=>$statusOrder,
            'receiveExists'=> $receive_exists
        );        
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

if(count($data) > 0)
    print json_encode($data);
    
exit;
