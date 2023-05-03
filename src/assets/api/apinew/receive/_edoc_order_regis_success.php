<?php
require('../db.php');
@$edocId = $_GET['edocid'];
@$departId = $_GET['departid'];
   
$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    rc.receive_no,
    rc.receive_date,
    rc.receive_time,
    rc.pdf_path,
    rc.pdf_name,
    r.rapid_name,
    sc.secrets_name,
    d.depart_name,
    e.status,
    t.edoc_type_id,
    t.edoc_type_name,
    u.user_fname,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc_receive rc
    LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE rc.depart_id = '$departId' AND rc.edoc_id = '$edocId'";


    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
    
    //@$edocDate = explode("-",$objdata['doc_date']);
    //@$edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    $data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'receiveNo'=>$objdata['receive_no'],
        'receiveDate'=>$objdata['receive_date'],
        'receiveTime'=>$objdata['receive_time']
        
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
