<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/edoc/';

@$edocId = $_POST["edocid"];
@$userId = $_POST["userid"];
@$departId_send = $_POST["departid_send"];
@$Time = $_POST["time"];
@$statusSequence = $_POST['status_sequence'];

//สถานะส่งหนังสือตัวจริง
// if($_POST["status_sent_doc"] == 'true')
//     @$statusSentDoc = 1;
// else
//     @$statusSentDoc = 0;

@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

@$docYear = date('Y');

$objsql = "SELECT temp_id, sent_no, edoc_id, depart_id, receive_dept, user_id, pdf_name, pdf_path, temp_sent_status
    FROM edoc_sent_temp
    WHERE edoc_id = '$edocId' 
    AND user_id='$userId' 
    AND temp_timewrite='$Time'
    ORDER BY temp_run_id ASC";
$objres = mysqli_query($con, $objsql);
$objrow = mysqli_num_rows($objres);
$No = 0;//ลำดับการส่งต่อ หรือเกษียน
if($objrow == 0){
    @$row = 0;
}else{
    while($objdata = mysqli_fetch_array($objres)){
        $No++;
        if($statusSequence == '1'){ //เลือกเกษียนแบบกำหนดลำดับ
        $objsql_seqn = "SELECT sequence FROM retire_sequence
            WHERE edoc_id='$objdata[edoc_id]'
            AND depart_send_id = '$departId_send'";
        $objres_seqn= mysqli_query($con,$objsql_seqn);
        $rowSeqn = mysqli_num_rows($objres_seqn);
        
        if($rowSeqn != 0){
            $objmax_seqn = "SELECT edoc_id, MAX(sequence) as maxSequence
            FROM retire_sequence
            WHERE edoc_id='$objdata[edoc_id]'
            AND depart_send_id = '$departId_send'
            GROUP BY edoc_id";
            $objres_max= mysqli_query($con, $objmax_seqn);
            $objdata_max = mysqli_fetch_array($objres_max);
            $sequence = $objdata_max['maxSequence'] + 1;
      
        }else{
            $sequence = $No;
        }
        
        if($sequence == '1')
            $processStatus = 'P'; //เกษียนได้
        else
            $processStatus = 'W'; //รอการเกษียนจากลำดับก่อนหน้า
            
            
        ## get sequence max ##    
        $objsql_max2 = "SELECT s1.sequence, s1.sent_id, s1.sent_status, s1.process_status
        FROM edoc_sent s1
          INNER JOIN
          (
            SELECT edoc_id, MAX(sequence) as MaxSeqNO
            FROM edoc_sent
            WHERE edoc_id = '$objdata[edoc_id]'
            AND depart_id_send = '$departId_send'
            GROUP BY edoc_id
          ) s2
          ON s2.edoc_id = s1.edoc_id
        WHERE s2.MaxSeqNO = s1.sequence";
        $objres_max2 = mysqli_query($con, $objsql_max2);
        $objdata_max2 = mysqli_fetch_array($objres_max2);
        
        if($objdata_max2['sent_status'] == '4' && $objdata_max2['process_status'] == 'P')
            $processStatus = 'P'; //ลับก่อนหน้าได้เกษียนไปแล้ว กำหนดให้เป็นลำดับถัดไป
        
        }else{
            $processStatus = 'P';
        }
        
        
            
        ## Upload file to Folder  depart ##
        @$folder_depart = 'D00'.$objdata['depart_id'].'/'.$docYear;
        @$structure = $location.''.$folder_depart.'/';
        if (!file_exists($structure)) {
            if (!mkdir($structure, 0777, true)) {
                die('Failed to create folders...');
            }
        }
        @$location_new = $structure;
        
        //@$new_filename =round(microtime(true)). '_'.$objdata['pdf_name'];
        //@$new_filename = 'DOC'.$objdata['depart_id'].'D'.round(microtime(true)).'_edoc_'. $objdata['pdf_name'];
        //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); //in local
        //@$name_encode=rawurlencode($new_filename);
        //@$pdf_path = '/edoc/'.$folder_depart.'/'.$name_encode;
        
        /* Location edoc sent temp */
    
        //@$pathNew = $subPath[0].'_edoc_'.$objdata['pdf_name'];
        @$Path = $objdata['pdf_path'];// $pathNew = '/edocsenttemp/DOC...'; 
        //@$path_edocsenttemp = iconv("UTF-8","TIS-620",$Path);
        $path_edoc_retired = $Path;
        
        //@copy ("../../edocsenttemp/".$objdata['file_name'], $location_new.$new_filename2);
        //if(@copy($path_edoc_retired, $location_new.$new_filename)){ // $new_filename2 Or in local
        
            $objsql2 = "INSERT INTO edoc_sent(
                sent_id,
                sent_no,
                sequence,
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
                process_status,
                status_sent_doc
                )VALUES(
                null,
                '$objdata[sent_no]',
                '$sequence',
                '$sendtoDate',
                '$sendtoTime',
                '$objdata[edoc_id]',
                '$objdata[user_id]',
                '$departId_send',
                '$objdata[depart_id]',
                '$objdata[receive_dept]',
                '$objdata[pdf_name]',
                '$path_edoc_retired',
                '3',
                '$statusSequence',
                '$processStatus',
                '$objdata[temp_sent_status]'
                )";
                
            if(mysqli_query($con, $objsql2)){
                if($statusSequence == '1'){
                    $objins_seqn  = "INSERT INTO retire_sequence (edoc_id, sequence, depart_send_id, depart_id, status
                        )VALUES('$objdata[edoc_id]', '$sequence', '$departId_send', '$objdata[depart_id]', '0')";
                    $objres_ins = mysqli_query($con, $objins_seqn);
                }
                $objsql3 = "DELETE FROM edoc_sent_temp
                    WHERE temp_id = '$objdata[temp_id]'";
                if(mysqli_query($con, $objsql3)){
                    $status = 'true';
                    //@unlink($path_edocsenttemp);
                }
                
                ## edoc track and log ##
                $operation = "ส่งต่อหนังสือ";
                $ip_addr = $_SERVER['REMOTE_ADDR'];
                $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
                    VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$departId_send', '$userId', '$ip_addr', '5')";
                mysqli_query($con, $objsql_track);
            }
        //}//.copy file
    }
    @$row = $objrow;

}

$data[] = array(
    'status'=>$status,
    'numrow'=>$row
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
