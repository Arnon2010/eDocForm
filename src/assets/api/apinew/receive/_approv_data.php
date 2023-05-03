<?php
require('../db.php');
@$departId = $_GET['depart_id'];
@$searchType = $_GET['searchtype'];

// page reload limit
@$row = $_GET['row'];
@$rowperpage = $_GET['rowperpage'];

function firstRowDetail($main_id) {
    global $con;
    $objsql = "SELECT file_name, file_path 
    FROM sign_detail
    WHERE main_id = '$main_id' AND detail_status = '1' 
    LIMIT 1
    ";
    $objrs = mysqli_query($con, $objsql);
    $data = mysqli_fetch_array($objrs);
    $file_name = $data['file_name'];
    return $file_name;
}

function moveActivity($main_id){
    global $con;
    // max move id
    $objsql_max = "SELECT MAX(move_id) as max_move_id 
        FROM sign_move 
        WHERE main_id = '$main_id' AND move_status = '1'";
    $objrs_max = mysqli_query($con, $objsql_max);
    $data_max = mysqli_fetch_array($objrs_max);
    $move_id_max = $data_max['max_move_id'];

    $objsql = "SELECT activity, tposition_id, detail_id,
        date(time) as date_activity, 
        time(time) as time_activity
        FROM sign_move
        WHERE main_id = '$main_id' 
        AND move_id = '$move_id_max'";
    $objrs = mysqli_query($con, $objsql);
    $data = mysqli_fetch_array($objrs);

    $tpositionId = $data['tposition_id'];
    $detailId = $data['detail_id'];

    $timeActivity = $data['time_activity'];
    $dateActivity = $data['date_activity'];

    if($data['activity'] == 'Upload')
        $condition = " AND d.sign_status = '0'";
    else if($data['activity'] == 'Signatured')
        $condition = " AND d.sign_status in ('2','3')";
    else if($data['activity'] == 'TranferTo')
        $condition = " AND d.sign_status = '0'";
    else if($data['activity'] == 'Done')
        $condition = " AND d.sign_status = '4'";

    $objsqlDetail = "SELECT d.file_name, d.file_path, d.sign_status, d.tposition_id, t.tposition_name
        FROM sign_detail d 
        LEFT JOIN takeposition t ON d.tposition_id = t.tposition_id
        WHERE d.tposition_id = '$tpositionId' 
        AND d.main_id = '$main_id' 
        AND d.detail_status = '1' 
        AND d.detail_id = '$detailId'
        $condition";

    $objrsDetail = mysqli_query($con, $objsqlDetail);
    $dataDetail = mysqli_fetch_array($objrsDetail);

    @$fileName = $dataDetail['file_name'];
    @$filePath = $dataDetail['file_path'];
    @$signStatus = $dataDetail['sign_status'];

    return array($move_id_max, 
                    $data['activity'], 
                        $dataDetail['tposition_id'], 
                            $dataDetail['tposition_name'],
                                $detailId,
                                    $fileName,
                                        $filePath, 
                                            $signStatus,
                                                $timeActivity,
                                                    $dateActivity);
}

function signDetail($main_id){
    global $con;
    
    $objsqlSignDetail = "SELECT * FROM sign_detail WHERE main_id = '$main_id'";
    $objrsSignDetail = mysqli_query($con, $objsqlSignDetail);
    while($objData = mysqli_fetch_array($objrsSignDetail)){
        $data[] = array(
            'mainId'=>$objData['main_id'],
            'tpositionId'=>$objData['tposition_id'],
            'fileName'=>$objData['file_name'],
            'filePath'=>$objData['file_path'],
            'signStatus'=>$objData['sign_status'],
        );
    }
    return $data;
}

function getReceiveNo ($con, $depart_id, $edoc_id) {
    $row_receive = mysqli_fetch_array( mysqli_query($con, "SELECT receive_no FROM edoc_receive 
        WHERE depart_id = '$depart_id' AND edoc_id = '$edoc_id'")
    );

    return $row_receive['receive_no'];
}


if($searchType == '2'){
    $condition = "AND m.main_status != '4'";
}else if($searchType == '3'){
    $condition = "AND m.main_status = '4'";
}else{
    $condition = "";
}


// ค้นหา
$qSearch = $_GET['qsearch'];

if($qSearch == 'notSearch'){
    $condition .= "";
} else {
    $arr_qsearch = explode("/",$qSearch);
    $date_search = ($arr_qsearch['2']-543).'-'.$arr_qsearch['1'].'-'.$arr_qsearch['0'];
    
    // $condition .= " AND (e.doc_no like '%$qSearch'
    //     OR e.doc_date = '$date_search' 
    //     OR e.headline like '%$qSearch%' 
    //     OR e.receiver like '%$qSearch%' 
    //     OR e.comment like '%$qSearch%' 
    //     OR e.sender like '%$qSearch%'
    //     OR e.sender_depart like '%$qSearch%'
    //     OR e.comment like '%$qSearch%'
    //     OR rp.rapid_name like '%$qSearch%'
    //     OR sc.secrets_name like '%$qSearch%'
    //     OR d.depart_name like '%$qSearch%' 
    //     OR t.edoc_type_name like '%$qSearch%' 
    //     OR u.user_fname like '%$qSearch%' 
    //     OR u.user_lname like '%$qSearch%' 
    //     OR m.doc_receive_no like '%$qSearch')";

    $condition .= " AND (e.doc_no like '%$qSearch%'
        OR m.doc_receive_no like '%$qSearch'
        OR e.doc_date = '$date_search' 
        OR e.headline like '%$qSearch%' 
        OR e.receiver like '%$qSearch%' 
        )";
}

