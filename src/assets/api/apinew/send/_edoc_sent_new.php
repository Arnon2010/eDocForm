<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
//date_default_timezone_set("Asia/Bangkok");
/* Location */
/*
@$location = '../../document/edoctemp/';

@$userId = $_POST["userid"];
@$depart_sentId = $_POST["depart_sent_id"];
@$timeWrite = $_POST["timewrite"];
@$sentNo = $_POST["sentno"];
@$edocId = $_POST["edocid"];
@$receiveDept = $_POST["receive_dept"];//ชื่อหน่วยงานรับ
*/
@$location = '../../document/edoc/';

@$sentNo = $_POST["sentno"];
@$edocId = $_POST["edocid"];
@$depart_id_receive = $_POST["depart_id_receive"];///ส่งไปยังหน่วยงาน
@$receiveDept = $_POST["receive_dept"];//ชื่อหน่วยงานรับ
@$userId = $_POST["userid"];
@$departId = $_POST["departid"];//รหัสหน่วยงานผู้ส่งหนังสือ   ***
//@$Time = $_POST["time"];
@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

@$Year = date('Y');

@$folder_depart = 'D00'.$departId;
$structure = $location.''.$folder_depart.'/'.$Year;
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;
@$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
@$tempId = $depart_id_receive.'0'.$userId.'U'.$stampdate;
//$stampdate = strtotime($_POST["timewrite"]);
//$tempId = $userId.'T'.$stampdate;
// Looping all files

//for ( $i = 0;$i < count($_FILES['file']['name']);$i++ ){
    @$filename = $_FILES['file']['name'][0];
    @$filetemp = $_FILES['file']['tmp_name'][0];
    
    //$new_filename =round(microtime(true)).'_'. $filename;
    @$new_filename = 'DOC'.$depart_id_receive.'D'.round(microtime(true)).'_'. $filename;
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); use in local
    @$name_encode=rawurlencode($new_filename);
    @$path = '/edoc/'.$folder_depart.'/'.$name_encode;
    // Upload file    
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ // $new_filename2 use in local.
        @$dateImport = date('Y-m-d');
        $objsql = "INSERT INTO edoc_sent(
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
            '$sentNo',
            '$sendtoDate',
            '$sendtoTime',
            '$edocId',
            '$userId',
            '$departId',
            '$depart_id_receive',
            '$receiveDept',
            '$filename',
            '$path',
            '1')";
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
    'validFile'=>1,
    'timeWrite'=>$timeWrite
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
