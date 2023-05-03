<?php
require('../db.php');
date_default_timezone_set("Asia/Bangkok");

$edocId = $_GET['edoc_id'];
$receiveNo = $_GET['receive_no'];
$departId = $_GET['depart_id'];
$tpositionId = $_GET['tposition_id'];
$mainId = $_GET['main_id'];

$detailId = $_GET['detail_id'];


$userId = $_GET['user_id'];

$fileName = $_GET['file_name'];
$filePath = $_GET['file_path'];

@$timeUpdate = date('Y-m-d H:i:s');

$docYear = date('Y');

$status = false;

// Copy to retire_signed forder and add to pdf_path_retire

/* Location */
@$location = '../../document/retire_signed/';
//tranfer to signer 
$folder_depart = 'D00'.$departId.'/'.$docYear.'/';

$path = $location.$folder_depart;
if (!file_exists($path)) {
    mkdir($path, 0777, true);
}

@$path_signed = "../../document".$filePath;// $pathNew = '/edoctemp/DOC...';

// $ext = '.'.pathinfo($filename, PATHINFO_EXTENSION);
$ext = '.pdf';
$t=time();
$generatedName = date('Y-m-d').'_RN'.$receiveNo.'_edoc_'.md5($t.$fileName).'_'.$t.'t'.$tpositionId.'m'.$mainId.$ext;

// path approv not signed
$filePath_to_retire = $path.$generatedName;
$file_path = '/retire_signed'.'/'.$folder_depart.$generatedName;


if(@copy($path_signed, $filePath_to_retire)){ // Or $new_filename in locolhost 

    // Copy to edoc forder and add to pdf_path

    $objsql = "UPDATE edoc_receive SET pdf_name_retire = '$fileName', pdf_path_retire = '$file_path'
            WHERE edoc_id = '$edocId'
            AND depart_id = '$departId'
            AND receive_no = '$receiveNo'";

    if(mysqli_query($con, $objsql)){

        $objsqlUpdMain = "UPDATE sign_main SET main_status = '4' 
            WHERE main_id = '$mainId' 
            AND edoc_id = '$edocId' 
            AND depart_id = '$departId'";

        if(mysqli_query($con, $objsqlUpdMain)){
            $objsql = "UPDATE sign_detail SET sign_status = '4' 
                    WHERE main_id = '$mainId' 
                    AND tposition_id = '$tpositionId'
                    AND sign_status IN ('2') 
                    AND detail_status = '1'
                    ";

            if(mysqli_query($con, $objsql)) {
                $objsqlInsMove = "INSERT INTO  sign_move SET 
                main_id = '$mainId',
                detail_id = '$detailId',
                tposition_id = '$tpositionId',
                activity = 'Done',
                user_id = '$userId',
                time = '$timeUpdate'";

                if(mysqli_query($con, $objsqlInsMove)){
                    $status = true;
                }
            }
        }  
    } 

}


$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>