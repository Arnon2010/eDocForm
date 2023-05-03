<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
@$edocTypeId = $request->edoctype;
@$edocId = $request->edocid;
@$userId_send= $request->userid_send;
@$receiveNo_first = $request->receiveno; //เลขรับหนังสือส่งต่อ
@$departId_receive = $request->departid_receive;//หน่วยงานรับ
@$departId_send = $request->departid_send;//ส่งจากหน่วยงาน
@$fileName = $request->filename;
@$filePath = $request->filepath;
@$userId = $request->userid;
@$Sequence = $request->sequence;
@$sequenceStatus = $request->sequence_status;
@$sentStatus = $request->sent_status;

@$newversionIs = $request->newversion_is;

@$receiveDate = date('Y-m-d');
@$receiveTime = date('H:i:s');

@$Year = $request->year_now;

$objsql_check = "SELECT depart_id
    FROM edoc_receive
    WHERE edoc_id = '$edocId'  
    AND depart_id = '$departId_receive' 
    AND depart_id_send = '$departId_send' 
    AND user_id = '$userId' 
    AND receive_date = '$receiveDate' 
    AND receive_time = '$receiveTime'
    ";
    
$objrs = mysqli_query($con, $objsql);
$numrow = mysqli_num_rows($objrs);

if($numrow == 0) { //เช็คว่ามีการลงรับหนังสือฉบับนี้แล้วหรือยัง

    /* Get number receive */
    //if($receiveNo_first == ''){
        $objsql = "SELECT receive_no FROM edoc_receive_no WHERE depart_id='$departId_receive'
            AND receive_year = '$Year'";
        $objrs = mysqli_query($con, $objsql);
        $objdata = mysqli_fetch_assoc($objrs);
        $receiveNo = $objdata['receive_no'];
        
        if($receiveNo == ''){
            $receiveNo = '1';
            $sql = "INSERT INTO edoc_receive_no (depart_id, receive_no, receive_year)
                values('$departId_receive', '$receiveNo', '$Year')";
        }else{
            $receiveNo = $receiveNo + 1;
            $sql = "UPDATE edoc_receive_no SET receive_no='$receiveNo', receive_year='$Year'
                WHERE depart_id='$departId_receive'";
        }
        
        $objrs2 = mysqli_query($con, $sql);//update and insert to table edoc_receive_no
        
    /*
    }else{
        $receiveNo = $receiveNo_first;
    }
    */

    $objsql = "INSERT INTO edoc_receive(receive_id, edoc_type_id, receive_no, receive_date, receive_time, edoc_id, user_id_send, depart_id_send, depart_id, pdf_name, pdf_path, user_id, status)
        VALUES(null,
        '$edocTypeId',
        '$receiveNo',
        '$receiveDate',
        '$receiveTime',
        '$edocId',
        '$userId_send',
        '$departId_send',
        '$departId_receive',
        '$fileName',
        '$filePath',
        '$userId',
        '1')";

    if(mysqli_query($con, $objsql)){
        $receive_id = mysqli_insert_id($con);
        $status = 1; //ลงรับสำเร็จ
        ## Update status edoc_sent table ##
        ## 1 = not confirm receive
        ## 2 = confirm received
        ## 3 = sent to department.
        ## 4 = confirm received sent to.
        if($sentStatus == '3')
            $sentStatus_set = '4';
        else
            $sentStatus_set = '2';
            
        $objsql = "UPDATE edoc_sent SET sent_status = '$sentStatus_set'
            WHERE edoc_id = '$edocId' AND depart_id = '$departId_receive' AND depart_id_send = '$departId_send'";
        mysqli_query($con, $objsql);
        
        ## Update process status sequence type.
        ## P = process next order.
        /*
        if($sequenceStatus == '1'){
        $sequenceNext = $Sequence + 1;
            $objsql2 = "UPDATE edoc_sent SET process_status = 'P'
                WHERE edoc_id = '$edocId'
                AND depart_id_send = '$departId_send'
                AND sequence = '$sequenceNext'";
            mysqli_query($con, $objsql2);
        }
        */

        ## Signature Add to sign main table##

        $main_id = 'xx';

        if($newversionIs == 'true'){
            
            $objsql_ma = "INSERT INTO sign_main SET 
                edoc_id = '$edocId', 
                depart_id = '$departId_receive',
                user_id = '$userId',
                create_date = '$receiveDate',
                create_time = '$receiveTime',
                doc_type = '2',
                doc_receive_no = '$receiveNo',
                main_type = '0'";
            if(mysqli_query($con, $objsql_ma)){
                // Get main id
                $main_id = mysqli_insert_id($con);
            }

        }

        ## edoc track and log ##
        $operation = "รับหนังสือ";
        $ip_addr = $_SERVER['REMOTE_ADDR'];
        $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
            VALUES(null, '$edocId', '$receiveTime', '$receiveDate', '$operation', '$departId_receive', '$userId', '$ip_addr', '3')";
        mysqli_query($con, $objsql_track);
            
    
        
    }else{
        $status = 0; //ลงรับไม่สำเร็จ
    }
}// check numrow
else {
    $status = 2; //ลงรับแล้ว
    $main_id = '';
    $receive_id = '';
}


$data[] = array(
    'status'=>$status,
    'departid_receive'=>$departId_receive,
    'mainId'=>$main_id,
    'receiveId'=>$receive_id
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>