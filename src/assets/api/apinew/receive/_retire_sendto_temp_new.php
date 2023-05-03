<?php
        require('../db.php');
        require('../fn.php');
        $request = json_decode(file_get_contents("php://input"));
        //date_default_timezone_set("Asia/Bangkok");
        /* Location */
        @$location = '../../document/edoctemp/';
        
        @$userId = $_POST["userid"];
        @$depart_sentId = $_POST["depart_sent_id"];
        @$timeWrite = $_POST["timewrite"];
        @$edocId = $_POST["edocid"];
        @$receiveDept = $_POST['receive_dept'];
        @$fileName = $_POST['file_name_retire'];
        @$filePath = $_POST['file_path_retire'];
        @$docYear = date('Y');
        @$stampdate = strtotime(date('Y-m-d h:i:s'));//strtotime($datetime);
        @$tempId = $depart_sentId.'D00'.$userId.'U'.$stampdate;
        
        $objsql = "SELECT distinct sent_no FROM edoc_sent WHERE edoc_id='$edocId'";
        $objrs = mysqli_query($con, $objsql);
        $objdata = mysqli_fetch_assoc($objrs);
        @$sentNo = $objdata['sent_no'];
        @$dateImport = date('Y-m-d');

        
        if($_POST["sent_status"] == 'true')
            @$statusSentDoc = 1;
        else
            @$statusSentDoc = 0;
        $status = 'false';
        
        $objsql = "INSERT INTO edoc_sent_temp(
                temp_id,
                sent_no,
                edoc_id,
                depart_id,
                receive_dept,
                user_id,
                pdf_name,
                pdf_path,
                temp_date,
                temp_timewrite,
                temp_status,
                temp_sent_status)
                VALUES('$tempId',
                '$sentNo',
                '$edocId',
                '$depart_sentId',
                '$receiveDept',
                '$userId',
                '$fileName',
                '$filePath',
                '$dateImport',
                '$timeWrite',
                '0',
                '$statusSentDoc'
                )";
        if(mysqli_query($con, $objsql)){
            $status = 'true';
        }

        $data[] = array(
            'status'=>$status,
            'tempid'=>$tempId,
            'pdfname'=>$fileName,
            'edocid'=>$edocId,
            'sentNo'=>$sentNo,
            'validFile'=>1
        );
        
        header("Access-Control-Allow-Origin: *");
        header("content-type:text/javascript;charset=utf-8");
        header("Content-Type: application/json; charset=utf-8", true, 200);
        print json_encode(array("data"=>$data));
