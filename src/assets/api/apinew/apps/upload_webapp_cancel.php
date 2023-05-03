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

    //$params_value = print_r($_POST);
    $detail_id = $_POST['detail_id'];

    $main_id = $_POST['main_id'];
    $depart_id = $_POST['depart_id'];
    $edoc_id = $_POST['edoc_id'];
    $file_name = $_POST['file_name'];
    $tposition_id = $_POST['tposition_id'];
    $text_callblack = $_POST['text_callblack'];

    // ตรวจสอบการยกเลิก
    $sql_detail = "SELECT detail_status FROM sign_detail WHERE detail_id = '$detail_id'";
    $res_detail = mysqli_query($mysqli, $sql_detail);
    $row_detail = mysqli_fetch_array($res_detail);
    $detail_status = $row_detail['detail_status'];

    if($detail_status == '1'){

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

            // insert detail new signatured
            $ins_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
                main_id         = '$_POST[main_id]',
                tposition_id    = '$_POST[tposition_id]',
                file_name       = '$filename_new',
                file_path       = '$file_path',
                sign_status     = '5',
                remark          = '$text_callblack',
                upload_date     = '$today',
                sign_date       = '$today'
                ");
            $ins_detail_id = mysqli_insert_id($mysqli); // id detail new

            //Add row new sign_move
            $ins_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
            main_id         = '$_POST[main_id]',
            detail_id       = '$ins_detail_id',
            tposition_id    = '$_POST[tposition_id]',
            activity        = 'Remark',
            user_id         = '',
            time            = '$today'
            ");

            // update sign_status row before signature form
            $upd_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
                sign_status     = '1' 
                WHERE detail_id = '$_POST[detail_id]'
                ");

            // update main
            $update_main = mysqli_query($mysqli, "UPDATE sign_main SET 
                main_status     = '1' 
                WHERE main_id = '$_POST[main_id]'
            ");

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

    // echo json_encode(array(
    //     'msg' => 'error upload !',
    //     'status' => false,
    // ));

}

?>