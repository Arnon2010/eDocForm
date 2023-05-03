<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$departId = $_POST['depart_id'];

@$userId = $_POST["user_id"];
@$mainId = $_POST["main_id"];
@$tpositionId = $_POST['tposition_id'];//รหัสผู้ลงนาม

@$timeAction = date('Y-m-d H:i:s');
@$docYear = date('Y');

$status = 'false';

//จำนวนรายการทียังไม่เซ็น
$row_not_sign = mysqli_fetch_array(mysqli_query($con, "SELECT count(main_id) as numrow_not_sign 
    FROM sign_detail 
    WHERE main_id = '$mainId' 
    AND sign_status = '0' 
    AND detail_status = '1'
    ")
);

//จำนวนรายการที่มีการส่งต่อ และผู้เกษียนหรือลงนามล่าสุด
$row_transfer = mysqli_fetch_array(mysqli_query($con, "SELECT 
    s.tposition_id, COUNT(s.main_id) AS numrow_transfer, msq.maxSequNo
    FROM (
        SELECT main_id, max(sequ_no) as maxSequNo 
        FROM sign_sequence 
        WHERE main_id = '$mainId'
        AND sequ_status = '1' 
        AND sequ_signed_status = 'C' 
        GROUP BY main_id
        
    ) msq
    INNER JOIN sign_sequence  s ON s.main_id = msq.main_id AND s.sequ_no = msq.maxSequNo
        WHERE s.main_id = '$mainId' 
        AND s.sequ_status = '1' 
        AND s.sequ_signed_status = 'C' 
        GROUP BY s.main_id
    ")
);

//กรณีที่มีการส่งต่อจากคนแรกแล้ว และคนรับถัดไปถูกยกเลิก ในการส่งต่ออัตโนมัติตามลำดับ
if($row_not_sign['numrow_not_sign'] == 0 && $row_transfer['numrow_transfer'] == 1){

    $location = '../../document/';

    $path_approv_not_signed = 'approv_not_signed/D00'.$departId.'/'.$docYear.'/';
    $path = $location.$path_approv_not_signed;
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    $obj_detail = mysqli_query($con, "SELECT detail_id, file_name, file_path 
        FROM  sign_detail
        WHERE main_id = '$mainId' 
        AND tposition_id = '$row_transfer[tposition_id]'
        AND sign_status IN ('2','3') 
        AND detail_status = '1'
    ");

    $No = 0;

    while($data_detail_tf = mysqli_fetch_array($obj_detail)){ $No++;

        $file_name_signed = $data_detail_tf['file_name'];

        $file_path_signed = $data_detail_tf['file_path'];

        //$ext = '.'.pathinfo($file_name_signed, PATHINFO_EXTENSION);
        $ext = '.pdf';
        $t=time();
        $generatedName_New = date('Y-m-d').'_No'.$No.'_'.md5($t.$file_name_signed).'_'.$t.'t'.$tpositionId.'m'.$mainId.$ext;

        //Copy file signed from table sign_detail.
        $filePath_signed = '../../document'.$file_path_signed;
        
        // path approv not signed
        
        $filePath_not_signed = $path.$generatedName_New;

        if (@copy($filePath_signed, $filePath_not_signed)) {

            // insert detail new signatured
            $file_path_not_signed = '/'.$path_approv_not_signed.$generatedName_New;

            $ins_detail = mysqli_query($con, "INSERT INTO sign_detail SET 
                main_id         = '$mainId',
                tposition_id    = '$tpositionId',
                file_name       = '$file_name_signed',
                file_path       = '$file_path_not_signed',
                sign_status     = '0',
                upload_date     = '$timeAction',
                sign_date       = '$timeAction'
                ");

            $detail_id_new = mysqli_insert_id($con);

            //Add new row  sign move
            $ins_move = mysqli_query($con, "INSERT INTO sign_move SET 
            main_id         = '$mainId',
            detail_id       = '$detail_id_new',
            tposition_id    = '$tpositionId',
            activity        = 'TranferTo',
            time            = '$timeAction'
            ");

             // update sign_status row before signature form
             $upd_detail = mysqli_query($con, "UPDATE sign_detail SET 
             sign_status     = '3' 
             WHERE detail_id = '$data_detail_tf[detail_id]'
             ");

            // update main_status
            $upd_main = mysqli_query($con, "UPDATE sign_main SET main_status  = '1' 
                WHERE main_id = '$mainId'
            ");

            // update sequence status = 1
            $upd_sequence = mysqli_query($con, "UPDATE sign_sequence SET sequ_transfer  = '1' 
                WHERE main_id = '$mainId' 
                AND tposition_id = '$row_transfer[tposition_id]' 
                AND sequ_status = '1' 
                AND sequ_signed_status = 'C'
            ");

            
        }else {
            $status = 'false';
        }

    } //end while
}
         
$objsql = "INSERT INTO sign_sequence(
    sequ_no,
    main_id,
    tposition_id,
    sequ_status)
    VALUES(null,'$mainId', '$tpositionId', '1')";
if(mysqli_query($con, $objsql)){
    $status = 'true';
}
    
$data[] = array(
    'status'=> $status,
    'mainId'=> $mainId,
    'respon'=> ''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
