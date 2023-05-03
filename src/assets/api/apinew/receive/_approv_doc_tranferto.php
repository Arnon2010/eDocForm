<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

/* Location */
@$location = '../../document/';

$itemTranf = $request->item_tranf;//ประเภทหนังสือ
$departId = $request->depart_id; //รหัสหน่วยงาน
$userId = $request->user_id;
//$Year = $request->year;


@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');

//$docYear = ($Year - 543);

$docYear = date('Y');

@$uploadDate = date('Y-m-d H:i:s');

$status = false;

//tranfer to signer 
$path_approv_not_signed = 'approv_not_signed/D00'.$departId.'/'.$docYear.'/';
$path = $location.$path_approv_not_signed;
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
}

foreach($itemTranf as $key => $val){
    $mainId = $val->main_id;
    $tpositionId = $val->tposition_id;
    $tpositionId_old = $val->tposition_id_old;

    //get detail file from sign_detail

    $objsql = "SELECT file_path, file_name, tposition_id FROM sign_detail 
        WHERE main_id = '$mainId' 
        AND sign_status = '2' 
        AND tposition_id = '$tpositionId_old'
        AND detail_status = '1'";

    $objrs = mysqli_query($con, $objsql);
    $no = 0;
    while($data = mysqli_fetch_array($objrs)){ $no++;
        
        $filePath = $data['file_path'];
        $fileName = $data['file_name'];

        if(strpos($fileName, '_esign_') !== false) {
            $strFileName = explode('_esign_',$fileName);
            $fileName = $strFileName[1];
            
        }else if(strpos($fileName, '_edoc_') !== false) {
            $strFileName = explode('_edoc_',$fileName);
            $fileName = $strFileName[1];
        }
        
        //$ext = '.'.pathinfo($filename, PATHINFO_EXTENSION);
        $ext = '.pdf';
        $t=time();
        //$generatedName = date('Y-m-d').'_NO'.$no.'_'.md5($t.$filename).'_edoc_'.$t.'t'.$tpositionId.'m'.$mainId.$ext;
        $generatedName = 'DOC'.$tpositionId.'_'.md5($t.$fileName.$tpositionId.$mainId).'_edoc_'.date('Y-m-d').'-'.$t.'-M'.$mainId.$ext;

        @$path_signed = "../../document".$filePath;// copy file from path: approv_signed;
        
        // path approv not signed
        $filePath_not_signed = $path.$generatedName;
        $file_path = '/'.$path_approv_not_signed.$generatedName;

        $filename_new = 'DOC'.round(microtime(true)).$no.'_edoc_'.$fileName;

        if(@copy($path_signed, $filePath_not_signed)){ // Or $new_filename in locolhost

            // Insert to sign_detail

            $objsqlInsDetail = "INSERT INTO sign_detail(
                detail_id,
                main_id,
                tposition_id,
                file_name,
                file_path,
                sign_status,
                upload_date)
                VALUES(null,'$mainId', '$tpositionId', '$filename_new', '$file_path', '0','$uploadDate')";
            if(mysqli_query($con, $objsqlInsDetail)){

                $detail_id_ins = mysqli_insert_id($con);

                $objsqlInsMove = "INSERT INTO  sign_move SET tposition_id = '$tpositionId',
                    main_id = '$mainId',
                    detail_id = '$detail_id_ins',
                    activity = 'TranferTo',
                    user_id = '$userId',
                    time = '$uploadDate'
                    ";
                if(mysqli_query($con, $objsqlInsMove)){

                    $objsqlUpdStatus = "UPDATE sign_detail SET sign_status = '3' 
                        WHERE main_id = '$mainId' 
                        AND sign_status = '2'
                        AND tposition_id = '$tpositionId_old'
                        AND detail_status = '1'";
                    if(mysqli_query($con, $objsqlUpdStatus)){
                        //update status sign_main
                        $objsqlUpdMain = "UPDATE sign_main SET main_status = '1' WHERE main_id = '$mainId'";
                        if(mysqli_query($con, $objsqlUpdMain))
                            $status = true;
                    }
                }

                // set status transfer
                $upd_sequence = mysqli_query($con, "UPDATE sign_sequence SET sequ_transfer = '1' 
                    WHERE main_id = '$mainId' 
                    AND tposition_id = '$data[tposition_id]'
                    AND sequ_status = '1'
                ");
            }

        } //if copy

    }// end while copy

    // add new sign sequence
    $insSequence = "INSERT INTO sign_sequence SET main_id = '$mainId',
    tposition_id = '$tpositionId',
    sequ_status = '1',
    sequ_signed_status = 'W'";

    mysqli_query($con, $insSequence);
}

$data[] = array(
    'status'=>$status,
    'mainId'=>$mainId,
    'tpositionId'=>$tpositionId,
    'departId'=>$departId,
    'resp'=> $objsqlInsMove
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>