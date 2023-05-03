<?php
include_once "../db.php";

$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$departId = $request->departid; //หน่วยงาน
@$departIduser = $request->departid_user; //รหัสหน่วยงานของเจ้าของหนังสือ
@$userType = $request->usertype; //ประเภทของผู้ใช้งาน

@$edocTypeId = $request->edoctype;
@$dateSendStart = $request->dateSendStart;
@$dateSendEnd = $request->dateSendEnd;
@$edocDateStart = $request->edocDateStart;
@$edocDateEnd = $request->edocDateEnd;
// ความลับ
if ($request->secrets_1 != '') $Secrets1 = '1';
if ($request->secrets_2 != '') $Secrets2 = '2';
if ($request->secrets_3 != '') $Secrets3 = '3';
if ($request->secrets_4 != '') $Secrets4 = '4';
//ความเร่งด่วน
if ($request->rapid_1 != '') $Rapid1 = '1';
if ($request->rapid_2 != '') $Rapid2 = '2';
if ($request->rapid_3 != '') $Rapid3 = '3';
if ($request->rapid_4 != '') $Rapid4 = '4';

@$docNo = $request->docNo;
@$Headline = $request->headline;
@$Receiver = $request->receiver;
@$Sender = $request->sender;
@$Comment = $request->comment;
@$departSearch = $request->departSearch;

$row = $request->row;
$rowperpage = $request->rowperpage;

$perpage_cond = $request->perpage_cond;
$perpage_cond2 = $request->perpage_cond2;

$total_row = $request->total_row;


if ($row > 0) { // กรณี่ที่มีการดูเพิ่มเติม
    $condition = $perpage_cond;
    $condition2 = $perpage_cond2;
    $numrow = $total_row;
} else { //ครั้งแรกที่ค้นหาข้อมูล
    $condition = "WHERE e.status <> '' ";

    if ($departId) {
        if($departId == '35') {
            $condition .= "AND eu.depart_id = '35'";
        } else {
            $condition .= "AND e.depart_id = '$departSearch'";
        }
    }

    if ($dateSendStart) {
        $dateStart_array = explode("/", $dateSendStart);
        $dateStart_new = $dateStart_array[2] . '-' . $dateStart_array[1] . '-' . $dateStart_array[0];
        $condition .= "AND e.sent_date >= '$dateStart_new'";
    }

    if ($dateSendEnd) {
        $dateEnd_array = explode("/", $dateSendEnd);
        $dateEnd_new = $dateEnd_array[2] . '-' . $dateEnd_array[1] . '-' . $dateEnd_array[0];
        $condition .= "AND e.sent_date <= '$dateEnd_new'";
    }


    if ($edocDateStart) {
        $edocDateStart_array = explode("/", $edocDateStart);
        $edocDateStart_new = $edocDateStart_array[2] . '-' . $edocDateStart_array[1] . '-' . $edocDateStart_array[0];
        $condition .= "AND e.doc_date >= '$edocDateStart_new'";
    }

    if ($edocDateEnd) {
        $edocDateEnd_array = explode("/", $edocDateEnd);
        $edocDateEnd_new = $edocDateEnd_array[2] . '-' . $edocDateEnd_array[1] . '-' . $edocDateEnd_array[0];
        $condition .= "AND e.doc_date <= '$edocDateEnd_new'";
    }

    if ($edocTypeId != '') {
        $condition .= "AND e.edoc_type_id = '$edocTypeId'";
    }

    if ($numberReceive) {
        $condition .= "AND s.sent_no like '%$numberReceive%'";
    }

    if ($docNo) {
        $condition .= "AND e.doc_no like '%$docNo%'";
        //$condition .= "AND (SUBSTRING_INDEX(e.doc_no,'/',-1) = '$docNo')";
    }

    if ($Headline) {
        $condition .= "AND e.headline LIKE '%$Headline%'";
    }


    if ($Receiver) {
        $condition .= "AND e.receiver LIKE '%$Receiver%'";
    }

    if ($Sender) {
        $condition .= "AND e.sender LIKE '%$Sender%'";
    }

    if ($Comment) {
        $condition .= "AND e.comment LIKE '%$Comment%'";
    }

    $condition2 .= " AND e.secrets in ('$Secrets1','$Secrets2', '$Secrets3', '$Secrets4')";

    $condition2 .= " AND e.rapid in ('$Rapid1','$Rapid2', '$Rapid3','$Rapid4')";

    if ($departId == '35') { //เลขหนังสือของมหาลัย
        $objsql_row = "SELECT e.edoc_id
        FROM edoc_univ_no eu
        LEFT JOIN edoc e ON eu.edoc_id = e.edoc_id
        LEFT JOIN department d ON e.depart_id = d.depart_id
        LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
        LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
        LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
        LEFT JOIN edoc_user u ON e.user_id = u.user_id
        $condition
        $condition2
        ORDER BY e.edoc_id DESC";
        $objrs_row = mysqli_query($con, $objsql_row);
        $numrow = mysqli_num_rows($objrs_row);
    } else {

        $objsql_row = "SELECT e.edoc_id
            FROM edoc e
            LEFT JOIN department d ON e.depart_id = d.depart_id
            LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
            LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
            LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
            LEFT JOIN edoc_user u ON e.user_id = u.user_id
            $condition
            $condition2
            ORDER BY e.edoc_id DESC";
        $objrs_row = mysqli_query($con, $objsql_row);
        $numrow = mysqli_num_rows($objrs_row);
    }
}

