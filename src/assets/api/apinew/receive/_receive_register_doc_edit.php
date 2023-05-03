<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$location = '../../document/edoc/';

@$filePath = $_POST['file_path'];
@$edocId = $_POST['edocid'];
@$docNo = $_POST['docno'];
@$docDate = $_POST['docdate'];
@$docTypeId = $_POST['doctypeid'];
@$userId = $_POST['userid'];//user login send
@$senderDepart = $_POST['sender_depart'];// ชื่อของหน่วยงานส่วนราชการ ภายนอก
@$Sender = $_POST['sender'];// ผู้ส่ง
@$Secrets = $_POST['secrets'];
@$Rapid = $_POST['rapid'];
@$receiveDepartId = $_POST['receivedept_id']; //หน่วยงานรับ
@$sendDepartId = $_POST['senddept_id']; //จากหน่วยงาน
@$Comment = $_POST['comment'];
@$Headline = $_POST['headline'];
@$Receiver = $_POST['receiver'];
@$dateWrite = date('Y-m-d h:i:s');;
@$fromDepartId_old = $_POST['from_departid_old'];

@$destroyYear = $_POST['destroy_year']; // รหัสหน่วยงานก่อนแก้ไข  (รหัสหน่วยงานที่ส่งหนังสือ)

@$sentDate = date('Y-m-d');
@$sentTime = date('h:i:s');

@$Year = $_POST['year_now'];

if($sendDepartId == '1'){//หน่วยงานภายนอก
    $departSenderText = $senderDepart;//ชื่อของหน่วยงานภายนอก
}else{
    list($departName, $departParent) = getDepartment($sendDepartId);
    $departSenderText = $departName;
}



@$dateArray = explode("/",$docDate);
@$docDateNew = ($dateArray[2]-543).'-'.$dateArray[1].'-'.$dateArray[0];


/*
$sentDateStr = explode("/",$sentDate);
@$sentDateNew = ($sentDateStr[2]-543).'-'.$sentDateStr[1].'-'.$sentDateStr[0];
*/
$objsql = "UPDATE edoc SET
    doc_no = '$docNo',
    edoc_type_id = '$docTypeId',
    secrets = '$Secrets',
    rapid = '$Rapid',
    depart_id = '$sendDepartId',
    sender_depart = '$departSenderText',
    doc_date = '$docDateNew',
    sent_date = '$sentDate',
    sent_time = '$sentTime',
    headline = '$Headline',
    receiver = '$Receiver',
    comment = '$Comment',
    user_id = '$userId',
    sender = '$Sender',
    edoc_datewrite = '$dateWrite',
    destroy_year = '$destroyYear'
    WHERE edoc_id = '$edocId'";

if(mysqli_query($con, $objsql)){
    $status = "true";
    @$docYear = date('Y');
    @$folder_depart = 'D00'.$sendDepartId.'/'.$docYear;
    $structure = $location.''.$folder_depart.'/';
    if (!file_exists($structure)) {
        if (!mkdir($structure, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    
    @$location_new = $structure;
    
    @$filetemp = $_FILES['file']['tmp_name'][0];
    @$filename = $_FILES['file']['name'][0];
    
    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }
    
    @$numberSend = explode("/",$docNo);
    @$date_sent_no = date('Y-m-d').'-'.$numberSend[1];
    @$new_filename = 'DOC'.$sendDepartId.'D'.round(microtime(true)).'_edoc_'. $date_sent_no.'.pdf';
    @$name_encode=rawurlencode($new_filename);
    @$filePathNew = '/edoc/'.$folder_depart.'/'.$name_encode;

    if($filePath == '' || $filePath == 'null'){
        $resp_test = 'file null';
    }
    
    if(($filePath != '' || $filePath != 'null') && $fromDepartId_old != $sendDepartId){ //file เดิม  แต่หน่วยงานเปลี่ยน
        @$Path = "../../document".$filePath;
        $pathDoc = $Path;
        $resp = 'copy file old !=';
        
        // 1. copy file.
        if(@copy("../../document".$filePath, $location_new.$new_filename)){
            // 2. edit edoc_sent table.
            $resp = 'copy file';
            $objsql_receive = "UPDATE edoc_receive SET pdf_path = '$filePathNew',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            mysqli_query($con, $objsql_receive);
            
            $objsql_sent = "UPDATE edoc_sent SET pdf_path = '$filePathNew',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            if(mysqli_query($con, $objsql_sent)){
                @unlink($pathDoc);
                $resp = 'copy file';
            }else{
                $status = "false";
            }
        } 

    }else if($filePath == 'null' || $filePath == ''){

        // Upload file
        if(move_uploaded_file($filetemp,$location_new.$new_filename)){
            // 2. edit edoc_sent table.
            //$resp = 'move file';
            $objsql_receive = "UPDATE edoc_receive SET
                pdf_path = '$filePathNew',
                pdf_name = '$filename',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            mysqli_query($con, $objsql_receive);
                
            $objsql_sent = "UPDATE edoc_sent SET
                pdf_path = '$filePathNew',
                pdf_name = '$filename',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            if(mysqli_query($con, $objsql_sent)){
                $resp = 'move file';
            }else{
                $status = "false";
            }
        }// end move upload file.
    }
    
}else{
    $status = "false";
}

$data[] = array(
    'status'=>$status,
    'edocId'=>$edocId,
    'departId'=>$receiveDepartId,
    'resp'=>$resp_test,
    'filePath'=>$filePath
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>