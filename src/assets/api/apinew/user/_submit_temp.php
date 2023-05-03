<?php
session_start();
require('../db.php');
//date_default_timezone_set("Asia/Bangkok");
/* Location */
$location = '../../document/submit/';

// Count total files
$countfiles = count($_FILES['file']['name']);
$facCode_Send = $_POST['facCode_Send'];
$user = $_POST["User"];
$facCode = $_POST["facCode"];
$docNo = $_POST["documentNo"];
$dateSubmitDoc = $_POST["dateSubmitDoc"];
//$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
//$tempId = $facCode.'U'.$user.$stampdate.'-F'.$facCode_Send;
$stampdate = strtotime($_POST["dateSubmitDoc"]);
$tempId = 'ID'.$stampdate.'U'.$user;
// Looping all files
for ( $i = 0;$i < $countfiles;$i++ ){
    $filename = $_FILES['file']['name'][$i];  
    //$new_filename =round(microtime(true)).'_'. $filename;
    $new_filename ='F'.$facCode_Send.'U'.$user.round(microtime(true)).'_'. $filename;
    $path = '/submit/'.$new_filename;
    // Upload file    
    if(move_uploaded_file($_FILES['file']['tmp_name'][$i],$location.$new_filename) ){
        $objsql = "INSERT INTO tbsubmit_temp(
                submittemp_id,
                submittemp_docNo,
                submittemp_fac,
                submittemp_pathfile,
                submittemp_filename,
                submittemp_user,
                submittemp_date,
                submittemp_doctime,
                status)
                VALUES('$tempId', '$docNo', '$facCode_Send', '$path', '$filename', '$user','".date('Y-m-d h:i:s')."', '$dateSubmitDoc','0')";
        mysqli_query($con, $objsql);
        $status = 'true';
    }else{
        $status = 'false';
    }
}

$arr = array(
    'status'=> $status,
    'docNo'=> $docNo,
    'filename' => $filename,
    'facultyName' =>$facCode_Send,
    'tempsubmitId'=>$tempId,
    'dateSubmitTime'=> $dateSubmitDoc,
    'validFile'=>1
);

echo json_encode($arr);