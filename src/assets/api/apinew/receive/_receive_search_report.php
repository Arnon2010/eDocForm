<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
@$dateSendStart = $request->date_send_start;
@$dateSendStop = $request->date_send_stop;
@$numberReceive = $request->number_receive;
@$departId = $request->depart_id;//หน่วยงาน
@$Year = ($request->doc_year)-543;//ปีของหนังสือส่ง
@$searchType = $request->search_type;//ประเภทค้นหา

$dateStart_array = explode("/",$dateSendStart);

if($searchType == 'numreceive'){
    if(strpos($numberReceive, '-') !== false) { //number is 5-10
        $numArray = explode("-",$numberReceive);
        $numStart = $numArray[0];
        $numEnd = $numArray[1];
        $condition .= "AND (rc.receive_no BETWEEN $numStart AND $numEnd)";
    }else{
        $condition .= "AND rc.receive_no = '$numberReceive'";
    }

}else{
    if($dateSendStart){
        $dateStart_new = $dateStart_array[2].'-'.$dateStart_array[1].'-'.$dateStart_array[0];
        $condition .= "AND rc.receive_date = '$dateStart_new'";
    }
    
    /*
    if($dateSendStop){
        $dateStop_array = explode("/",$dateSendStop);
        $dateStop_new = $dateStop_array[2].'-'.$dateStop_array[1].'-'.$dateStop_array[0];
        $condition .= "AND rc.receive_date <= '$dateEnd_new'";
    }
    */
}



// Select doctype
    
        //$dateToday = date('Y-m-d');
        //$dateReport = thai_date_fullmonth($dateToday); //วันที่ออกรายงาน
        $dateReport = $dateStart_array[0].'/'.$dateStart_array[1].'/'.($dateStart_array[2]+543);
        $data_header[] = array(
            'dateReport'=>$dateReport
        );

        $objsql = "SELECT e.edoc_id,
            e.depart_id,
            e.doc_no,
            e.edoc_type_id,
            e.doc_date,
            e.headline,
            e.receiver,
            e.comment,
            r.rapid_name,
            s.secrets_name,
            dd.depart_name AS depart_doc_name,
            ds.depart_name AS depart_send_name,
            e.sender_depart,
            e.status,
            e.sender,
            rc.receive_no,
            t.edoc_type,
            CONCAT(u.user_fname,' ',u.user_lname) as userFullname
            FROM edoc_receive rc
            LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
            LEFT JOIN department dd ON e.depart_id = dd.depart_id
            LEFT JOIN department ds ON rc.depart_id_send = ds.depart_id
            LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
            LEFT JOIN rapid r ON e.rapid = r.rapid_id
            LEFT JOIN secrets s ON e.secrets = s.secrets_id
            LEFT JOIN edoc_user u ON e.user_id = u.user_id
            WHERE rc.depart_id = '$departId'
            AND year(rc.receive_date) = '$Year'
            $condition
            ORDER BY rc.receive_id DESC";

        $objrs = mysqli_query($con, $objsql);
        $numrow = mysqli_num_rows($objrs);
        $i = 0;
        while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
            $edocDate = explode("-",$objdata['doc_date']);
            $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
            $data[] = array(
                'No'=>$i,
                'receiveNo'=>$objdata['receive_no'],
                'edocTypeId'=>$objdata['edoc_type_id'],
                'edocType'=>$objdata['edoc_type'],
                'edocId'=>$objdata['edoc_id'],
                'edocNo'=>$objdata['doc_no'],
                'edocDate'=>$edocDateNew,
                'Headline'=>$objdata['headline'],
                'Receiver'=>$objdata['receiver'],
                'Comment'=>$objdata['comment'],
                'Rapid'=>$objdata['rapid_name'],
                'Secrets'=>$objdata['secrets_name'],
                'deptDoc'=>$objdata['depart_doc_name'],
                'deptSend'=>$objdata['depart_send_name'],
                'senderDepart'=>$objdata['sender_depart'],
                'Sender'=>$objdata['sender'],
                'departId_doc'=>$objdata['depart_id'],
                
            );
        }

$row[] = array('rowReceive'=>$numrow, 'resp'=>'');

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data, "row"=>$row, "data_header"=>$data_header));