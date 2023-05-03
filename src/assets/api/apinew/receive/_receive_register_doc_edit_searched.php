<?php
    require('../db.php');
    require('../fn.php');
    $request = json_decode(file_get_contents("php://input"));
    date_default_timezone_set("Asia/Bangkok");

    @$location = '../../document/edoc/';

    @$filePath = $_POST[file_path];
    @$edocId = $_POST[edocid];
    @$receiveDepartId = $_POST[receive_depart_id]; //หน่วยงานรับ
    @$sendDepartId = $_POST[send_depart_id]; //จากหน่วยงาน
    @$dateWrite = date('Y-m-d h:i:s');
    @$docNo = $_POST[doc_no];

    @$sentDate = date('Y-m-d');
    @$sentTime = date('h:i:s');

    @$Year = $_POST[year_now];

    @$dateArray = explode("/",$docDate);
    @$docDateNew = ($dateArray[2]-543).'-'.$dateArray[1].'-'.$dateArray[0];


    $status = "true";

    @$docYear = date('Y');
    @$folder_depart = 'D00'.$sendDepartId.'/'.$docYear;
    $structure = $location.''.$folder_depart.'/';
    if (!file_exists($structure)) {
        if (!mkdir($structure, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    
    @$location_new = $structure;
    
    @$filetemp = $_FILES['file']['tmp_name'][0];
    @$filename = $_FILES['file']['name'][0];
    
    if(strpos($filename, '_esign_') !== false) {
        $strFileName = explode('_esign_',$filename);
        $filename = $strFileName[1];
    }else if(strpos($filename, '_edoc_') !== false) {
        $strFileName = explode('_edoc_',$filename);
        $filename = $strFileName[1];
    }
    
    @$numberSend = explode("/",$docNo);
    @$date_sent_no = date('Y-m-d').'-'.$numberSend[1];
    @$new_filename = 'DOC'.$receiveDepartId.'D'.round(microtime(true)).'_edoc_'. $date_sent_no.'.pdf';
    @$name_encode=rawurlencode($new_filename);
    @$filePathNew = '/edoc/'.$folder_depart.'/'.$name_encode;
    
   // Upload file
        if(move_uploaded_file($filetemp,$location_new.$new_filename)){
            // 2. edit edoc_sent table.
            //$resp = 'move file';
            $objsql_receive = "UPDATE edoc_receive SET
                pdf_path = '$filePathNew',
                pdf_name = '$filename',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            mysqli_query($con, $objsql_receive);
                
            $objsql_sent = "UPDATE edoc_sent SET
                pdf_path = '$filePathNew',
                pdf_name = '$filename',
                depart_id_send = '$sendDepartId'
                WHERE edoc_id = '$edocId'";
            if(mysqli_query($con, $objsql_sent)){
                $resp = 'move file';
            }else{
                $status = "false";
            }
        }// end move upload file.
    
    
    $data[] = array(
        'status'=>$status,
        'edocId'=>$edocId,
        'departId'=>$receiveDepartId,
        'resp'=>$resp,
        'filePath'=>$filePath
    );

    header("Access-Control-Allow-Origin: *");
    header("content-type:text/javascript;charset=utf-8");
    header("Content-Type: application/json; charset=utf-8", true, 200);
    print json_encode(array("data"=>$data));
?>