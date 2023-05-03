<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$location = '../../document/edoc/';

$docType = $_POST['doctype']; //ประเภทหนังสือ
$userId = $_POST['userid']; //ผู้ดำเนินการ
$Secrets = $_POST['secrets']; //ความเร่งด่วน
$Rapid = $_POST['rapid']; //ความลับ
$departId = $_POST['depart_id']; //รหัสหน่วยงานหนังสือเสนอ
$Comment = $_POST['comment']; //การปฏิบัติ
$Headline = $_POST['headline']; //เรื่อง
$Receiver = $_POST['receiver']; //เรียน
$dateWrite = $_POST['datewrite']; //วันที่เสนอ
$Sender = $_POST['sender']; //ชื่อผู้เสนอ
$senderDepart = $_POST['senderdepart']; //หน่วยงานเสนอ
$destroyYear = $_POST['destroy_year']; //อายุหนังสือ

@$Year = $_POST['year_now'];
@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');
@$timeAction = date('Y-m-d H:i:s');

// $dateArray = explode("/", $docDate);
// $docDateNew = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];

@$docYear = date('Y');

@$folder_depart = 'D00' . $departId . '/' . $docYear;
$structure = $location . '' . $folder_depart . '/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}

@$location_new = $structure;
@$filetemp = $_FILES['file']['tmp_name'][0];
@$filename = $_FILES['file']['name'][0];

if (strpos($filename, '_esign_') !== false) {
    $strFileName = explode('_esign_', $filename);
    $filename = $strFileName[1];
} else if (strpos($filename, '_edoc_') !== false) {
    $strFileName = explode('_edoc_', $filename);
    $filename = $strFileName[1];
}


@$date_sent_no = date('Y-m-d');
@$new_filename = 'DOCUNIV-' . $departId . 'D' . round(microtime(true)) . '_edoc_' . $date_sent_no . '.pdf';

@$name_encode = rawurlencode($new_filename);
@$filePathNew = '/edoc/' . $folder_depart . '/' . $name_encode;


// Upload file
if (move_uploaded_file($filetemp, $location_new . $new_filename)) {
    // Insert to table "edoc"
    $objsql = "INSERT INTO edoc(edoc_id,
        doc_no,
        edoc_type_id,
        sent_date,
        sent_time,
        headline,
        receiver,
        comment,
        secrets,
        rapid,
        depart_id,
        sender_depart,
        user_id,
        sender,
        edoc_datewrite,
        destroy_year,
        status) VALUES(null,
        '$docNo',
        '$docType',
        '$sentDate',
        '$sentTime',
        '$Headline',
        '$Receiver',
        '$Comment',
        '$Secrets',
        '$Rapid',
        '$departId',
        '$senderDepart',
        '$userId',
        '$Sender',
        '$dateWrite',
        '$destroyYear',
        '0'
        )";

    if (mysqli_query($con, $objsql)) {

        $edoc_id = mysqli_insert_id($con); // Get "edoc id" last insert

        $objsql_main = "INSERT INTO sign_main SET 
            edoc_id = '$edoc_id', 
            depart_id = '$departId',
            user_id = '$userId',
            create_date = '$sentDate',
            create_time = '$sentTime',
            main_status = '2',
            doc_type = '1',
            apply_number_univ = '2'";
        if (mysqli_query($con, $objsql_main)) {

            $mainId = mysqli_insert_id($con);

            ## Add move activity GetNumber
            $objmove_getnum = mysqli_query($con, "INSERT INTO sign_move SET 
            main_id = '$mainId',
            activity = 'ApplyNumber',
            user_id = '$userId',
            time = '$timeAction'");

            ## Add pathfile to edoc_univ_no table
            $objuniv_no = mysqli_query($con, "INSERT INTO edoc_univ_no SET edoc_id = '$edoc_id',
                depart_id = '$departId', 
                file_path = '$filePathNew',
                file_name = '$filename'
            ");
            if ($objuniv_no)
                $status = "true";
        }

        ## edoc track and log ##
        /*
        $operation = "เสนอหนังสือ";
        $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, status)
            VALUES(null, '$edoc_id', '$sentTime', '$sentDate', '$operation', '$departIdUser', '$userId', 'A')";
        mysqli_query($con, $objsql_track);
        */
    } else {
        $status = "false";
    }
} // end move upload file.

$data[] = array(
    'status' => $status,
    'edocId' => $edoc_id,
    'mainId' => $mainId,
    'resp' => ''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data" => $data));
