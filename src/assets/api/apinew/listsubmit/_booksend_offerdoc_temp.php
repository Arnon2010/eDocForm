<?php
session_start();
require('../db.php');
$docNo = $_POST['docno'];
//$docNo = $docNo+1;
$submitdocId = $_POST['submitdocid'];
//$order_No = $_POST['order_no'];
$mainId = $_POST['main_id'];
$facSend = $_POST['facsend'];
$facReceive = $_POST['facreceive'];
$facIdReceive = $_POST['facIdreceive'];
$User = $_POST['userid'];
$docTime = $_POST['offerdoctime'];
$preferFaculty = $_POST['prefer'];

$stampdate = strtotime($docTime);
$tempId = 'ID'.$stampdate.'U'.$User;

/* Location */
$location = '../../document/offer_not_signatured/';
$countfiles = count($_FILES['file_offer']['name']); // Count total files
for ( $i = 0;$i < $countfiles;$i++ ){
    $filename = $_FILES['file_offer']['name'][$i];  
    $new_filename ='F'.$facIdReceive.'U'.$User.round(microtime(true)).'_'. $filename;
    $path = '/offer_not_signatured/'.$new_filename;
    // Upload file
    
    if(move_uploaded_file($_FILES['file_offer']['tmp_name'][$i],$location.$new_filename) ){
        
        // Insert to table tbtemp_offerbooksend
        $objsql = "INSERT INTO tbofferbooksend_temp(
                    tempoffer_id,
                    tempoffer_docNo,
                    tempoffer_date,
                    tempoffer_filepath,
                    tempoffer_filename,
                    submitmain_id,
                    tempoffer_doctime,
                    tempoffer_prefer,
                    fac_receive,
                    fac_send,
                    user,
                    submitdoc_id,
                    status)
                VALUES('$tempId',
                    '$docNo',
                    '".date('Y-m-d H:i:s')."',
                    '$path',
                    '$filename',
                    '$mainId',
                    '$docTime',
                    '$preferFaculty',
                    '$facReceive',
                    '$facSend',
                    '$User',
                    '$submitdocId',
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
    'offerTime'=> $docTime,
    'facReceive'=> $facReceive,
    'tempofferId'=> $tempId,
    'validFile'=> 1
);

echo json_encode($arr);
