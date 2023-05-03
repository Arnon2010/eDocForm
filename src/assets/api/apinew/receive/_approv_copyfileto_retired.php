<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));       
date_default_timezone_set("Asia/Bangkok");

/* Location retire */
@$location = '../../document/retire_signed/';

/* Location edoc */
@$location_edoc = '../../document/edoc/';

@$departId_receive = $_POST["depart_id_receive"];
@$edocId = $_POST["edocid"];
@$receiveNo = $_POST['receive_no'];

//ตรวจสอบไฟล์หนังสือต้นฉบับ
$sqlChkR = "SELECT rc.pdf_path, rc.depart_id_send, e.doc_no 
    FROM edoc_receive rc
    LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
    WHERE rc.edoc_id = '$edocId' 
    AND rc.receive_no = '$receiveNo' 
    AND rc.depart_id = '$departId_receive'";

$rsChkR = mysqli_query($con, $sqlChkR);
$dataChkR = mysqli_fetch_array($rsChkR);
$pdf_path_check = $dataChkR['pdf_path'];
$depart_id_send = $dataChkR['depart_id_send'];//หน่วยงานส่ง

@$docYear = date('Y');

if($pdf_path_check == ''){

    @$folder_depart_send = 'D00'.$depart_id_send.'/'.$docYear;
    $structure_send = $location_edoc.''.$folder_depart_send.'/';
    if (!file_exists($structure_send)) {
        if (!mkdir($structure_send, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    
    @$location_new_send = $structure_send;

    @$filetemp = $_FILES['file']['tmp_name'][0];
    @$filename = $_FILES['file']['name'][0];
    
    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }

    $sendNo = $dataChkR['doc_no'];

    $numberSend = explode("/",$sendNo);
    @$date_sent_no = date('Y-m-d').'-'.$numberSend[1];
    //@$new_filename = 'DOC'.$sendDepartId.'D'.round(microtime(true)).'_edoc_'. $filename;
    @$new_filename = 'DOC'.$depart_id_send.'D'.round(microtime(true)).'_edoc_'. $date_sent_no.'.pdf';
    @$name_encode=rawurlencode($new_filename);
    @$pdf_path_send = '/edoc/'.$folder_depart_send.'/'.$name_encode;

    // Upload file    
    if(move_uploaded_file($filetemp,$location_new_send.$new_filename) ){ // $new_filename2  in local
        $objsql = "UPDATE edoc_receive SET pdf_name = '$filename', pdf_path = '$pdf_path_send'
            WHERE edoc_id = '$edocId'
            AND depart_id = '$departId_receive' 
            AND receive_no = '$receiveNo'";
        if(mysqli_query($con, $objsql)){
            $status = 'true'; 
        }
    }else{
        $status = 'false';
    }
}


// upload ไฟล์เกษียน ใน folder retire_signed
@$folder_depart = 'D00'.$departId_receive.'/'.$docYear;
$structure = $location.''.$folder_depart.'/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;

//for ( $i = 0;$i < count($_FILES['file']['name']);$i++ ){
    
    @$filetemp = $_FILES['file']['tmp_name'][0];
    @$filename = $_FILES['file']['name'][0];
    
    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }

    $date_receive_no = date('Y-m-d').'-'.$receiveNo;
    //$encodeDateNumberR = base64_encode($date_receive_no);
    @$new_filename = 'DOC'.$departId_receive.'D'.round(microtime(true)).'_edoc_'.$date_receive_no.'.pdf';
    //@$new_filename = 'DOC'.$departId_receive.'D'.round(microtime(true)).'_edoc_'. $filename;
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); Or in local
    
    @$name_encode=rawurlencode($new_filename);
    @$pdf_path = '/retire_signed/'.$folder_depart.'/'.$name_encode;

    // Upload file    
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ // $new_filename2  in local
        $objsql = "UPDATE edoc_receive SET pdf_name_retire = '$filename', pdf_path_retire = '$pdf_path'
            WHERE edoc_id = '$edocId'
            AND depart_id = '$departId_receive'
            AND receive_no = '$receiveNo'";
        if(mysqli_query($con, $objsql)){
            $status = 'true'; 
        }
    }else{
        $status = 'false';
    }
//}

$data[] = array(
    'status'=>$status,
    'pdfnameRetire'=>$filename,
    'edocid'=>$edocId,
    'validFile'=>1,
    'resp'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
