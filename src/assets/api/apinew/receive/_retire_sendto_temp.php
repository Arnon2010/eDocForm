<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
//date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/edoctemp/';

@$userId = $_POST["userid"];
@$depart_sentId = $_POST["depart_sent_id"];
@$timeWrite = $_POST["timewrite"];
@$edocId = $_POST["edocid"];
@$receiveDept = $_POST['receive_dept'];
//@$sentNo = getsendNo($edocId);

@$docYear = date('Y');

$objsql = "SELECT distinct sent_no FROM edoc_sent WHERE edoc_id='$edocId'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
@$sentNo = $objdata['sent_no'];

@$folder_depart = 'D00'.$depart_sentId.'/'.$docYear;


//$structure = $location.''.$folder_depart.'/';
$structure = $location.''.$folder_depart.'/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;
@$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
@$tempId = $depart_sentId.'D00'.$userId.'U'.$stampdate;
//$stampdate = strtotime($_POST["timewrite"]);
//$tempId = $userId.'T'.$stampdate;
// Looping all files

//for ( $i = 0;$i < count($_FILES['file']['name']);$i++ ){
    @$filename = $_FILES['file']['name'][0];
    @$filetemp = $_FILES['file']['tmp_name'][0];
    
    //$new_filename =round(microtime(true)).'_'. $filename;
    @$new_filename = 'DOC'.$depart_sentId.'D'.round(microtime(true)).'_'. $filename;
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); Or in local
    @$name_encode=rawurlencode($new_filename);
    @$pdf_path = '/edoctemp/'.$folder_depart.'/'.$name_encode;

    // Upload file    
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ // $new_filename2  in local
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
                VALUES('$tempId','$sentNo', '$edocId', '$depart_sentId', '$receiveDept', '$userId','$filename', '$pdf_path','$dateImport','$timeWrite','0')";
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
    'sentNo'=>$sentNo,
    'validFile'=>1
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
