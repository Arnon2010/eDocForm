<?php
require('../db.php');

@$edocId = $_GET['edocid'];
@$departId_edoc = $_GET['departid'];

/*
*/
$objsql = "SELECT s.sent_id,
    s.pdf_name,
    s.pdf_path,
    s.depart_id,
    d.depart_name,
    s.receive_dept,
    s.sent_status,
    s.sent_comment,
    t.edoc_type,
    s.status_sent_doc
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN department d ON s.depart_id = d.depart_id
    WHERE s.edoc_id='$edocId' AND s.depart_id_send = '$departId_edoc'
    ";
    
$no = 0;
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){ $no++;
    $data[] = array(
        'deptNo'=>$no,
        'docType'=>$objdata['edoc_type'],
        'sentId'=>$objdata['sent_id'],
        'departId'=>$objdata['depart_id'],
        'fileName'=>$objdata['pdf_name'],
        'filePath'=>$objdata['pdf_path'],
        'departName'=>$objdata['depart_name'],
        'receiveDept'=>$objdata['receive_dept'],
        'status'=>$objdata['sent_status'],
        'sentComment'=>$objdata['sent_comment'],
        'status_sent_doc'=>$objdata['status_sent_doc']
    );
}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>