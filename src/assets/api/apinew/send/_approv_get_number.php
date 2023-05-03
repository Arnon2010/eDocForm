<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

$mainId = $request->mainid;
$edocId = $request->docid;//รหัสหนังสือ
$docTypeId = $request->doctype_id;//รหัสหนังสือ
$userId = $request->userid;//ผู้ดำเนินการ
$departIdUser = $request->depart_id_user; //รหัสหน่วยงานผู้ดำเนิการ
$Year = $request->year_now;
$docDate = $request->doc_date;

$departId = $departIdUser;

@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');

@$timeAction = date('Y-m-d H:i:s');

$docDateArr = explode("/",$docDate);
$docDate = ($docDateArr[2]-543).'-'.$docDateArr[1].'-'.$docDateArr[0];

$yearDoc = $docDateArr[2];

//$docDate = date('Y-m-d');

//$sendNo = getdocNo_send($departId);

$objsql = "SELECT sent_no FROM edoc_sent_no 
    WHERE depart_id='$departId' 
    AND edoc_type_id = '$docTypeId' 
    AND sent_year = '$yearDoc'";

$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);

//กำหนดเลขหนังสือส่งถัดไป
if($objdata['sent_no'] == ''){
    $No = '1';
    $sql = "INSERT INTO edoc_sent_no (edoc_type_id, sent_no, sent_year, depart_id) 
                values('$docTypeId', '$No', '$yearDoc', '$departId')";
}else{
    $No = $objdata['sent_no'] + 1;
    $sql = "UPDATE edoc_sent_no SET sent_no='$No' 
        WHERE depart_id='$departId' 
        AND edoc_type_id = '$docTypeId' 
        AND sent_year = '$yearDoc'";
}

//รหัสหนังสือของหน่วยงาน
$objsql2 = "SELECT depart_code FROM department WHERE depart_id='$departId'";
$objrs2 = mysqli_query($con, $objsql2);
$objdata2 = mysqli_fetch_assoc($objrs2);
$departCode = $objdata2['depart_code'];

//$departCode = getdepartCode($departId);

$docNo = $departCode.''.$No;
$sendNo = $docNo;

$objsql_doc = "UPDATE edoc SET 
    doc_no = '$docNo',
    doc_date = '$docDate' 
    WHERE edoc_id = '$edocId'
";
   
if(mysqli_query($con, $objsql_doc)){
    
    $status = "true";
    $objrs_sentNo = mysqli_query($con, $sql);//update and insert to table edoc_sent_no

    //inset table map edoc_univ_no กรณีมหาลัยออกเลขให้หน่วยงานอื่น
    if($numberUniv == true){
        $objsql_univNo = "INSERT INTO edoc_univ_no(edoc_id, depart_id) 
            VALUES ('$edocId','$departId')";
        mysqli_query($con, $objsql_univNo);
    }
    
    ## edoc track and log ##
    $operation = "ออกเลขหนังสือ";
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, status)
        VALUES(null, '$edocId', '$sentTime', '$sentDate', '$operation', '$departId', '$userId', '1')";
    mysqli_query($con, $objsql_track);

    ## Update status main ##
    $objsql_man = "UPDATE sign_main SET main_status = '3' WHERE main_id = '$mainId'";
    mysqli_query($con, $objsql_man);

    ## Add move activity GetNumber
    $obj_detail = mysqli_query($con, "SELECT detail_id, tposition_id FROM sign_detail 
    WHERE main_id = '$mainId' AND detail_status = '1' AND sign_status = '2'");
    while($row_detail = mysqli_fetch_array($obj_detail)) {

        $add_move = mysqli_query($con, "INSERT INTO  sign_move SET 
            main_id = '$mainId',
            detail_id = '$row_detail[detail_id]',
            tposition_id = '$row_detail[tposition_id]',
            activity = 'GetNumber',
            user_id = '$userId',
            time = '$timeAction'");
    }

    
}else{
    $status = "false";
}

$dateArray = explode("-",$docDate);
$docDateNew = $dateArray[2].'/'.$dateArray[1].'/'.$dateArray[2];

$data[] = array(
    'status'=>$status,
    'departId'=>$departId,
    'docNo'=>$docNo,
    'docDate'=>$docDate,
    'resp'=>''
    
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>