<?php
session_start();
require('../db.php');
$order_No = $_POST['order_no'];
$Title= $_POST['title'];
$offer_epassport = $_POST['supervisor'];
$User = $_POST['user'];
$facCode = $_POST['faccode'];
//$docTime = $_POST['tempoffer_doctime'].'-U'.$User;
$tempId = $_POST['tempoffer_id'];
$mainId = $_POST['mainid'];

$status = 'false';

$sql_main = "INSERT INTO tbofferbooksend_main(
        offermain_id,
        order_No,
        offermain_epassport,
        offermain_title,
        offermain_date,
        offermain_note,
        faccode,
        user,
        datemodified,
        status)
    VALUES(
        '$tempId',
        '$order_No',
        '$offer_epassport',
        '$Title',
        '".date('Y-m-d H:i:s')."',
        '',
        '$facCode',
        '$User',
        '".date('Y-m-d H:i:s')."',
        'W'
    )";
if(mysqli_query($con, $sql_main) ){
    
    $objsql = "SELECT * FROM tbofferbooksend_temp
        WHERE tempoffer_id = '$tempId' AND user='$User'";
        
    $objresult = mysqli_query($con, $objsql);
    while($objdata = mysqli_fetch_array($objresult)){
    // Insert to table tbofferbooksend
        $objsqlIns = "INSERT INTO  tbofferbooksend_doc(
            offerdoc_id,
            offermain_id,
            offerdoc_filepath,
            offerdoc_filename,
            offerdoc_prefer,
            fac_receive,
            status)
            VALUES(
                null,
                '$tempId',
                '$objdata[tempoffer_filepath]',
                '$objdata[tempoffer_filename]',
                '$objdata[tempoffer_prefer]',
                '$objdata[fac_receive]',
                '0'
            )";
            
        mysqli_query($con, $objsqlIns);
        $status = 'true';
        
    }
    $objsql = "DELETE FROM tbofferbooksend_temp WHERE tempoffer_id = '$tempId'";
    mysqli_query($con, $objsql);
}


if($status == 'true'){
    $objsql = "UPDATE tbsubmit_main SET status = '2', datemodified='".date('Y-m-d H:i:s')."'
        WHERE order_No = '$order_No' AND submitmain_id = '$mainId'";
    mysqli_query($con, $objsql);
}
$arr = array(
    'status'=> $status
);

echo json_encode($arr);