$objsql = "SELECT 
    m.main_id,
    m.main_status,
    m.main_type,
    m.doc_receive_no,
    e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.sender_depart,
    rp.rapid_name,
    sc.secrets_name,
    m.depart_id,
    d.depart_name,
    e.sender,
    t.edoc_type,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname,
    u.user_fname
    FROM sign_main m 
    LEFT JOIN edoc e ON m.edoc_id = e.edoc_id 
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON m.user_id = u.user_id
    WHERE m.depart_id = '$departId' 
    AND m.doc_type = '2' 
    $condition 
    ORDER BY m.create_date DESC, m.create_time DESC 
    limit $row, $rowperpage";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;

    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);

    @list($moveId, $activity, $tposition_id, $tposition_name, $detail_id, $file_name, $file_path, $sign_status, $time_activity, $date_activity) = moveActivity($objdata['main_id']);

    if($activity == 'Upload')
        $move_status = 'เสนอถึง';
    else if($activity == 'Signatured')
        $move_status = 'เกษียนแล้ว';
    else if($activity == 'TranferTo')
        $move_status = 'เสนอถึง';
    else if($activity == 'Done')
        $move_status = 'เสร็จสิ้น';
    else if($activity == 'DoneOriginal')
        $move_status = 'เสนอฉบับจริง';
    else if($activity == 'Remark')
        $move_status = 'ถูกส่งกลับ';
    else if($activity == '')
        $move_status = false; //ยังไม่มีการเสนอหนังสือ

    //$receiveNo = getReceiveNo($con, $departId, $objdata['edoc_id']);

    $receiveNo = $objdata['doc_receive_no'];

    // ***** update เลขรับหนังสือ table sign_main ***** //

    // $update_main = mysqli_query($con, "UPDATE sign_main SET doc_receive_no = '$receiveNo' 
    //     WHERE main_id = '$objdata[main_id]'");

    // $row_detail = mysqli_fetch_array(mysqli_query($con, "SELECT detail_id, tposition_id  
    //     FROM sign_detail 
    //     WHERE main_id = '$objdata[main_id]' AND sign_status = '4' AND detail_status = '1'
    // "));

    // $update_move = mysqli_query($con, "UPDATE sign_move SET detail_id = '$row_detail[detail_id]' 
    //     WHERE main_id = '$objdata[main_id]' 
    //     AND tposition_id = '$row_detail[tposition_id]' 
    //     AND activity = 'Done' 
    //     AND detail_id in ('0','')
    // ");

    $file_name_first = firstRowDetail($objdata['main_id']);

    $data[] = array(
        'No'=>$i,
        'mainId'=>$objdata['main_id'],
        'mainType'=>$objdata['main_type'],
        'edocId'=>$objdata['edoc_id'],
        'edocType'=>$objdata['edoc_type'],
        'edocNo'=>$objdata['doc_no'],
        'receiveNo'=>$receiveNo,
        'edocDate'=>$edocDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'sender_depart'=>$objdata['sender_depart'],
        'departId'=>$objdata['depart_id'],
        'departName'=>$objdata['depart_name'],
        'senderDepart'=>$objdata['sender_depart'],
        'userReceive'=>$objdata['user_fname'],
        'approvStatus'=>$objdata['main_status'],
        'moveActivity'=> array('moveId'=>$moveId, 
                'status'=>$move_status, 
                'Activity'=>$activity, 
                'tpositionId'=>$tposition_id, 
                'tpositionName'=>$tposition_name,
                'detailId'=>$detail_id,
                'fileNameSign'=>$file_name_first,
                'filePathSign'=>$file_path,
                'signStatus'=>$sign_status,
                'time'=>$time_activity,
                'date'=>$date_activity),
        'signDetail'=> signDetail($objdata['main_id']),
        'sql'=>$objsql
    );
}

//$data[] = array('resp'=>'');

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

if(count($data) > 0)
    print json_encode($data);
    
exit;
