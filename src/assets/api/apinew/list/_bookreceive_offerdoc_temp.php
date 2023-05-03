<?php
session_start();
require('../db.php');
$booksendId = $_POST['booksend_id'];
$ePassport = $_POST['epassport'];
$supervisorName = $_POST['supervisor_name'];
$userId = $_POST['user_id'];
$facCode = $_POST['faccode'];
$date = date('Y-m-d H:i:s');
$arrDate = explode(" ",$date);
$dateOffer = $arrDate[0];
$timeOffer = $arrDate[1];
$sql = "SELECT breceive_id FROM tbbookreceive_main WHERE booksenddetail_id = '$booksendId'";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);
$breceiveId = $row[breceive_id];
/* Location */
$location = '../../document/offerreceive_not_signatured/';
$countfiles = count($_FILES['file_offer']['name']); // Count total files
for ( $i = 0;$i < $countfiles;$i++ ){
    $filename = $_FILES['file_offer']['name'][$i];  
    $new_filename ='F'.$facIdReceive.'U'.$User.round(microtime(true)).'_'. $filename;
    $path = '/offerreceive_not_signatured/'.$new_filename;
    
    $status = 'false';
    // Upload file
    
    if(move_uploaded_file($_FILES['file_offer']['tmp_name'][$i],$location.$new_filename) ){
        
        // Insert to table tbtemp_offerbooksend
        $objsql = "INSERT INTO tbofferbookreceive_temp(
                    tempoffer_id,
                    breceive_id,
                    epassport,
                    tempoffer_date,
                    tempoffer_filepath,
                    tempoffer_filename,
                    faccode,
                    user,
                    status)
                VALUES(null,
                    '$breceiveId',
                    '$ePassport',
                    '$date',
                    '$path',
                    '$filename',
                    '$facCode',
                    '$userId',
                    '0'
                )";
        mysqli_query($con, $objsql);
        $status = 'true';
    }
    
}

$arr = array(
    'status'=> $status,
    'filePath'=> $path,
    'fileName'=> $filename,
    'breceiveId'=> $breceiveId,
    'supervisorName'=>$supervisorName,
    'dateTime'=>$date,
    'dateOffer'=>$dateOffer,
    'timeOffer'=>$timeOffer
    
);

echo json_encode($arr);
