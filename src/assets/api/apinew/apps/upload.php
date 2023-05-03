<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("content-type:text/javascript;charset=utf-8");

header("Content-Type: application/json; charset=utf-8", true, 200);

@$today = date('Y-m-d H:i:s');

if($_POST['api_token_key'] == 'a3393d48c839d8fbe212457cddff720a'){
    
    include "config.php";

    $params_value = print_r($_POST);

    $main_id = $_POST['main_id'];
    $depart_id = $_POST['depart_id'];
    $edoc_id = $_POST['edoc_id'];
    $file_name = $_POST['file_name'];
    $detail_id = $_POST['detail_id'];
    $tposition_id = $_POST['tposition_id'];

    // ตรวจสอบการยกเลิก และการส่งซ็ำ
    $sql_detail = "SELECT detail_status, sign_status FROM sign_detail 
        WHERE detail_id = '$_POST[detail_id]' 
        AND  main_id = '$_POST[main_id]' 
        AND tposition_id = '$_POST[tposition_id]'";
    $res_detail = mysqli_query($mysqli, $sql_detail);
    $row_detail = mysqli_fetch_array($res_detail);
    $detail_status = $row_detail['detail_status'];

    if($detail_status == '1' && $row_detail['sign_status'] == '0'){

        $no = 1;

        $year = date('Y');

        $location = '../../document/';

        $path_approv_signed = 'approv_signed/D00'.$depart_id.'/'.$year.'/';
        $path = $location.$path_approv_signed;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $originalName = $_FILES['file']['name'];

        if(strpos($originalName, '_esign_') !== false) {
            $strFileName = explode('_esign_',$originalName);
            $originalName = $strFileName[1];
        }else if(strpos($originalName, '_edoc_') !== false) {
            $strFileName = explode('_edoc_',$originalName);
            $originalName = $strFileName[1];
        }


        //$file_tmp = $_FILES['file']['tmp_name'];

        $ext = '.'.pathinfo($originalName, PATHINFO_EXTENSION);
        $t=time();
        //$generatedName = date('Y-m-d').'_'.md5($t.$originalName).'_'.$t.'t'.$tposition_id.'m'.$main_id.$ext;
        $generatedName = 'DOC'.$tposition_id.'_'.md5($t.$originalName.$tposition_id.$main_id).'_edoc_'.date('Y-m-d').'-'.$t.'-M'.$main_id.$ext;

        // path approv signed
        $filePath_signed = $path.$generatedName;

        $file_path = '/'.$path_approv_signed.$generatedName;

        $filename_new = 'DOC'.round(microtime(true)).$no.'_edoc_'.$originalName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath_signed)) {

            // check การส่งซ้ำ
            $objdetail_check = mysqli_query($mysqli, "SELECT sign_status 
                FROM sign_detail 
                WHERE detail_id = '$_POST[detail_id]' 
                AND  main_id = '$_POST[main_id]' 
                AND tposition_id = '$_POST[tposition_id]'");

            $rowdetail_check = mysqli_fetch_array($objdetail_check);

            if($rowdetail_check['sign_status'] == '0'){

                // update sign_status row before signature form
                $upd_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
                    sign_status     = '1' 
                    WHERE detail_id = '$_POST[detail_id]' 
                    AND  main_id = '$_POST[main_id]' 
                    AND tposition_id = '$_POST[tposition_id]'
                    ");

                // insert detail new signatured
                $ins_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
                    main_id         = '$_POST[main_id]',
                    tposition_id    = '$_POST[tposition_id]',
                    file_name       = '$filename_new',
                    file_path       = '$file_path',
                    sign_status     = '2',
                    upload_date     = '$today',
                    sign_date       = '$today'
                    ");
                $ins_detail_id = mysqli_insert_id($mysqli); // id detail new

                //Add row new sign_move
                $ins_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
                main_id         = '$_POST[main_id]',
                detail_id       = '$ins_detail_id',
                tposition_id    = '$_POST[tposition_id]',
                activity        = 'Signatured',
                time            = '$today'
                ");

                

                // check num row sing of doc
                $num_row_not_sign = mysqli_num_rows(mysqli_query($mysqli, "SELECT sign_status FROM sign_detail 
                    WHERE main_id='$_POST[main_id]' 
                    AND tposition_id = '$_POST[tposition_id]' 
                    AND sign_status = '0' 
                    AND detail_status = '1'
                    ")
                );

                if($num_row_not_sign >= 1){// หากยังเซ็นไฟล์ไม่ครบ
                    $main_status = '1';
                    $sequ_signed_status = 'W';
                } 
                
                else{
                    $main_status = '2';
                    $sequ_signed_status = 'C';
                }

                // update main_status
                $upd_main = mysqli_query($mysqli, "UPDATE sign_main SET main_status  = '$main_status' 
                    WHERE main_id = '$_POST[main_id]'
                ");

                // update sign sequence
                $upds_sequence =  mysqli_query($mysqli, "UPDATE sign_sequence SET sequ_signed_status  = '$sequ_signed_status' 
                    WHERE main_id = '$_POST[main_id]' 
                    AND tposition_id = '$_POST[tposition_id]'
                ");

                //check sequence
                $row_sequence = mysqli_fetch_array(
                    mysqli_query($mysqli, "SELECT tposition_id, count(sequ_no) AS num_sequ_no
                    FROM sign_sequence 
                    WHERE main_id = '$_POST[main_id]' 
                    AND sequ_signed_status = 'W' 
                    AND sequ_status = '1'
                    ORDER BY sequ_no ASC 
                    LIMIT 0,1
                "));

                //check detail more file of tposition
                $objsql_file_more = mysqli_query($mysqli, "SELECT count(tposition_id) AS num_more_file
                FROM sign_detail
                WHERE main_id = '$_POST[main_id]' 
                AND tposition_id = '$_POST[tposition_id]' 
                AND sign_status = '0' 
                AND detail_status = '1'
                ");

                $row_file_more =  mysqli_fetch_array($objsql_file_more);
                $more_file = $row_file_more['num_more_file'];

                // ** กรณีส่งต่อให้อัตโนมัติ **//
                if($_POST['main_type'] == '2' && $row_sequence['num_sequ_no'] >= 1 && $more_file == 0) {

                    $path_approv_not_signed = 'approv_not_signed/D00'.$depart_id.'/'.$year.'/';
                    $path = $location.$path_approv_not_signed;
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $test_to_auto_transfer = 'test_to_auto_transfer';

                    $to_tposition_id = $row_sequence['tposition_id'];

                    $obj_detail_at = "SELECT detail_id, file_name, file_path FROM sign_detail 
                        WHERE main_id = '$_POST[main_id]' 
                        AND tposition_id = '$_POST[tposition_id]' 
                        AND detail_status = '1' 
                        AND sign_status = '2'";

                    $res_detail_at = mysqli_query($mysqli, $obj_detail_at);

                    $No = 0;

                    while($data_detail_tf = mysqli_fetch_array($res_detail_at)){ $No++;

                        $file_name_signed = $data_detail_tf['file_name'];

                        $file_path_signed = $data_detail_tf['file_path'];

                        if(strpos($file_name_signed, '_esign_') !== false) {
                            $strFileName = explode('_esign_',$file_name_signed);
                            $file_name_signed = $strFileName[1];
                        }else if(strpos($file_name_signed, '_edoc_') !== false) {
                            $strFileName = explode('_edoc_',$file_name_signed);
                            $file_name_signed = $strFileName[1];
                        }

                        //$ext = '.'.pathinfo($file_name_signed, PATHINFO_EXTENSION);
                        $ext = '.pdf';
                        $t=time();
                        //$generatedName_New = date('Y-m-d').'_No'.$No.'_'.md5($t.$file_name_signed).'_'.$t.'t'.$to_tposition_id.'m'.$main_id.$ext;
                        
                        $generatedName_New = 'DOC'.$tposition_id.'_'.md5($t.$file_name_signed.$tposition_id.$main_id).'_edoc_'.date('Y-m-d').'-'.$t.'-M'.$main_id.$ext;

                        //Copy file signed from table sign_detail.
                        $filePath_signed = '../../document'.$file_path_signed;
                        
                        // path approv not signed
                        
                        $filePath_not_signed = $path.$generatedName_New;

                        $filename_new = 'DOC'.round(microtime(true)).$no.'_edoc_'.$file_name_signed;

                        if (@copy($filePath_signed, $filePath_not_signed)) {

                            // insert detail new signatured
                            $file_path_not_signed = '/'.$path_approv_not_signed.$generatedName_New;

                            $ins_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
                                main_id         = '$_POST[main_id]',
                                tposition_id    = '$to_tposition_id',
                                file_name       = '$filename_new',
                                file_path       = '$file_path_not_signed',
                                sign_status     = '0',
                                upload_date     = '$today',
                                sign_date       = '$today'
                                ");

                            $detail_id_new = mysqli_insert_id($mysqli);

                            //Add new row  sign move
                            $ins_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
                            main_id         = '$_POST[main_id]',
                            detail_id       = '$detail_id_new',
                            tposition_id    = '$to_tposition_id',
                            activity        = 'TranferTo',
                            time            = '$today'
                            ");

                            // update sign_status row before signature form
                            $upd_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
                                sign_status     = '3' 
                                WHERE detail_id = '$data_detail_tf[detail_id]'
                                ");

                            // update main_status
                            $upd_main = mysqli_query($mysqli, "UPDATE sign_main SET main_status  = '1' 
                                WHERE main_id = '$_POST[main_id]'
                            ");

                            // update sequence status = 1
                            $upd_sequence = mysqli_query($mysqli, "UPDATE sign_sequence SET sequ_transfer  = '1' 
                            WHERE main_id = '$_POST[main_id]' 
                            AND tposition_id = '$_POST[tposition_id]' 
                            AND sequ_status = '1' 
                            AND sequ_signed_status = 'C'
                        ");
                        }else {
                            echo json_encode(array(
                                'msg' => 'error upload !',
                                'status' => false,
                            ));
                        }

                    } //end while
            
                }//end if check auto send

            } //end check detail sign_status=0
            //add new detail

            //add status move

            //update main

            $data[] = array(
                'params_value'=> $params_value,
                'ins_detail_id'=> $ins_detail_id,
                'more_file'=> $more_file,
                'test_to_auto_transfer'=> $test_to_auto_transfer,
                'sequ_no'=>$row_sequence['num_sequ_no'],
                'to_tposition_id'=> $to_tposition_id,
                'more_file'=>$more_file,
                'generatedName'=>$generatedName_New,
                'obj_detail_at'=>$obj_detail_at
            );

            echo json_encode(array(
                'msg' => 'success upload !',
                'status' => true,
                'data'=> $data
            ));
        }else {
            echo json_encode(array(
                'msg' => 'error upload !',
                'status' => false,
            ));
        }
    }else{
        echo json_encode(array(
            'msg' => 'File is remove !',
            'status' => false,
        ));
    }

}

?>