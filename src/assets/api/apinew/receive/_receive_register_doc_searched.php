<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

/* Location */
@$location = '../../document/edoc/';

@$edocId = $_POST[edocid];
@$departId = $_POST[departid];//หน่วยงานรับหนังสือ
@$departName_receive = $_POST[departname_receive]; //ชื่อหน่วยงานรับหนังสือ
@$dateWrite = $_POST[datewrite];
@$userId = $_POST[userid];//ผู้รับหนังสือ

@$newversionIs = $_POST[newversion_is];

@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');
@$Year = $_POST[year_now];

$objsql = " SELECT 
    edoc_id,
    doc_no,
    edoc_type_id,
    doc_date,
    sent_date,
    sent_time,
    headline,
    receiver,
    comment,
    secrets,
    rapid,
    depart_id,
    sender_depart,
    user_id,
    sender,
    edoc_datewrite,
    destroy_year 
    FROM edoc 
    WHERE edoc_id = '$edocId'";
   
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);  

$docTypeId = $objdata['edoc_type_id'];
$sendNo = $objdata['doc_no'];
$sendUserId = $objdata['user_id'];
$sendDepartId = $objdata['depart_id'];
$receiveDepartId = $departId;

list($departNameReceive, $departParentReceive) = getDepartment($receiveDepartId); //ชื่อหน่วยงานรับ
            /*
            @$docYear = date('Y');

            @$folder_depart = 'D00'.$sendDepartId.'/'.$docYear;
            $structure = $location.''.$folder_depart.'/';
            if (!file_exists($structure)) {
                if (!mkdir($structure, 0777, true)) {
                    die('Failed to create folders...');
                }
            }

            @$location_new = $structure;

            $dateArray = explode("/",$docDate);
            $docDateNew = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];
                
            @$filename = $_FILES['file']['name'][0];
            @$filetemp = $_FILES['file']['tmp_name'][0];
    
            if(strpos($filename, '_esign_') !== false) {
                $strFileName = explode('_esign_',$filename);
                $filename = $strFileName[1];
            }else if(strpos($filename, '_edoc_') !== false) {
                $strFileName = explode('_edoc_',$filename);
                $filename = $strFileName[1];
            }
            
            $numberSend = explode("/",$sendNo);
            @$date_sent_no = date('Y-m-d').'-'.$numberSend[1];
            
            //@$new_filename = 'DOC'.$sendDepartId.'D'.round(microtime(true)).'_edoc_'. $filename;
            @$new_filename = 'DOC'.$receiveDepartId.'D'.round(microtime(true)).'_edoc_'. $date_sent_no.'.pdf';
            //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename);
            @$name_encode=rawurlencode($new_filename);
            @$path = '/edoc/'.$folder_depart.'/'.$name_encode;
            // Upload file    
            if(move_uploaded_file($filetemp,$location_new.$new_filename)){
            */
                $objsql_sent = "INSERT INTO edoc_sent(
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
                    sent_status)
                    VALUES(
                    null,
                    '$sendNo',
                    '$sentDate',
                    '$sentTime',
                    '$edocId',
                    '$sendUserId',
                    '$sendDepartId',
                    '$receiveDepartId',
                    '$departNameReceive',
                    '$filename',
                    '$path',
                    '2')";
                if(mysqli_query($con, $objsql_sent)){
                    // ออกเลขรับและข้อมูลการรับหนังสือ //
                    $objsql = "SELECT receive_no FROM edoc_receive_no WHERE depart_id='$receiveDepartId' AND receive_year = '$Year'";
                    $objrs = mysqli_query($con, $objsql);
                    $objdata = mysqli_fetch_assoc($objrs);
                    
                    //Get number receive 
                    $No = $objdata['receive_no'];
                    
                    if($No == ''){
                        $receiveNo = '1';
                        $objsql_receiveNo = "INSERT INTO edoc_receive_no (depart_id, receive_no, receive_year)
                        values('$receiveDepartId', '$receiveNo', '$Year')";
                    }else{
                        $receiveNo = $No + 1;
                        $objsql_receiveNo = "UPDATE edoc_receive_no SET receive_no='$receiveNo', receive_year='$Year' WHERE depart_id='$receiveDepartId'";
                    }
                    mysqli_query($con, $objsql_receiveNo);
                            
                    @$receiveDate = date('Y-m-d');
                    @$receiveTime = date('H:i:s');
                    
                    $objsql = "INSERT INTO edoc_receive(receive_id, edoc_type_id, receive_no, receive_date, receive_time, edoc_id, user_id_send, depart_id_send, depart_id, pdf_name, pdf_path, user_id, receive_type, status)
                        VALUES(null,
                        '$docTypeId',
                        '$receiveNo',
                        '$receiveDate',
                        '$receiveTime',
                        '$edocId',
                        '$sendUserId',
                        '$sendDepartId',
                        '$receiveDepartId',
                        '$filename',
                        '$path',
                        '$userId',
                        '1',
                        '1')";
                        
                    if(mysqli_query($con, $objsql)){
                        $status = "true";
                        $receiveId = mysqli_insert_id($con);
                        // update สถานะส่งหนังสือ
                        $objsql_status = "UPDATE edoc SET status = '1' WHERE edoc_id = '$edocId'";
                        mysqli_query($con, $objsql_status);
                    }

                    ## Signature Add to sign main table##
                    
                    $main_id = 'xx';

                    if($newversionIs == 'true'){
                        
                        $objsql_ma = "INSERT INTO sign_main SET 
                            edoc_id = '$edocId', 
                            depart_id = '$receiveDepartId',
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
                    $operation = "รับหนังสือจากภายนอก";
                    $ip_addr = $_SERVER['REMOTE_ADDR'];
                    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
                        VALUES(null, '$edocId', '$receiveTime', '$receiveDate', '$operation', '$receiveDepartId', '$userId', '$ip_addr', '5')";
                    mysqli_query($con, $objsql_track);
                }
            /*
            }//end move upload file.
            */
        

$data[] = array(
    'status'=>$status,
    'departid'=>$sendDepartId,
    'datewrite'=>$dateWrite,
    'userid'=>$userId,
    'docno'=>$docNo,
    'sendno'=>$sendNo,
    'edocid'=>$edocId,
    'receiveId'=>$receiveId,
    'resp'=>$new_filename,
    'mainId'=>$main_id
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>