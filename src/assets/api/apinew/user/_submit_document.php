<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$tempId = $request->submitdocTempid;
$Title = $request->submitdocTitle;
$Perfer = $request->submitdocPerfer;
$Note = $request->submitdocNote;
$facCode = $request->facCode;//หน่วยงานที่ส่ง
$User = $request->User;
$userId = $request->userId;
$dateAdd = date('Y-m-d H:i:s');
$dateModified = date('Y-m-d H:i:s');
//$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
//$nocode = $facCode.'U'.$userId.$stampdate;
$sqlMaxNo = "SELECT MAX(order_No) as orderNo FROM tbsubmit_main WHERE faccode = '$facCode'";
$resMaxNo = mysqli_query($con, $sqlMaxNo);
$dataMaxNo = mysqli_fetch_array($resMaxNo);
$MaxNo = $dataMaxNo['orderNo'];
if($MaxNo == ''){
    $No = 1;
}else{
    $No = $MaxNo + 1;
}
    
$Order = $No;

$objsql = "INSERT INTO tbsubmit_main(
        submitmain_id,
        order_No,
        submitmain_title,
        submitmain_date,
        submitmain_note,
        faccode,
        user,
        datemodified,
        status
    )VALUES(
        '$tempId',
        '$Order',
        '$Title',
        '$dateAdd',
        '$Note',
        '$facCode',
        '$userId',
        '$dateModified',
        '1'
    )";
mysqli_query($con, $objsql);

$objSql = "SELECT submittemp_id, submittemp_pathfile, submittemp_fac, submittemp_filename
    FROM tbsubmit_temp
    WHERE submittemp_id = '$tempId' AND submittemp_user='$userId'";
$objRes = mysqli_query($con, $objSql);

$status = 'false';

while($objData = mysqli_fetch_array($objRes)){
    $pathFile = $objData['submittemp_pathfile'];
    $faculty_receive = $objData['submittemp_fac'];
    $fileName = $objData['submittemp_filename'];
    $objsql = "INSERT INTO tbsubmit_doc(
        submitdoc_id,
        submitmain_id,
        submitdoc_prefer,
        submitdoc_filepath,
        submitdoc_filename,
        fac_receive,
        status)
        VALUES(null,
        '$tempId',
        '$Perfer',
        '$pathFile',
        '$fileName',
        '$faculty_receive',
        '1')";
    if(mysqli_query($con, $objsql) ){
        
        $objsql = "DELETE FROM  tbsubmit_temp WHERE submittemp_id = '$objData[submittemp_id]'";
        mysqli_query($con, $objsql);
        $status = 'true';
    }
}
//$status = 'true';

$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

