<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/approv_not_signed/';

@$userId = $_POST["user_id"];//รหัสผู้ใช้
@$edocId = $_POST["edoc_id"];//รหัสผู้ใช้
@$departId = $_POST['depart_id'];//รหัสหน่วยงานของหนังสือ

@$mainId = $_POST['main_id'];//รหัส sign main
@$tpositionId_first = $_POST['tposition_id_first'];

@$uploadDate = date('Y-m-d H:i:s');

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
//@$tempId = $departId.'0'.$userId.'U'.$stampdate;

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
      
    @$date_doc_approv = date('Y-m-d').'-'.$stampdate;
    //$encodeDateNumberR = base64_encode($date_receive_no);
    //$new_filename =round(microtime(true)).'_'. $filename;
    $file_name = 'DOC'.$mainId.'D'.round(microtime(true)).'_esign_'. $filename;
    @$new_filename = 'DOC'.$mainId.'D'.round(microtime(true)).'_esign_'.$date_doc_approv.'.pdf';
    //@$new_filename2 = iconv("UTF-8","TIS-620",$new_filename); use in local
    @$name_encode=rawurlencode($new_filename);
    @$path = '/approv_not_signed/'.$folder_depart.'/'.$name_encode;
    // Upload file
    $validFile = 0;
    if(!file_exists($filetemp) || !is_uploaded_file($filetemp)) {
        $validFile = 0;//'No file upload'
    }else{
        $validFile = 1;
    }
    if(move_uploaded_file($filetemp,$location_new.$new_filename) ){ 
                
        $objsql = "INSERT INTO sign_detail(
                detail_id,
                main_id,
                tposition_id,
                file_name,
                file_path,
                sign_status,
                upload_date)
                VALUES(null,'$mainId', '', '$file_name', '$path', '0','$uploadDate')";
        if(mysqli_query($con, $objsql)){

            $objsql2 = "UPDATE sign_detail SET tposition_id = '$tpositionId_first' 
                WHERE main_id = '$mainId' AND sign_status = '0'";
            mysqli_query($con, $objsql2);

            $status = 'true';
        }
    }else{
        $status = 'false';
    }
//}

$data[] = array(
    'status'=>$status,
    'mainId'=>$mainId,
    'fileName'=>$file_name,
    'filePath'=>$path,
    'edocid'=>$edocId,
    'departId'=>$departId,
    'validFile'=>$validFile,
    'resp'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
