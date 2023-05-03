<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/edoc/';

@$departId_edoc = $_POST["departid_edoc"];
@$receiveDept = $_POST["receive_dept"];
@$sentNo = $_POST["sentno"];
@$edocId = $_POST["edocid"];
@$folder_depart = 'D00'.$departId_edoc;
@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

@$userId = $_POST["userid"];

@$filePath = $_POST["file_path"];
@$sentId = $_POST["sentid"];
@$departId_receive = $_POST["departid_receive"];

@$Year = date('Y');

$structure = $location.''.$folder_depart.'/'.$Year.'/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;
// Looping all files

//for ( $i = 0;$i < count($_FILES['file']['name']);$i++ ){
    @$filename = $_FILES['file']['name'][0];
    @$filetemp = $_FILES['file']['tmp_name'][0];
    
    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }
    
    
    $numberSend = explode("/",$sentNo);
    @$date_sent_no = date('Y-m-d').'-'.$numberSend[1];
    
    //$new_filename =round(microtime(true)).'_'. $filename;
    //@$new_filename = 'DOC'.$depart_sentId.'D'.round(microtime(true)).'_edoc_'. $filename;
    @$new_filename = 'DOC'.$departId_receive.'D'.round(microtime(true)).'_edoc_'. $date_sent_no. '.pdf';
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); in Local
    @$name_encode=rawurlencode($new_filename);
    @$path = '/edoc/'.$folder_depart.'/'.$Year.'/'.$name_encode;

    // Upload file    
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ // Or $new_filename2 in local

        $objsql = "UPDATE edoc_sent SET 
            pdf_name = '$filename',
            pdf_path = '$path'
            WHERE sent_id = '$sentId';
        ";

        if(mysqli_query($con, $objsql)){
            $status = 'true';

            // update table receive
            $objsql_r = "UPDATE edoc_receive SET 
                pdf_name = '$filename',
            pdf_path = '$path'
            WHERE edoc_id = '$edocId' AND depart_id_send = '$departId_edoc' AND depart_id = '$departId_receive'
            ";
            if(mysqli_query($con,$objsql_r)){
                //delete file old
                @$pathOld = "../../document".$filePath;
                @$path_edoctemp = iconv("UTF-8","TIS-620",$pathOld);
                $path_edoctemp = $pathOld;
                
                @unlink($path_edoctemp);
                
                $status = 'true'; 

            }

        }
        
    }else{
        $status = 'false';
    }
    
    if($status == 'true'){
        $objsql = "UPDATE edoc SET status='1' WHERE edoc_id = '$edocId'";
        mysqli_query($con, $objsql);
        
        ## edoc track and log ##
        $operation = "แก้ไขหนังสือ";
        $detail = 'ส่งถึง '.$receiveDept;
        $ip_addr = $_SERVER['REMOTE_ADDR'];
        $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, detail, depart_id, user_id, ip, status)
            VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$detail', '$departId_edoc', '$userId', '$ip_addr', '9')";
        mysqli_query($con, $objsql_track);
    }
//}

$data[] = array(
    'status'=>$status,
    'sentid'=>$sentId,
    'pdfname'=>$filename,
    'edocid'=>$edocId,
    'validFile'=>1,
    'resp'=>$objsql_r
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
