<?php
session_start();
require('../db.php');
@$depart_id = $_GET['depart'];

@$Year = ($_GET['year'] - 543);
@$searchType = $_GET['search_type'];

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  

$condition = "";

if($searchType == '2'){
    $condition = "AND m.main_status in ('2')";
}else if($searchType == '3'){
    $condition = "AND m.main_status in ('3','4', '5', 'N')";
}else{
    $condition = "";
}

$objsqlMain = "SELECT 
    m.main_id,
    m.main_status,
    m.main_type,
    m.edoc_id,
    m.depart_id,
    m.status_get_no,
    m.apply_number_univ
    FROM sign_main m 
    WHERE year(create_date) = '$Year' 
    AND m.doc_type = '1' 
    AND m.apply_number_univ IN (1,2)
    $condition
    ORDER BY m.main_id DESC";
    
$objrs = mysqli_query($con, $objsqlMain);
$i = 0;
while($objdataMain = mysqli_fetch_assoc($objrs)){ $i++;

    $objsqlDoc = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    r.rapid_name,
    s.secrets_name,
    d.depart_name,
    e.status,
    e.sender,
    t.edoc_type_name,
    e.edoc_datewrite,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE e.edoc_id = '$objdataMain[edoc_id]'
    ORDER BY e.edoc_id DESC";

    $objdata = mysqli_fetch_array(mysqli_query($con, $objsqlDoc));

    if($objdata['doc_no'] == '') {
        $docNo = 'รอดำเนินการ';
        $edocDateNew = '-';
    } else {
        $docNo = $objdata['doc_no'];
        //วันที่หนังสือ
        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    }
    

    $objsql_move = "SELECT mv.activity,  t.tposition_id, t.tposition_name, sd.file_name, sd.file_path, 
            mx.moveidMax, date(mv.time) as date_activity, time(time) as time_activity
        FROM (

            SELECT main_id, MAX(move_id) as moveidMax FROM sign_move 
            WHERE main_id = '$objdataMain[main_id]' 
            AND move_status = '1' 
            GROUP BY main_id
            
        ) mx
        INNER JOIN sign_move mv ON mv.main_id = mx.main_id AND mv.move_id = mx.moveidMax
        LEFT JOIN sign_detail sd ON mv.detail_id = sd.detail_id
        LEFT JOIN takeposition t ON mv.tposition_id = t.tposition_id
        WHERE mv.main_id = '$objdataMain[main_id]' 
        AND mv.move_status = '1' 
        GROUP BY mv.main_id
        ";
    $objrs_move = mysqli_query($con, $objsql_move);
    $dataMove = mysqli_fetch_array($objrs_move);

    @$activity = $dataMove['activity'];
    @$tposition_id =  $dataMove['tposition_id'];
    @$tposition_name = $dataMove['tposition_name'];
    
    @$timeActivity = $dataMove['time_activity'];
    @$dateActivity = $dataMove['date_activity'];

    if($objdata['status'] == '1'){
        $activity = 'DoneSent';
    }


    $move_status = '';

    if($activity == 'Upload')
        $move_status = 'เสนอลงนาม';
    else if($activity == 'Signatured')
        $move_status = 'ลงนามแล้ว';
    else if($activity == 'TranferTo')
        $move_status = 'เสนอลงนาม';
    else if($activity == 'Done')
        $move_status = 'เสร็จสิ้น';
    else if($activity == 'DoneNotnumber')
        $move_status = 'ไม่ออกเลข';
    else if($activity == 'DoneSent')
        $move_status = 'ส่งหนังสือแล้ว';
    else if($activity == 'GetNumber')
        $move_status = 'ออกเลขหนังสือ';
    else if($activity == 'ApplyNumber')
        $move_status = 'ขอเลขมหาลัย';

    
    //Check sign doc complete
    $sql_detail_sign = "SELECT sign_status FROM sign_detail 
        WHERE main_id='$objdataMain[main_id]' 
        AND tposition_id = '$tposition_id' 
        AND sign_status = '0' 
        AND detail_status = '1'
        ";
    $res_detail_sign = mysqli_query($con, $sql_detail_sign);
    $num_row_not_sign = mysqli_num_rows($res_detail_sign);

    if($num_row_not_sign >= 1 && $activity == 'Signatured'){ //หากยังลงนามไม่ครบ
        $move_status = 'เสนอลงนาม';
        $activity = 'Upload';
    }

    $filePath= $dataMove['file_path'];
    $fileName= $dataMove['file_name'];

    //กรณีขอเลขหนังสือมหาลัย (เสนอฉบับจริง) และไม่เสนอต่อหลังจากออกเลข
    if($objdataMain['apply_number_univ'] == '2' && $objdataMain['status_get_no'] == '0'){
        // Get data from edoc_univ_no
        $row_univno = mysqli_fetch_array(mysqli_query($con, "SELECT file_path, file_name 
            FROM edoc_univ_no 
            WHERE edoc_id = '$objdataMain[edoc_id]' 
            AND depart_id = '$objdataMain[depart_id]'
        "));

        $fileName = $row_univno['file_name'];
        $filePath = $row_univno['file_path'];
    }

    //Check num row file of sign

    if($activity == 'DoneSent'){
        $sql_num_file = "SELECT sign_status FROM sign_detail 
            WHERE main_id='$objdataMain[main_id]' 
            AND sign_status in ('2', '4')
            AND detail_status = '1'
            ";
    }else {

        $sql_num_file = "SELECT m.main_id, m.Activity, mx.MaxTime
        FROM (
              SELECT main_id, MAX(time) as MaxTime
              FROM sign_move 
              WHERE main_id='$objdataMain[main_id]'
              GROUP BY main_id
        ) mx
        INNER JOIN sign_move m
        ON m.main_id = mx.main_id AND m.time = mx.MaxTime";
    }
    
    $res_detail_file = mysqli_query($con, $sql_num_file);
    $num_row_file = mysqli_num_rows($res_detail_file);

    $data[] = array(
        'No'=>$i,
        'mainId'=>$objdataMain['main_id'],
        'mainType'=>$objdataMain['main_type'],
        'approvStatus'=>$objdataMain['main_status'],
        'approvStatusGetNo'=>$objdataMain['status_get_no'],
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$docNo,
        'edocDate'=>$edocDateNew,
        'edocTypeName'=>$objdata['edoc_type_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'Sender'=>$objdata['sender'],
        'dateWrite'=>$objdata['edoc_datewrite'],
        'moveActivity'=> array('Activity'=>$activity,
                                'status'=>$move_status,
                                'tpositionId'=>$tposition_id, 
                                'tpositionName'=>$tposition_name,
                                'filePath'=>$filePath,
                                'fileName'=>$fileName,
                                'time'=> $timeActivity,
                                'date'=> $dateActivity,
                            ),
        'num_row_not_sign'=> $num_row_not_sign,
        'num_row_file'=> $num_row_file,
        'docStatus'=>$objdata['status']
    );
}

//$data[] = array('sql'=>$objsql);

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

@print json_encode(array("data"=>$data));
