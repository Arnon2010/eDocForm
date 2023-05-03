<?php
session_start();
require('../db.php');
$breceiveId = $_POST['breceive_id'];
$dateTime = $_POST['datetime'];

$sql = "SELECT * FROM tbofferbookreceive_temp WHERE breceive_id = '$breceiveId' AND tempoffer_date = '$dateTime'";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);
$status = 'false';

$objsql = "INSERT INTO tbofferbookreceive_main(
        offer_id,
        breceive_id,
        epassport,
        offer_date,
        offer_filepath,
        offer_filename,
        faccode,
        user,
        status)
    VALUES(null,
        '$breceiveId',
        '$row[epassport]',
        '$row[tempoffer_date]',
        '$row[tempoffer_filepath]',
        '$row[tempoffer_filename]',
        '$row[faccode]',
        '$row[user]',
        'W'
    )";
    
if(mysqli_query($con, $objsql)){
    $status = 'true';
}
$arr = array(
        'status'=> $status
    );
echo json_encode($arr);
