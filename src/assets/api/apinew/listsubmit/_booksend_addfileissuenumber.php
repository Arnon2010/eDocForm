<?php
session_start();
require('../db.php');
$facCode = $_POST['faccode'];
$offerId = $_POST['offerid'];
$orderNo = $_POST['order'];
$bsendNumber = $_POST['bsendNumber'];
$userAdd = $_POST['useradd'];
$writeTime = $_POST['writetime'];

/* Location */
$location = '../../document/booksend/';
$countfiles = count($_FILES['file']['name']); // Count total files
for ( $i = 0;$i < $countfiles;$i++ ){
    $filename = $_FILES['file']['name'][$i];  
    $new_filename ='WND-OID'.$offerId.'-'.round(microtime(true)).'_'. $filename;
    $path = '/booksend/'.$new_filename;
    // Upload file
    
    if(move_uploaded_file($_FILES['file']['tmp_name'][$i],$location.$new_filename) ){
        
        // Insert to table tbtemp_offerbooksend
        $objsql = "INSERT INTO tbissuenumber_temp(
                    isunumber_id,
                    isunumber_date,
                    offerdoc_id,
                    order_No,
                    isunumber_filepath,
                    isunumber_filename,
                    isunumber_writetime,
                    user_add,
                    faccode,
                    status)
                VALUES(
                     null,
                    '".date('Y-m-d H:i:s')."',
                    '$offerId',
                    '$orderNo',
                    '$path',
                    '$filename',
                    '$writeTime',
                    '$userAdd',
                    '$facCode',
                    '0'
                )";
        mysqli_query($con, $objsql);
        $status = 'true';
    }else{
        $status = 'false';
    }
    
}

$arr = array(
    'status'=> $status,
    'path'=> $path,
    'writetime'=> $writeTime
);

echo json_encode($arr);
