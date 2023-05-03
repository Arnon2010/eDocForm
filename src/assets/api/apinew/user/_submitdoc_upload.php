<?php
session_start();
require('../db.php');
//date_default_timezone_set("Asia/Bangkok");
/* Location */
$location = '../../submit_fileupload/';

// Count total files
$countfiles = count($_FILES['file']['name']);
$facCode_Send = $_POST['facCode_Send'];
$user = $_POST["User"];
$facCode = $_POST["facCode"];
$dateSubmitDoc = $_POST["dateSubmitDoc"].'-U'.$user;
$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
$tempId = $facCode.'U'.$user.$stampdate.'-F'.$facCode_Send;
// Looping all files
for ( $i = 0;$i < $countfiles;$i++ ){
    $filename = $_FILES['file']['name'][$i];  
    $new_filename =round(microtime(true)).'_'. $filename;
    $path = '/submit_fileupload/'.$new_filename;
    // Upload file    
    if(move_uploaded_file($_FILES['file']['tmp_name'][$i],$location.$new_filename) ){
        $objsql = "INSERT INTO tbtempsubmit(
                tempsubmit_id,
                tempsubmit_faccode,
                tempsubmit_pathfile,
                tempsubmit_filename,
                tempsubmit_user,
                tempsubmit_date,
                tempsubmit_doctime,
                status)
                VALUES('$tempId', '$facCode_Send', '$path', '$filename', '$user','".date('Y-m-d')."', '$dateSubmitDoc','0')";
        mysqli_query($con, $objsql);
        $status = 'true';
    }else{
        $status = 'false';
    }
}

$arr = array(
    'status'=> $status,
    'filename' => $new_filename,
    'facultyName' =>$facCode_Send,
    'tempsubmitId'=>$tempId,
    'dateSubmitTime'=> $dateSubmitDoc
);

echo json_encode($arr);