if ($departId == '35') { //เลขหนังสือของมหาลัย

    $objsql = "SELECT e.edoc_id,
            e.edoc_type_id,
            t.edoc_type_name,
            e.doc_no,
            e.doc_date,
            e.headline,
            e.receiver,
            e.comment,
            e.status,
            rp.rapid_name,
            e.secrets,
            sc.secrets_name,
            e.depart_id,
            d.depart_name,
            e.sender_depart,
            e.sender,
            CONCAT(u.user_fname,' ',u.user_lname) as userFullname
            FROM edoc_univ_no eu
            LEFT JOIN edoc e ON eu.edoc_id = e.edoc_id
            LEFT JOIN department d ON e.depart_id = d.depart_id
            LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
            LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
            LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
            LEFT JOIN edoc_user u ON e.user_id = u.user_id
    $condition
    $condition2
    ORDER BY e.edoc_id DESC 
    limit $row, $rowperpage";

} else {

    $objsql = "SELECT e.edoc_id,
    e.edoc_type_id,
    t.edoc_type_name,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.status,
    rp.rapid_name,
    e.secrets,
    sc.secrets_name,
    e.depart_id,
    d.depart_name,
    e.sender_depart,
    e.sender,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    $condition
    $condition2
    
    ORDER BY e.edoc_id DESC 
    limit $row, $rowperpage";
}
$objrs = mysqli_query($con, $objsql);
$rows = mysqli_num_rows($objrs);
$i = 0;

while ($objdata = mysqli_fetch_assoc($objrs)) {
    //กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  
    if ($objdata['depart_id'] == $departIduser || $userType == 'SA') {
        //$condition = "AND e.secrets IN ('$Secrets1','$Secrets2','$Secrets3','$Secrets4')";
        $roleSend = 'Y'; //Can edit to send books.
    } else {
        //$condition .= "AND e.secrets = '$Secrets1'";
        $roleSend = 'N'; //cannot edit to send books.
    }

    $edocDate = explode("-", $objdata['doc_date']);
    $edocDateNew = $edocDate[2] . '/' . $edocDate[1] . '/' . ($edocDate[0] + 543);

    if ($objdata['secrets'] == '1') {
        $data[] = array(
            'No' => $i,
            'edocId' => $objdata['edoc_id'],
            'edocTypeId' => $objdata['edoc_type_id'],
            'edocTypeName' => $objdata['edoc_type_name'],
            'edocNo' => $objdata['doc_no'],
            'edocDate' => $edocDateNew,
            'filePath' => $objdata['pdf_path'],
            'fileName' => $objdata['pdf_name'],
            'Headline' => $objdata['headline'],
            'Receiver' => $objdata['receiver'],
            'Comment' => $objdata['comment'],
            'Rapid' => $objdata['rapid_name'],
            'Secrets' => $objdata['secrets_name'],
            'departName' => $objdata['depart_name'],
            'senderDepart' => $objdata['sender_depart'],
            'Sender' => $objdata['sender'],
            'edocStatus' => $objdata['status'],
            'sendStatus' => $objdata['sent_status'],
            'roleSend' => $roleSend,
            'departId_send' => $objdata['depart_id'],
            'departIdUser' => $departIduser,
            'userType' => $userType
        );
    } else {

        if (($objdata['depart_id'] == $departIduser) || ($userType == 'SA')) {
            $data[] = array(
                'No' => $i,
                'edocId' => $objdata['edoc_id'],
                'edocTypeId' => $objdata['edoc_type_id'],
                'edocTypeName' => $objdata['edoc_type_name'],
                'edocNo' => $objdata['doc_no'],
                'edocDate' => $edocDateNew,
                'filePath' => $objdata['pdf_path'],
                'fileName' => $objdata['pdf_name'],
                'Headline' => $objdata['headline'],
                'Receiver' => $objdata['receiver'],
                'Comment' => $objdata['comment'],
                'Rapid' => $objdata['rapid_name'],
                'Secrets' => $objdata['secrets_name'],
                'departName' => $objdata['depart_name'],
                'Sender' => $objdata['sender'],
                'edocStatus' => $objdata['status'],
                'sendStatus' => $objdata['sent_status'],
                'roleSend' => $roleSend,
                'departId_send' => $objdata['depart_id'],
                'departIdUser' => $departIduser,
                'userType' => $userType
            );
        }
    }
}


@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

//print json_encode(array("data"=>$data, "row_data"=>$row_data));

if (count($data) > 0) {
    $row_data[] = array('rowReceive' => $numrow, 'resp' => '', 'perpage_cond' => $condition, 'perpage_cond2' => $condition2);

    @print json_encode(array("data" => $data, "row_data" => $row_data));
}
exit;
