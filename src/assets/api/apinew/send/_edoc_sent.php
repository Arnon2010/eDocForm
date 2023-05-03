<?php
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

/* Location */
@$location = '../../document/edoc/';

@$edocId = $_POST["edoc_id"];
@$departId = $_POST["departid"];//รหัสหน่วยงานของผูส่ง
@$userId = $_POST["userid"];
@$Time = $_POST["time"];
if($_POST["status_sent_doc"] == 'true')
    @$statusSentDoc = 1;
else
    @$statusSentDoc = 0;

@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

@$docYear = date('Y');

$objsql = "SELECT temp_id, sent_no, edoc_id, depart_id, receive_dept, user_id, pdf_name, pdf_path
    FROM edoc_sent_temp  WHERE edoc_id = '$edocId'";
$objres = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_array($objres)){
    
    $folder_depart = 'D00'.$departId.'/'.$docYear;
    $structure = $location.''.$folder_depart.'/';
    if (!file_exists($structure)) {
        if (!mkdir($structure, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    @$location_new = $structure;
    @$subPath = explode('_edoc_',$objdata['pdf_path']);
    //@$new_filename =round(microtime(true)). '_'.$objdata['pdf_name'];
    @$new_filename = 'DOC'.$objdata['depart_id'].'D'.round(microtime(true)).'_edoc_'. $subPath[1];
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); in localhost
    @$name_encode=rawurlencode($new_filename);
    @$path = '/edoc/'.$folder_depart.'/'.$name_encode;
        
    //Path from edoc temp
    //@$location = '../../document';
    
    @$path_edoctemp = "../../document".$objdata['pdf_path'];// $pathNew = '/edoctemp/DOC...';
    //@$path_edoctemp = iconv("UTF-8","TIS-620",$Path); use in local.    
    if(@copy($path_edoctemp, $location_new.$new_filename)){ // Or $new_filename in locolhost
    
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
            status_sent_doc)
            VALUES(
            null,
            '$objdata[sent_no]',
            '$sendtoDate',
            '$sendtoTime',
            '$objdata[edoc_id]',
            '$userId',
            '$departId',
            '$objdata[depart_id]',
            '$objdata[receive_dept]',
            '$objdata[pdf_name]',
            '$path',
            '1',
            '$statusSentDoc')";
        if(mysqli_query($con, $objsql2)){
            $objsql3 = "DELETE FROM edoc_sent_temp
                    WHERE temp_id = '$objdata[temp_id]'";
            if(mysqli_query($con, $objsql3)){
                $status = 'true';
                @unlink($path_edoctemp);
                
            }
        }
    }
}

if($status == 'true'){
    $objsql = "UPDATE edoc SET status='1' WHERE edoc_id = '$edocId'";
    mysqli_query($con, $objsql);
    
    ## edoc track and log ##
    if($statusSentDoc == '1')
        $operation = "อยู่ระหว่างการส่งหนังสือ (ฉบับจริง)";
    else
        $operation = "อยู่ระหว่างการส่งหนังสือ";

    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$departId', '$userId', '$ip_addr', '2')";
    mysqli_query($con, $objsql_track);
    
    
}

$data[] = array(
    'status'=>$status,
    'pathTemp'=>$Path,
    'folderPath'=>$folder_depart,
    'pathNew'=>$pathNew
);

print json_encode(array("data"=>$data));
