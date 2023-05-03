<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$dateSubmittime = $request->dateSubmittime;
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
$sqlMaxNo = "SELECT MAX(submit_no) as SubmitNo FROM tbsubmitdoc WHERE submit_faccode = '$facCode'";
$resMaxNo = mysqli_query($con, $sqlMaxNo);
$dataMaxNo = mysqli_fetch_array($resMaxNo);
$MaxNo = $dataMaxNo['SubmitNo'];
if($MaxNo == ''){
    $No = 1;
}else{
    $No = $MaxNo + 1;
}
    
$Order = $facCode.'A'.$No;

$objSql = "SELECT tempsubmit_id,tempsubmit_pathfile, tempsubmit_faccode, tempsubmit_filename
    FROM tbtempsubmit
    WHERE tempsubmit_doctime = '$dateSubmittime'";
$objRes = mysqli_query($con, $objSql);
while($objData = mysqli_fetch_array($objRes)){
    $pathFile = $objData['tempsubmit_pathfile'];
    $faculty_receive = $objData['tempsubmit_faccode'];
    $fileName = $objData['tempsubmit_filename'];
    $objsql = "INSERT INTO tbsubmitdoc(
        submit_id,
        submit_order,
        submit_no,
        submit_date,
        submit_title,
        submit_prefer,
        submit_filepath,
        submit_filename,
        submit_faccode,
        faccode_receive,
        submit_note,
        user,
        date_modified,
        submit_status)
        VALUES(null, '$Order', '$No', '$dateAdd', '$Title', '$Perfer', '$pathFile', '$fileName', '$facCode', '$faculty_receive', '$Note', '$User', '$dateModified', 'W')";
    if(mysqli_query($con, $objsql) ){
        
        $objsql = "UPDATE tbtempsubmit SET status = '1'
            WHERE tempsubmit_id = '$objData[tempsubmit_id]'";
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

