<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");


@$userId = $_POST["user_id"];//รหัสผู้ใช้
@$edocId = $_POST["edoc_id"];//รหัสหนังสือ
@$departId = $_POST['depart_id'];//รหัสหน่วยงานของหนังสือ

@$mainId = $_POST['main_id'];//รหัส sign main
@$tpositionId = $_POST['tposition_id']; //รหัสผู้เซ็น   ** เลือกเกษียนแบบปกติ

@$uploadDate = date('Y-m-d H:i:s');

@$docYear = date('Y');

@$timeAction = date('Y-m-d H:i:s');

/* Location */
@$location = '../../document/';

$path_approv_not_signed = 'approv_not_signed/D00'.$departId.'/'.$docYear.'/';
$path = $location.$path_approv_not_signed;
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
}



// Count total files
$countfiles = count($_FILES['file']['name']);

// Upload file
if($countfiles == 0) {
    $validFile = 0;//'No file upload'
}else{
    $validFile = 1;
}

// Looping all files
$no = 0;
for ( $i=0;$i < $countfiles; $i++ ){
    
    $no++;

    //$filename = date('YmdHis').'_D'.$mainId.'No'.$i.'_'.$_FILES['file']['name'][$i];

    $filename = $_FILES['file']['name'][$i];
    $filetemp = $_FILES['file']['tmp_name'][$i];

    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }

    $ext = '.'.pathinfo($filename, PATHINFO_EXTENSION);
    $t=time();

    //$generatedName = date('Y-m-d').'_NO'.$no.'_edoc_'.md5($t.$filename).'_'.$t.'t'.$tpositionId.'m'.$mainId.$ext;

    $generatedName = 'DOC'.$tpositionId.'_'.md5($t.$filename.$tpositionId.$mainId).'_edoc_'.date('Y-m-d').'-'.$t.'-M'.$mainId.$ext;

    // path approv not signed
    $filePath_not_signed = $path.$generatedName;
    $file_path = '/'.$path_approv_not_signed.$generatedName;

    $filename_new = 'DOC'.round(microtime(true)).$no.'_edoc_'.$filename;

    if(move_uploaded_file($filetemp, $filePath_not_signed) ){ 
                
        $objsql = "INSERT INTO sign_detail(
            detail_id,
            main_id,
            tposition_id,
            file_name,
            file_path,
            sign_status,
            upload_date
            )
            VALUES(null,'$mainId', '$tpositionId', '$filename_new', '$file_path', '0','$uploadDate')";
        if(mysqli_query($con, $objsql)){

            $detailId = mysqli_insert_id($con);

            // Add to sign move
            $sql_move = "INSERT INTO sign_move SET 
                main_id = '$mainId',
                detail_id = '$detailId',
                tposition_id = '$tpositionId',
                activity = 'Upload',
                user_id = '$userId',
                time = '$timeAction'
                ";
            mysqli_query($con, $sql_move);

            $status = 'true';
        }
        
    }else{
            $status = 'false';
        
    }
  
}


$data[] = array(
    'status'=>$status,
    'mainId'=>$mainId,
    'edocid'=>$edocId,
    'departId'=>$departId,
    'validFile'=>$validFile,
    'detailId'=>$detailId,
    'resp'=>'sign received'
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
