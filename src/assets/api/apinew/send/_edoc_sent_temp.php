<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/edoctemp/';

@$userId = $_POST["userid"];
@$departId = $_POST['departid'];//รหัสหน่วยงานของหนังสือ
@$depart_sentId = $_POST["depart_sent_id"];//รหัสหน่วยงานรับหนังสือ
@$receiveDept = $_POST["receive_dept"];//ชื่อหน่วยงานรับ
@$timeWrite = $_POST["timewrite"];
@$sentNo = $_POST["sentno"];
@$numberSend = $_POST["number_send"];
@$edocId = $_POST["edocid"];


@$docYear = date('Y');

@$folder_depart = 'D00'.$departId.'/'.$docYear;
$structure = $location.''.$folder_depart.'/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;
@$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
@$tempId = $depart_sentId.'0'.$userId.'U'.$stampdate;
//$stampdate = strtotime($_POST["timewrite"]);
//$tempId = $userId.'T'.$stampdate;
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
      
    @$date_sent_no = date('Y-m-d').'-'.$numberSend;
    //$encodeDateNumberR = base64_encode($date_receive_no);
    //$new_filename =round(microtime(true)).'_'. $filename;
    //@$new_filename = 'DOC'.$depart_sentId.'D'.round(microtime(true)).'_edoc_'. $filename;
    @$new_filename = 'DOC'.$depart_sentId.'D'.round(microtime(true)).'_edoc_'.$date_sent_no.'.pdf';
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); use in local
    @$name_encode=rawurlencode($new_filename);
    @$path = '/edoctemp/'.$folder_depart.'/'.$name_encode;
    // Upload file
    $validFile = 0;
    if(!file_exists($filetemp) || !is_uploaded_file($filetemp)) {
        $validFile = 0;//'No file upload'
    }else{
        $validFile = 1;
    }
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ // $new_filename2 use in local.
        @$dateImport = date('Y-m-d');
        $objsql = "INSERT INTO edoc_sent_temp(
                temp_id,
                sent_no,
                edoc_id,
                depart_id,
                receive_dept,
                user_id,
                pdf_name,
                pdf_path,
                temp_date,
                temp_timewrite,
                temp_status)
                VALUES('$tempId','$sentNo', '$edocId', '$depart_sentId', '$receiveDept', '$userId','$filename', '$path','$dateImport','$timeWrite','0')";
        if(mysqli_query($con, $objsql)){
            $status = 'true';
        }
    }else{
        $status = 'false';
    }
//}

$data[] = array(
    'status'=>$status,
    'tempid'=>$tempId,
    'pdfname'=>$filename,
    'edocid'=>$edocId,
    'validFile'=>$validFile,
    'timeWrite'=>$timeWrite
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
