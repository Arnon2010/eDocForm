<?php
    require('../db.php');
    $request = json_decode(file_get_contents("php://input"));
    date_default_timezone_set("Asia/Bangkok");

    @$mainId = $request->mainid;
    @$tpositionId = $request->tpositionid;
    @$sequType = $request->sequtype;
    @$userId = $request->userid;
    @$itemSigner = $request->itemsigner;
    @$statusGetNo = $request->status_getno;

    //เสนอหนังสือหลังจากออกเลข
    if($statusGetNo == '1')
        $status_get_no = '1';
    else {
        $status_get_no = '0';
    }

    $upd_detail = "UPDATE sign_detail SET confirm_status = '1'
        WHERE main_id = '$mainId' 
        AND sign_status = '0' 
        AND detail_status = '1'"; 
    mysqli_query($con, $upd_detail);

    if($sequType == '1'){ //กรณีเสนอแบบทั่วไป
        $objsql_sequ = "INSERT INTO sign_sequence SET 
            main_id = '$mainId',
            tposition_id = '$tpositionId',
            sequ_status = '1'
        ";
        mysqli_query($con, $objsql_sequ);

        $mainType = '1';//เสนอแบบปกติ
        
    }else if($sequType == '2'){//กรณีเสนอแบบลำดับอัตโนมัติ
        foreach($itemSigner as $key=>$val){
            $objsql_sequ = "INSERT INTO sign_sequence SET 
                main_id = '$val->main_id',
                tposition_id = '$val->tposition_id',
                sequ_status = '1'
            ";
            mysqli_query($con, $objsql_sequ);

            //ลำดับแรกสำหรับลงนาม
            if($key == 0){
                $tpositionId = $val->tposition_id;
                $objsql = "UPDATE sign_detail SET confirm_status = '1' 
                WHERE main_id = '$mainId' 
                AND sign_status = '0' 
                AND detail_status = '1'"; 
            }
        }
        $mainType = '2';//เสนอตามลำดับอัตโนมัติ
    }
    
    //Update status sign main รออนุมัติ
    $objsql_main = "UPDATE sign_main SET main_status = '1', 
        main_type = '$mainType',
        status_get_no = '$status_get_no'
        WHERE main_id = '$mainId'";
    if(mysqli_query($con, $objsql_main))
        $status = 'true';

    $data[] = array(
        'status'=> $status,
        'resp'=> $itemSigner
    );

    header("Access-Control-Allow-Origin: *");
    header("content-type:text/javascript;charset=utf-8");
    header("Content-Type: application/json; charset=utf-8", true, 200);
    print json_encode(array("data"=>$data));
