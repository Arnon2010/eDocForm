<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
@$dateSendStart = $request->date_send_start;
@$dateSendStop = $request->date_send_stop;
@$numberSend = $request->number_send;
@$departId = $request->depart_id;//หน่วยงาน
@$Year = ($request->doc_year)-543;//ปีของหนังสือส่ง
@$searchType = $request->search_type; //ประเภทค้นหา

$dateStart_array = explode("/",$dateSendStart);
$dateStop_array = explode("/",$dateSendStop);

if($searchType == 'numsend'){ //ค้นหาด้วยเลขหนังสือส่ง
    if(strpos($numberSend, '-') !== false) { //number is 5-10
        $numArray = explode("-",$numberSend);
        $numStart = $numArray[0];
        $numEnd = $numArray[1];
        if($departId != '35') {// กรณีไม่ใช่ หน่วยงาน มหาวิทยาลัยเทคโนโลยีราชมงคลศรีวิชัย
            $condition .= "AND (SUBSTRING_INDEX(es.sent_no,'/',-1) BETWEEN '$numStart' AND '$numEnd')";
        }else{ 
            $condition .= "AND e.edoc_id IN (
                SELECT edoc_id FROM edoc_univ_no
                ) 
                AND (SUBSTRING_INDEX(es.sent_no,'/',-1) BETWEEN '$numStart' AND '$numEnd')";
        }
    }else{
        $condition .= "AND es.sent_no LIKE '%/$numberSend'";
    }

}else{
    if($dateSendStart){
        $dateStart_new = $dateStart_array[2].'-'.$dateStart_array[1].'-'.$dateStart_array[0];
        if($departId != '35') {// กรณีไม่ใช่ หน่วยงาน มหาวิทยาลัยเทคโนโลยีราชมงคลศรีวิชัย
            $condition .= "AND e.doc_date = '$dateStart_new'";
        } else {
            $condition .= "AND e.edoc_id IN (
                SELECT edoc_id FROM edoc_univ_no
                ) 
                AND e.doc_date = '$dateStart_new'";
        }
    }
    
    /*
    if($dateSendStop){
        $dateStop_array = explode("/",$dateSendStop);
        $dateStop_new = $dateStop_array[2].'-'.$dateStop_array[1].'-'.$dateStop_array[0];
        $condition .= "AND rc.receive_date <= '$dateEnd_new'";
    }
    */
}


$dateReport = $dateStart_array[0].'/'.$dateStart_array[1].'/'.($dateStart_array[2]+543);

// Select doctype
if($departId != '35') {// กรณีไม่ใช่ หน่วยงาน มหาวิทยาลัยเทคโนโลยีราชมงคลศรีวิชัย
    $condition_type = " AND es.depart_id_send = '$departId'";
    $condition2 = " AND e.depart_id = '$departId'";

} else {
    $condition_type = "";
    $condition2 = "";

}
    $objsqldoctype = "SELECT
        t.edoc_type_name,
        t.edoc_type_id,
        e.doc_date
        FROM edoc_sent es
        LEFT JOIN edoc e ON es.edoc_id = e.edoc_id
        LEFT JOIN department d ON e.depart_id = d.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        WHERE year(e.doc_date) = '$Year' 
        $condition 
        $condition_type
        GROUP BY t.edoc_type_id, t.edoc_type_name
        ORDER BY t.edoc_type_name ASC";
    
    $objrsdoctype = mysqli_query($con, $objsqldoctype);
    
    while($objdatadoctype = mysqli_fetch_array($objrsdoctype)){
        
        //$dateReport = thai_date_fullmonth($objdatadoctype['doc_date']);
        $docdate_array = explode("-",$objdatadoctype['doc_date']);
        $dateReport = $docdate_array[2].'/'.$docdate_array[1].'/'.($docdate_array[0]+543);
        
        $doctype[] = array('edocTypeName'=>$objdatadoctype['edoc_type_name'],
            'edocTypeId'=>$objdatadoctype['edoc_type_id'],
            'departId_send'=>$objdatadept['depart_id'],
            'dateReport'=>$dateReport
        );

        $objsql = "SELECT e.edoc_id,
            e.doc_no,
            e.edoc_type_id,
            e.doc_date,
            e.headline,
            e.receiver,
            e.comment,
            r.rapid_name,
            s.secrets_name,
            dd.depart_name AS depart_doc_name,
            e.status,
            e.sender,
            CONCAT(u.user_fname,' ',u.user_lname) as userFullname
            FROM edoc_sent es
            LEFT JOIN edoc e ON es.edoc_id = e.edoc_id
            LEFT JOIN department dd ON e.depart_id = dd.depart_id
            LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
            LEFT JOIN rapid r ON e.rapid = r.rapid_id
            LEFT JOIN secrets s ON e.secrets = s.secrets_id
            LEFT JOIN edoc_user u ON e.user_id = u.user_id
            WHERE year(e.doc_date) = '$Year'
            AND e.edoc_type_id = '$objdatadoctype[edoc_type_id]'
            $condition 
            $condition2
            GROUP BY e.edoc_id
            ORDER BY e.edoc_id DESC";

        $objrs = mysqli_query($con, $objsql);
        $i = 0;

        while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
            $edocDate = explode("-",$objdata['doc_date']);
            $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
            $data[] = array(
                'No'=>$i,
                'edocTypeId'=>$objdata['edoc_type_id'],
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
                'deptReceive'=>$objdata['depart_receive_name'],
                'Sender'=>$objdata['sender'],
                'edocStatus'=>$objdata['status'],
                'roleSend'=>$roleSend
            );
        }

    }//end doc type

$row[] = array('rowReceive'=>$numrow, 'resp'=>''.$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data, "row"=>$row, "dept"=>$dept, "doctype"=>$doctype));