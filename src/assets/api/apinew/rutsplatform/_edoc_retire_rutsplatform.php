<?php
//if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // The request is using the POST method
    header("HTTP/1.1 200 OK");
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Max-Age: 1000');
    //$this->next->call();
//}

$request = json_decode(file_get_contents('php://input'));
$personId = $request->personId; //เลขบัตรประชาชน
include_once ('../db.php');

$objsql = "SELECT edoc.edoc_id, 
edoc.headline, 
edoc.doc_no, 
edoc.doc_date, 
takeposition.tposition_name, 
takeposition.e_passport, 
edoc_type.edoc_type_name, 
delegate_user.person_name,
retire.retire_text,
retire.retire_date,
delegate_retire.file_path,
delegate_retire.retire_date,
delegate_retire.retire_time,
delegate_retire.delegate_retire_id
FROM delegate_retire
INNER JOIN delegate_user ON delegate_retire.deleg_user_id = delegate_user.deleg_user_id 
INNER JOIN retire ON delegate_retire.retire_id = retire.retire_id
INNER JOIN takeposition ON retire.tposition_id = takeposition.tposition_id
INNER JOIN edoc ON retire.edoc_id = edoc.edoc_id 
INNER JOIN edoc_type ON edoc.edoc_type_id = edoc_type.edoc_type_id
WHERE delegate_user.citizen_id = '$personId'";

$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
    
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $receiveDate = explode("-",$objdata['receive_date']);
    $receiveDateNew = $receiveDate[2].'/'.$receiveDate[1].'/'.($receiveDate[0]+543);

    //$urldoc_encode = rawurlencode('/document/'.$objdata['pdf_path_retire']);

    $urldoc = $_SERVER['SERVER_NAME'].'/document'.$objdata['file_path'];
    
    $data[] = array(
        'delegateRetireId'=>$objdata['delegate_retire_id'],
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocTypeId'=>$objdata['edoc_type_id'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'edocDate'=>$edocDateNew,
        'receiveNo'=>$objdata['receive_no'],
        // 'receiveTime'=>$objdata['receive_time'],
        // 'receiveDate'=>$receiveDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'personName'=>$objdata['person_name'],
        'retireText'=>$objdata['retire_text'],
        'retireDate'=>$objdata['retire_date'],
        'retireTime'=>$objdata['retire_time'],
        'pdfPathRetire'=>$urldoc
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data), JSON_UNESCAPED_UNICODE);

?>