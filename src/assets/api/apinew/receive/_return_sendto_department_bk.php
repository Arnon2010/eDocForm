<?php
    require('../db.php');
    $request = json_decode(file_get_contents("php://input"));
    date_default_timezone_set("Asia/Bangkok");
   
    @$sentId = $request->sent_id;
    @$userId = $request->user_id;
    @$departId_send = $request->departid_send;
    @$departId_receive = $request->departid_receive;
    @$departName_receive = $request->departname_receive;
   
    @$sendtoDate = date('Y-m-d');
    @$sendtoTime = date('H:i:s');

    @$docYear = date('Y');

    $objsql = "SELECT * FROM edoc_sent WHERE sent_id = '$sentId'";
    $objres = mysqli_query($con, $objsql);
    $objrow = mysqli_num_rows($objres);

    
    $objdata = mysqli_fetch_array($objres);
                
    $objsql2 = "INSERT INTO edoc_sent(
                sent_id,
                sent_no,
                date_sendto,
                time_sendto,
                edoc_id,
                user_id,
                depart_id_send,
                depart_id,
                receive_dept,
                pdf_name,
                pdf_path,
                sent_status,
                sequence_status,
                process_status
                )VALUES(
                null,
                '$objdata[sent_no]',
                '$sendtoDate',
                '$sendtoTime',
                '$objdata[edoc_id]',
                '$userId',
                '$departId_send',
                '$departId_receive',
                '$departName_receive',
                '$objdata[pdf_name]',
                '$objdata[pdf_path]',
                '3',
                '2',
                'P')";
                
    if(mysqli_query($con, $objsql2)){
                                
        ## edoc track and log ##
        $operation = "ส่งหนังสือย้อนกลับ";
        $ip_addr = $_SERVER['REMOTE_ADDR'];
        $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
             VALUES(null, '$objdata[edoc_id]', '$sendtoTime', '$sendtoDate', '$operation', '$departId_send', '$userId', '$ip_addr', '9')";
        mysqli_query($con, $objsql_track);
        $status = 'true';
    }
    
$data[] = array(
    'status'=>$status,
    'resp'=>$objsql2
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
