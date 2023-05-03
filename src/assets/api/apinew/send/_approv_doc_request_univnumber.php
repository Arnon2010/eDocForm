<?php
    require('../db.php');
    $request = json_decode(file_get_contents("php://input"));
    date_default_timezone_set("Asia/Bangkok");

    @$location = '../../document/edoc/';

    $mainId = $request->mainid;
    $edocId = $request->docid;//รหัสหนังสือ
    $docTypeId = $request->doctype_id;//รหัสหนังสือ
    $userId = $request->userid;//ผู้ดำเนินการ
    $departIdUser = $request->depart_id_user; //รหัสหน่วยงานผู้ดำเนิการ
    $Year = $request->year_now;
    $departId = $departIdUser;

    @$sentDate = date('Y-m-d');
    @$sentTime = date('H:i:s');
    @$timeAction = date('Y-m-d H:i:s');

    @$docDate = date('Y-m-d');

    @$status = "true";


    ## Update status main ##
    $objsql_man = "UPDATE sign_main SET apply_number_univ = '1' WHERE main_id = '$mainId'";
    mysqli_query($con, $objsql_man);

    ## Add move activity GetNumber

    @$docYear = date('Y');
        
    @$folder_depart = 'D00'.$departId.'/'.$docYear;
    $structure = $location.''.$folder_depart.'/';
    if (!file_exists($structure)) {
        if (!mkdir($structure, 0777, true)) {
            die('Failed to create folders...');
        }
    }

    $no_detail = 0;
    $obj_detail = mysqli_query($con, "SELECT detail_id, tposition_id, file_name, file_path 
        FROM sign_detail 
        WHERE main_id = '$mainId' AND detail_status = '1' AND sign_status = '2'");
    while($row_detail = mysqli_fetch_array($obj_detail)) { $no_detail++;

        // copy file doc AND edoc_univ_no
        @$location_new = $structure;
        @$filename = $row_detail['file_name'];
        @$filePath = $row_detail['file_path'];

        if(strpos($filename, '_esign_') !== false) {
            $strFileName = explode('_esign_',$filename);
            $filename = $strFileName[1];
        }else if(strpos($filename, '_edoc_') !== false) {
            $strFileName = explode('_edoc_',$filename);
            $filename = $strFileName[1];
        }

        @$date_sent_no = date('Y-m-d').'-'.$no_detail;
        @$new_filename = 'DOCUNIV-'.$departId.'D'.round(microtime(true)).'_edoc_'. $date_sent_no.'.pdf';

        @$name_encode=rawurlencode($new_filename);
        @$filePathNew = '/edoc/'.$folder_depart.'/'.$name_encode;

        
        ## 1. copy file.
        if(@copy("../../document".$filePath, $location_new.$new_filename)){
            # 2. Add to table sign_move.
            $add_move = mysqli_query($con, "INSERT INTO  sign_move SET 
                main_id = '$mainId',
                detail_id = '$row_detail[detail_id]',
                tposition_id = '$row_detail[tposition_id]',
                activity = 'ApplyNumber',
                user_id = '$userId',
                time = '$timeAction'");

            # 3. Add to table edoc_univ_no.
            $objuniv_no = mysqli_query($con, "INSERT INTO edoc_univ_no SET edoc_id = '$edocId',
                depart_id = '$departId', 
                file_path = '$filePathNew',
                file_name = '$filename'
            ");
            if($objuniv_no)
                $status = "true";

        } 
    }

    $dateArray = explode("-",$docDate);
    $docDateNew = $dateArray[2].'/'.$dateArray[1].'/'.$dateArray[2];

    $data[] = array(
        'status'=>$status,
        'departId'=>$departId,
        'resp'=>''  
    );

    header("Access-Control-Allow-Origin: *");
    header("content-type:text/javascript;charset=utf-8");
    header("Content-Type: application/json; charset=utf-8", true, 200);
    print json_encode(array("data"=>$data));
?>