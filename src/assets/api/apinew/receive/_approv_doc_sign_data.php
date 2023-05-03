<?php
session_start();

require_once "../db.php";

$mainId = $_GET['main_id'];

if($mainStatus == '1')
    $condition = "AND dt.sign_status = '0'";
else if($mainStatus == '3' || $mainStatus == '4')
    $condition = "AND dt.sign_status IN ('2')";


//แสดงรายละเอียดคนเดียวกัน
$objsql = "SELECT s.sequ_no, s.main_id, t.tposition_id, t.tposition_name, p.position_name
    FROM  sign_sequence s 
    LEFT JOIN takeposition t ON s.tposition_id = t.tposition_id 
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE s.main_id = '$mainId' 
    AND s.sequ_status = '1'
    GROUP BY t.tposition_id
    ORDER BY s.sequ_no ASC";

//04/08/2564 แยกตามลำดับ
// $objsql = "SELECT s.sequ_no, s.main_id, s.sequ_signed_status, t.tposition_id, t.tposition_name, p.position_name
//     FROM  sign_sequence s 
//     LEFT JOIN takeposition t ON s.tposition_id = t.tposition_id 
//     LEFT JOIN position p ON t.position_id = p.position_id
//     WHERE s.main_id = '$mainId' 
//     AND s.sequ_status = '1'
//     ORDER BY s.sequ_no ASC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;

while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    
    /*
    $dateWrite = explode("-",$objdata['edoc_datewrite']);
    $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
    */

    // $sql_move = "SELECT sm.activity, m.main_id, m.timeMax
    //     FROM (
    //         SELECT main_id, MAX(time) as timeMax FROM sign_move 
    //         WHERE main_id = '$mainId' 
    //         AND  tposition_id = '$objdata[tposition_id]'
    //     ) m
    //     INNER JOIN sign_move sm ON sm.main_id = m.main_id AND sm.time = m.timeMax
    //     WHERE sm.main_id = '$mainId' 
    //     AND sm.tposition_id = '$objdata[tposition_id]' 
        
    //     ";
    // $res_move = mysqli_query($con, $sql_move);
    // $row_max_activity = mysqli_fetch_assoc($res_move);

    // if($row_max_activity['activity'] == 'Upload' || $row_max_activity['activity'] == 'TranferTo')
    //     $add_dismiss = true;
    // else
    //     $add_dismiss = false;

    $row_sequence = mysqli_fetch_assoc(
        mysqli_query($con,"SELECT sequ_transfer 
        FROM sign_sequence 
        WHERE main_id ='$mainId'
        AND tposition_id = '$objdata[tposition_id]'
        AND sequ_status = '1' 
        ")
    );

        if($row_sequence['sequ_transfer'] == '0'){
            $add_dismiss = true;
        }
        else {
            $add_dismiss = false;
        }
        
        $objsql_detail = "SELECT mdt.main_id, 
            dt.detail_id, 
            dt.file_name, 
            dt.file_path, 
            dt.sign_status, 
            mdt.MaxUploadDate, 
            dt.sign_date, 
            dt.remark, 
            dt.detail_status, 
            t.tposition_name,
            t.tposition_id,  
            p.position_name,
            dt.remark
        FROM  (
            SELECT main_id, MAX(upload_date) as MaxUploadDate 
            FROM sign_detail 
            WHERE main_id = '$mainId'  
            AND detail_status = '1' 
            AND sign_status in ('0','2','3', '4', '5')
            AND tposition_id = '$objdata[tposition_id]' 
            GROUP BY detail_id
            
        ) mdt
        INNER JOIN sign_detail dt ON dt.main_id = mdt.main_id AND dt.upload_date = mdt.MaxUploadDate
        INNER JOIN takeposition t ON dt.tposition_id = t.tposition_id 
        INNER JOIN position p ON t.position_id = p.position_id 
        WHERE dt.main_id = '$mainId'  
        AND dt.detail_status = '1' 
        AND dt.sign_status in ('0','2','3','4', '5')
        AND dt.tposition_id = '$objdata[tposition_id]' 
        GROUP BY dt.detail_id
        ORDER BY dt.detail_id DESC";
        
        $objrs_detail = mysqli_query($con, $objsql_detail);
        $k = 0;
        $data_detail = array();

        while($row_detail = mysqli_fetch_assoc($objrs_detail)){ $k++;
            
            /*
            $dateWrite = explode("-",$objdata['edoc_datewrite']);
            $dateWriteNew = $dateWrite[2].'/'.$dateWrite[1].'/'.($dateWrite[0]+543);
            */

            $arr_name_file = explode("_edoc_", $row_detail['file_name']);
            $shot_file_name = $arr_name_file[1];

            if($row_detail['tposition_name'] != '')
                $tposition_name = $row_detail['tposition_name'];
            else
                $tposition_name = "รอเสนอ";
            if($row_detail['remark'] == ''  )
                $Remark = '-';
            else 
                $Remark = $row_detail['remark'];

            $data_detail[] = array(
                'No'=>$k,
                'detailId'=>$row_detail['detail_id'],
                'mainId'=>$row_detail['main_id'],
                'fileName'=>$row_detail['file_name'],
                'filePath'=>$row_detail['file_path'],
                'shotFileName'=>$shot_file_name,
                'remarkComment'=>$row_detail['remark'],
                'uploadDate'=>$row_detail['MaxUploadDate'],
                'signDate'=>$row_detail['sign_date'],
                'detailStatus'=>$row_detail['detail_status'],
                'tpositionName'=>$tposition_name,
                'positionName'=>$row_detail['position_name'],
                'signStatus'=>$row_detail['sign_status'],
                'res_sql'=> $objsql_detail,
                'MaxUploadDate'=>$row_detail['MaxUploadDate'],
                'remark'=>$Remark
            );

        }
    
    //check senback cancel
    $row_senbackcancel = mysqli_fetch_assoc(mysqli_query($con,"SELECT activity FROM sign_move
        WHERE main_id ='$mainId'
        AND move_status = '1'
        AND activity = 'Remark'
        ")
    );

    $data[] = array(
        'No'=> $i,
        'sequNo'=> $objdata['sequ_no'],
        'mainId'=> $objdata['main_id'],
        'signerName'=> $objdata['tposition_name'],
        'tpositionId'=> $objdata['tposition_id'],
        'positionName'=> $objdata['position_name'],
        'signDetail'=>$data_detail,
        'add_dismiss'=> $add_dismiss,
        'row_senbackcancel'=> $row_senbackcancel,
        'sequ_signed_status'=> $objdata['sequ_signed_status'],
        'objsql_detail'=>$objsql_detail
    );

}

$objsqlMaxNo = "SELECT MAX(sequ_no) AS sequ_max_no FROM sign_sequence WHERE main_id = '$mainId'";
$objrsMaxNo = mysqli_query($con, $objsqlMaxNo);
$objdataMaxNo = mysqli_fetch_assoc($objrsMaxNo);

$sequMaxNo = $objdataMaxNo['sequ_max_no'];
$datamax_no[] = array('sequMaxNo'=>$sequMaxNo);

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "sequ_max"=>$datamax_no));
