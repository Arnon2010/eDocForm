<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("content-type:text/javascript;charset=utf-8");

header("Content-Type: application/json; charset=utf-8", true, 200);

include "config.php";
include "../rmutsv_login.php";

$postjson = json_decode(file_get_contents('php://input'), true);

@$today = date('Y-m-d H:i:s');


if($postjson['aksi'] == "proses_register"){

    @$checkmail = mysqli_fetch_array(mysqli_query($mysqli,"SELECT email_address FROM tb_users 
        WHERE email_address = '$postjson[email_address]'"));

    if($checkmail['email_address'] == $postjson['email_address']){
        $result = json_encode(array('success'=>false, 'msg'=>'Email is already'));
    }else{
        $password = md5($postjson['password']);
        $insert = mysqli_query($mysqli, "INSERT INTO tb_users SET 
            your_name = '$postjson[your_name]',
            gender = '$postjson[gender]',
            date_birthday = '$postjson[date_birth]',
            email_address = '$postjson[email_address]',
            password = '$password',
            created_at = '$today'
        ");
    
        if($insert){
            $result = json_encode(array('success'=>true, 'msg'=>'Resigter successfuly'));
        }else{
            $result = json_encode(array('success'=>false, 'msg'=>'Resigter error'));
        }
    }

    echo $result;

}else if($postjson['aksi'] == "proses_login"){
    
    //$password = md5($postjson['user_password']);
    $User = trim($postjson['user_epassport']);
    $Pass = trim($postjson['user_password']);

    if(rmutsv_login::login("$User","$Pass")){

        $logindata = mysqli_fetch_array(mysqli_query($mysqli,"SELECT tposition_name, e_passport 
        FROM takeposition 
        WHERE e_passport = '$User'"));

        $objsql = "SELECT tposition_name, e_passport 
        FROM takeposition 
        WHERE e_passport = '$User'";

        $data = array(
            'e_passport'    => $logindata['e_passport'],
            'signerName'     => $logindata['tposition_name']
        );

        //$result = json_encode(array('success'=>true, 'result'=>$objsql));

        if($logindata){
            $result = json_encode(array('success'=>true, 'result'=>$data));
        }else{
            $result = json_encode(array('success'=>false));
        }

    }else {
        $result = json_encode(array('success'=>false, 'mesg'=>'ชื่อผู้ใช้ หรือรหัสผ่านของท่านไม่ถูกต้อง'));
    }

    echo $result;

}else if($postjson['aksi'] == "load_docs"){
    
    $data = array();
    //$query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $postjson[start], $postjson[limit]");
    
    $query = mysqli_query($mysqli,"SELECT 
            sm.edoc_id, 
            sm.main_id,
            sm.main_type,
            sm.main_status, 
            t.tposition_id, 
            t.tposition_name,
            ed.headline, 
            ed.doc_date,
            sd.detail_id,
            sd.upload_date,
            sd.file_path,
            sd.file_name,
            p.position_name,
            d.depart_id,
            d.depart_name,
            et.edoc_type_name,
            sd.read_status
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id 
        LEFT JOIN department d ON p.depart_id = d.depart_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status = '0' 
        AND sd.detail_status = '1'
        ORDER BY sd.detail_id DESC 
        LIMIT $postjson[start], $postjson[limit]");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;

        //$first_text_headline = substr($row['headline'], 0, 1);

        $data[] = array(
            'No'                    => $No,
            'detail_id'             => $row['detail_id'],
            'upload_date'           => $row['upload_date'],
            'file_path'             => $row['file_path'],
            'file_name'             => $row['file_name'],
            'headline'              => $row['headline'],
            'depart_id'             => $row['depart_id'],
            'edoc_id'               => $row['edoc_id'],
            'edoc_type_name'        => $row['edoc_type_name'],
            'doc_date'              => $row['doc_date'],
            'main_id'               => $row['main_id'],
            'main_status'           => $row['main_status'],
            'main_type'             => $row['main_type'],
            'tposition_id'          => $row['tposition_id'],
            'depart_name'           => $row['depart_name'],
            'tposition_name'        => $row['tposition_name'],
            'position_name'         => $row['position_name'],
            'read_status'           => $row['read_status'],
            'first_text_headline'   => $first_text_headline
        );
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}
else if($postjson['aksi'] == "load_docs_noti_alert"){
    
    $data = array();
    //$query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $postjson[start], $postjson[limit]");
    
    $query = mysqli_query($mysqli,"SELECT 
            ed.headline, 
            ed.doc_date,
            sd.detail_id,
            sd.upload_date,
            et.edoc_type_name,
            sd.read_status,
            sd.noti_status
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status = '0'
        AND sd.detail_status = '1'
        ORDER BY sd.detail_id DESC 
        ");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;

        //  noti status new 
        //  $noti_status = $row['noti_status'];

        //  $upload_date = $row['upload_date'];
 
        //  $nInterval = strtotime($today) - strtotime($upload_date);
        //  $nInterval = $nInterval/60;
 
        //  if($noti_status == '0' && $nInterval >= 5){
        //      $data[] = array(
        //          'id'     => $row['detail_id'],
        //          'text'   => $row['headline'],
        //          'title'  => $row['edoc_type_name'],
        //          'icon'   => 'http://example.com/icon.png'
             
        //      );
 
        //      $query_noti = mysqli_query($mysqli,"UPDATE sign_detail set noti_status = '1' 
        //          WHERE detail_id = '$row[detail_id]'
        //      ");
        //  }
 
        // noti status
        $noti_status = $row['noti_status'];

        if($noti_status == '0'){
            $data[] = array(
                'id'     => $row['detail_id'],
                'text'   => $row['headline'],
                'title'  => $row['edoc_type_name'],
                'icon'   => 'https://e-doc.rmutsv.ac.th/image/apps/icon/icon.png'
            );

            $query_noti = mysqli_query($mysqli,"UPDATE sign_detail set noti_status = '1' 
                WHERE detail_id = '$row[detail_id]'
            ");
        }
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}
else if($postjson['aksi'] == "load_docs_main"){
    
    $data = array();
    
    $query = mysqli_query($mysqli,"SELECT DISTINCT sd.main_id, 
            sm.edoc_id, 
            sm.doc_type,
            sm.depart_id,
            sm.doc_receive_no,
            t.tposition_id, 
            t.tposition_name,
            ed.headline, 
            ed.doc_date,
            ed.doc_no,
            ed.sender_depart,
            sd.detail_id,
            sd.upload_date,
            DATE_FORMAT(sd.upload_date, '%Y-%m-%d') DATEONLY, 
            DATE_FORMAT(sd.upload_date, '%H:%i:%s') TIMEONLY,
            sd.file_path,
            sd.file_name,
            p.position_name,
            d.depart_name,
            dd.depart_name AS edoc_depart_name,
            et.edoc_type_name,
            sd.read_status,
            sc.secrets_id,
            sc.secrets_name,
            rp.rapid_id,
            rp.rapid_name
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id 
        LEFT JOIN department d ON p.depart_id = d.depart_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN department dd ON ed.depart_id = dd.depart_id
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status = '0' 
        AND sd.detail_status = '1' 
        AND sm.main_status = '1'
        GROUP BY sd.main_id
        ORDER BY sd.detail_id DESC 
        LIMIT $postjson[start], $postjson[limit]");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;

        //$first_text_headline = substr($row['headline'], 0, 1);

        // $row_receive = mysqli_fetch_array( mysqli_query($mysqli, "SELECT receive_no 
        //     FROM edoc_receive 
        //     WHERE depart_id = '$row[depart_id]' AND edoc_id = '$row[edoc_id]'")
        // );

        // วันทีหนังสือ
        if($row['doc_no'] != ''){
            $doc_no = $row['doc_no'];
            $arr_doc_date = explode("-",$row['doc_date']);
            $doc_date = $arr_doc_date[2].'/'.$arr_doc_date[1].'/'.($arr_doc_date[0]+543);
        }else{
            $doc_no = 'รอดำเนินการ';
            $doc_date = 'รอดำเนินการ';
        }


        // วันที่และเวลาเสนอ
        $date_new = explode("-",$row['DATEONLY']);
        $upload_date = $date_new[2].'/'.$date_new[1].'/'.($date_new[0]+543).' เวลา '.$row['TIMEONLY'].' น.';

        $receiveNo = $row['doc_receive_no'];

        $data[] = array(
            'No'                    => $No,
            'docType'               => $row['doc_type'],
            'SecretsId'             => $row['secrets_id'],
            'Secrets'               => $row['secrets_name'],
            'RapidId'               => $row['rapid_id'],
            'Rapid'                 => $row['rapid_name'],
            'detail_id'             => $row['detail_id'],
            'upload_date'           => $upload_date,
            'file_path'             => $row['file_path'],
            'file_name'             => $row['file_name'],
            'headline'              => $row['headline'],
            'depart_id'             => $row['depart_id'],
            'edoc_id'               => $row['edoc_id'],
            'edoc_type_name'        => $row['edoc_type_name'],
            'doc_date'              => $doc_date,
            'main_id'               => $row['main_id'],
            'tposition_id'          => $row['tposition_id'],
            'depart_name'           => $row['depart_name'],
            'edoc_depart_name'      => $row['edoc_depart_name'],
            'sender_depart'         => $row['sender_depart'],
            'tposition_name'        => $row['tposition_name'],
            'position_name'         => $row['position_name'],
            'read_status'           => $row['read_status'],
            'first_text_headline'   => $first_text_headline,
            'receiveNo'             => $receiveNo
        );
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}

//รายการหนังสือลงนาม new
else if($postjson['aksi'] == "load_docs_tosign_retire_main"){
    
    $data = array();
    
    $query = mysqli_query($mysqli,"SELECT DISTINCT sd.main_id, 
            sm.edoc_id, 
            sm.doc_type,
            sm.depart_id,
            sm.doc_receive_no,
            t.tposition_id, 
            t.tposition_name,
            ed.headline, 
            ed.doc_date,
            sd.detail_id,
            sd.upload_date,
            sd.file_path,
            sd.file_name,
            p.position_name,
            d.depart_name,
            et.edoc_type_name,
            sd.read_status,
            sc.secrets_id,
            sc.secrets_name,
            rp.rapid_id,
            rp.rapid_name
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id 
        LEFT JOIN department d ON p.depart_id = d.depart_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status = '0' 
        AND sd.detail_status = '1'
        AND sm.doc_type = '2'
        GROUP BY sd.main_id
        ORDER BY sd.detail_id DESC 
        LIMIT $postjson[start], $postjson[limit]");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;
       
        $receiveNo = $row['doc_receive_no'];

        $data[] = array(
            'No'                    => $No,
            'docType'               => $row['doc_type'],
            'SecretsId'             => $row['secrets_id'],
            'Secrets'               => $row['secrets_name'],
            'RapidId'               => $row['rapid_id'],
            'Rapid'                 => $row['rapid_name'],
            'detail_id'             => $row['detail_id'],
            'upload_date'           => $row['upload_date'],
            'file_path'             => $row['file_path'],
            'file_name'             => $row['file_name'],
            'headline'              => $row['headline'],
            'depart_id'             => $row['depart_id'],
            'edoc_id'               => $row['edoc_id'],
            'edoc_type_name'        => $row['edoc_type_name'],
            'doc_date'              => $row['doc_date'],
            'main_id'               => $row['main_id'],
            'tposition_id'          => $row['tposition_id'],
            'depart_name'           => $row['depart_name'],
            'tposition_name'        => $row['tposition_name'],
            'position_name'         => $row['position_name'],
            'read_status'           => $row['read_status'],
            'first_text_headline'   => $first_text_headline,
            'receiveNo'             => $receiveNo
        );
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}

//รายละเอียดหนังสือที่จะลงนามและ เกษียน
else if($postjson['aksi'] == "load_single_data"){

    $data_detail = array();
    $query = mysqli_query($mysqli,"SELECT 
            sm.edoc_id, 
            sm.main_id, 
            sm.main_status,
            sm.main_type,
            sm.doc_type,
            ed.headline, 
            ed.receiver,
            ed.doc_date,
            ed.doc_no,
            ed.sender_depart,
            ed.depart_id as sender_depart_id,
            sm.depart_id,
            d.depart_name as deptname_edoc,
            md.depart_name as deptname_parent,
            mds.depart_name as deptname_sub,
            mds.depart_parent,
            et.edoc_type_name,
            sc.secrets_name,
            rp.rapid_name
        FROM sign_main sm
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id 
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        LEFT JOIN department d ON ed.depart_id = d.depart_id
        LEFT JOIN department mds ON sm.depart_id = mds.depart_id
        LEFT JOIN department md ON mds.depart_parent = md.depart_id
        WHERE sm.main_id = '$postjson[main_id]' 
        ");

    $row = mysqli_fetch_array($query);

    $arr_doc_date = explode("-",$row['doc_date']);
    $doc_date = $arr_doc_date[2].'/'.$arr_doc_date[1].'/'.($arr_doc_date[0]+543);

    if($row['depart_parent'] == 0) {
        $depart_submit = $row['deptname_sub'];
    } else {
        $depart_submit = $row['deptname_parent'].'/ '.$row['deptname_sub'];
    }
    $data = array(
        'doc_type'         => $row['doc_type'],
        'main_type'        => $row['main_type'],
        'edoc_id'          => $row['edoc_id'],
        'Secrets'          => $row['secrets_name'],
        'Rapid'            => $row['rapid_name'],
        'main_id'          => $row['main_id'],
        'headline'         => $row['headline'],
        'receiver'         => $row['receiver'],
        'edoc_type'        => $row['edoc_type_name'],
        'doc_no'           => $row['doc_no'],
        'doc_date'         => $doc_date,
        'agenc_dept'       => $row['deptname_edoc'],
        'depart_submit'    => $depart_submit,
        'depart_id'        => $row['depart_id'],
        'sender_depart'    => $row['sender_depart'],
        'sender_depart_id' => $row['sender_depart_id']
    );
     
    // รายการไฟล์หนังสือที่ต้องเกษียน หรือลงนาม
    
    $objsql_detail = "SELECT 
        sd.detail_id,
        sd.tposition_id,
        sd.upload_date,
        DATE_FORMAT(sd.upload_date, '%Y-%m-%d') DATEONLY, 
        DATE_FORMAT(sd.upload_date, '%H:%i:%s') TIMEONLY,
        sd.file_path,
        sd.file_name,
        sd.read_status,
        sd.sign_status,
        sd.detail_status,
        t.tposition_name,
        p.position_name
    FROM sign_detail sd 
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE t.e_passport = '$postjson[uid_passport]' 
    AND sd.main_id = '$postjson[main_id]'
    AND sd.sign_status IN ('0') 
    AND sd.detail_status = '1'
    ORDER BY sd.detail_id DESC";

    $query_detail = mysqli_query($mysqli, $objsql_detail);

    $i = 0;
    while($row_detail = mysqli_fetch_array($query_detail)){ 

        // วันที่และเวลาเสนอ
        $date_new = explode("-",$row_detail['DATEONLY']);
        $upload_date = $date_new[2].'/'.$date_new[1].'/'.($date_new[0]+543).' เวลา '.$row_detail['TIMEONLY'].' น.';

        //ผู้ส่ง หรือผู้เสนอหนังสือ
        $objsql_sender = mysqli_query($mysqli,"SELECT 
            concat(u.user_fname,' ', u.user_lname) as sender_user 
            FROM edoc_user u 
            LEFT JOIN sign_move sm ON u.user_id = sm.user_id
            WHERE sm.detail_id = '$row_detail[detail_id]' 
            AND sm.main_id = '$postjson[main_id]' 
            and sm.tposition_id = '$row_detail[tposition_id]'");
            
        $row_sender = mysqli_fetch_array($objsql_sender);
        
        $i++;

        $arr_name_file = explode("_edoc_", $row_detail['file_name']);

        $shot_file_name = $arr_name_file[1];

        $data_detail[] = array(
            'No'               => $i,
            'edoc_id'          => $row['edoc_id'],
            'main_id'          => $row['main_id'],
            'main_status'      => $row['main_status'],
            'main_type'        => $row['main_type'],
            'detail_id'        => $row_detail['detail_id'],
            'tposition_id'     => $row_detail['tposition_id'],
            'file_name'        => $row_detail['file_name'],
            'file_path'        => $row_detail['file_path'],
            'shot_file_name'   => $shot_file_name,
            'depart_id'        => $row['depart_id'],
            'read_status'      => $row_detail['read_status'],
            'sign_status'      => $row_detail['sign_status'],
            'detail_status'    => $row_detail['detail_status'],
            'upload_date'      => $upload_date,
            'sender_user'      => $row_sender['sender_user'],
            'tposition_name'   => $row_detail['tposition_name'],
            'position_name'    => $row_detail['position_name']
        );
    }
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data, 'result_detail'=>$data_detail));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
}
//รายละเอียดหนังสือที่จะลงนามและ เกษียนแล้ว
else if($postjson['aksi'] == "load_single_data_signed"){
    
    $data_detail = array();

    $query = mysqli_query($mysqli,"SELECT 
            sm.edoc_id, 
            sm.main_id, 
            sm.main_status,
            sm.main_type,
            sm.doc_type,
            ed.headline, 
            ed.receiver,
            ed.doc_date,
            ed.doc_no,
            ed.sender_depart,
            ed.depart_id as sender_depart_id,
            sm.depart_id,
            d.depart_name as deptname_edoc,
            md.depart_name as deptname_parent,
            mds.depart_name as deptname_sub,
            mds.depart_parent,
            et.edoc_type_name,
            sc.secrets_name,
            rp.rapid_name
        FROM sign_main sm
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id 
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        LEFT JOIN department d ON ed.depart_id = d.depart_id
        LEFT JOIN department mds ON sm.depart_id = mds.depart_id
        LEFT JOIN department md ON mds.depart_parent = md.depart_id
        WHERE sm.main_id = '$postjson[main_id]' 
        ");

    $row = mysqli_fetch_array($query);

    $arr_doc_date = explode("-",$row['doc_date']);
    $doc_date = $arr_doc_date[2].'/'.$arr_doc_date[1].'/'.($arr_doc_date[0]+543);

    if($row['depart_parent'] == 0) {
        $depart_submit = $row['deptname_sub'];
    } else {
        $depart_submit = $row['deptname_parent'].'/ '.$row['deptname_sub'];
    }

    $data = array(
        'doc_type'         => $row['doc_type'],
        'main_type'        => $row['main_type'],
        'edoc_id'          => $row['edoc_id'],
        'Secrets'          => $row['secrets_name'],
        'Rapid'            => $row['rapid_name'],
        'main_id'          => $row['main_id'],
        'headline'         => $row['headline'],
        'receiver'         => $row['receiver'],
        'edoc_type'        => $row['edoc_type_name'],
        'doc_no'           => $row['doc_no'],
        'doc_date'         => $doc_date,
        'agenc_dept'       => $row['deptname_edoc'],
        'depart_id'        => $row['depart_id'],
        'sender_depart'    => $row['sender_depart'],
        'sender_depart_id' => $row['sender_depart_id'],
        'depart_submit'    => $depart_submit
    );
            
    // รายการไฟล์หนังสือที่ต้องเกษียน หรือลงนามแล้ว
    
    $objsql_detail = "SELECT 
        sd.detail_id,
        sd.tposition_id,
        sd.upload_date,
        DATE_FORMAT(sd.upload_date,'%Y-%m-%d') DATEONLY, 
        DATE_FORMAT(sd.upload_date,'%H:%i:%s') TIMEONLY,
        sd.file_path,
        sd.file_name,
        sd.read_status,
        sd.sign_status,
        sd.detail_status,
        t.tposition_name,
        p.position_name
    FROM sign_detail sd 
    LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE t.e_passport = '$postjson[uid_passport]' 
    AND sd.main_id = '$postjson[main_id]'
    AND sd.sign_status IN ('2','3','4') 
    AND sd.detail_status = '1'
    ORDER BY sd.detail_id DESC";
      
    $query_detail = mysqli_query($mysqli, $objsql_detail);

    $i = 0;
    
    while($row_detail = mysqli_fetch_array($query_detail)){ $i++;

        $arr_name_file = explode("_edoc_", $row_detail['file_name']);

        $shot_file_name = $arr_name_file[1];

        // วันที่และเวลาเสนอ
        $date_new = explode("-",$row_detail['DATEONLY']);
        $upload_date = $date_new[2].'/'.$date_new[1].'/'.($date_new[0]+543).' เวลา '.$row_detail['TIMEONLY'].' น.';

        $data_detail[] = array(
            'No'               => $i,
            'edoc_id'          => $row['edoc_id'],
            'main_id'          => $row['main_id'],
            'main_status'      => $row['main_status'],
            'main_type'        => $row['main_type'],
            'detail_id'        => $row_detail['detail_id'],
            'tposition_id'     => $row_detail['tposition_id'],
            'file_name'        => $row_detail['file_name'],
            'file_path'        => $row_detail['file_path'],
            'shot_file_name'   => $shot_file_name,
            'depart_id'        => $row['depart_id'],
            'read_status'      => $row_detail['read_status'],
            'sign_status'      => $row_detail['sign_status'],
            'detail_status'    => $row_detail['detail_status'],
            'upload_date'      => $upload_date,
            'tposition_name'   => $row_detail['tposition_name'],
            'position_name'    => $row_detail['position_name']
        );
    }
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data, 'result_detail'=>$data_detail));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
   

}


else if($postjson['aksi'] == "load_signed_data"){
    $data = array();
    //$query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $postjson[start], $postjson[limit]");
    
    $query = mysqli_query($mysqli,"SELECT DISTINCT sd.main_id, 
            sm.doc_type,
            sm.edoc_id, 
            sm.depart_id,
            sm.doc_receive_no,
            t.tposition_id, 
            t.tposition_name,
            ed.headline, 
            ed.doc_date,
            ed.doc_no,
            ed.sender_depart,
            dd.depart_name AS edoc_depart_name,
            sd.detail_id,
            sd.upload_date,
            DATE_FORMAT(sd.upload_date, '%Y-%m-%d') DATEONLY, 
            DATE_FORMAT(sd.upload_date,'%H:%i:%s') TIMEONLY,
            sd.file_path,
            sd.file_name,
            p.position_name,
            d.depart_name,
            et.edoc_type_name,
            sd.read_status,
            sc.secrets_id,
            sc.secrets_name,
            rp.rapid_id,
            rp.rapid_name
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id 
        LEFT JOIN department d ON p.depart_id = d.depart_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN department dd ON ed.depart_id = dd.depart_id
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status IN ('2','3','4')
        AND sd.detail_status = '1'
        GROUP BY sd.main_id
        ORDER BY sd.detail_id DESC 
        LIMIT $postjson[start], $postjson[limit]");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;

        //$first_text_headline = substr($row['headline'], 0, 1);

        // $row_receive = mysqli_fetch_array( mysqli_query($mysqli, "SELECT receive_no 
        //     FROM edoc_receive 
        //     WHERE depart_id = '$row[depart_id]' AND edoc_id = '$row[edoc_id]'")
        // );

        // วันทีหนังสือ
        if($row['doc_no'] != ''){
            $doc_no = $row['doc_no'];
            $arr_doc_date = explode("-",$row['doc_date']);
            $doc_date = $arr_doc_date[2].'/'.$arr_doc_date[1].'/'.($arr_doc_date[0]+543);
        }else{
            $doc_no = 'รอดำเนินการ';
            $doc_date = 'รอดำเนินการ';
        }

        // วันที่และเวลาเสนอ
        $date_new = explode("-",$row['DATEONLY']);
        $upload_date = $date_new[2].'/'.$date_new[1].'/'.($date_new[0]+543).' เวลา '.$row['TIMEONLY'].' น.';

        $receiveNo = $row['doc_receive_no'];

        //check more file not sign.

        $objsql_file_detail = mysqli_query($mysqli, "SELECT count(detail_id) as row_more_file 
            FROM sign_detail 
            WHERE main_id = '$row[main_id]' 
            AND sign_status = '0' 
            AND detail_status = '1'
            AND tposition_id = '$row[tposition_id]'
            ");

        $row_file = mysqli_fetch_array($objsql_file_detail);

        if($row_file['row_more_file'] == 0){

            $data[] = array(
                'No'                    => $No,
                'doc_type'              => $row['doc_type'],
                'SecretsId'             => $row['secrets_id'],
                'Secrets'               => $row['secrets_name'],
                'RapidId'               => $row['rapid_id'],
                'Rapid'                 => $row['rapid_name'],
                'detail_id'             => $row['detail_id'],
                'upload_date'           => $upload_date,
                'file_path'             => $row['file_path'],
                'file_name'             => $row['file_name'],
                'headline'              => $row['headline'],
                'depart_id'             => $row['depart_id'],
                'edoc_id'               => $row['edoc_id'],
                'edoc_type_name'        => $row['edoc_type_name'],
                'sender_depart'         => $row['sender_depart'],
                'edoc_depart_name'      => $row['edoc_depart_name'],
                'doc_date'              => $doc_date,
                'main_id'               => $row['main_id'],
                'tposition_id'          => $row['tposition_id'],
                'depart_name'           => $row['depart_name'],
                'tposition_name'        => $row['tposition_name'],
                'position_name'         => $row['position_name'],
                'read_status'           => $row['read_status'],
                'first_text_headline'   => $first_text_headline,
                'receiveNo'             => $receiveNo
            );
        }
    }
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
}

//ค้นหาหนังสือลงนาม และเกษียนแล้ว
else if($postjson['aksi'] == "load_signed_data_search"){
    $data = array();
    //$query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $postjson[start], $postjson[limit]");
    
    $query = mysqli_query($mysqli,"SELECT DISTINCT sd.main_id, 
            sm.doc_type,
            sm.edoc_id, 
            sm.depart_id,
            sm.doc_receive_no,
            t.tposition_id, 
            t.tposition_name,
            ed.headline, 
            ed.doc_date,
            ed.doc_no,
            ed.sender_depart,
            dd.depart_name AS edoc_depart_name,
            sd.detail_id,
            sd.upload_date,
            sd.file_path,
            sd.file_name,
            p.position_name,
            d.depart_name,
            et.edoc_type_name,
            sd.read_status,
            sc.secrets_id,
            sc.secrets_name,
            rp.rapid_id,
            rp.rapid_name
        FROM sign_detail sd 
        LEFT JOIN takeposition t ON sd.tposition_id = t.tposition_id
        LEFT JOIN position p ON t.position_id = p.position_id 
        LEFT JOIN department d ON p.depart_id = d.depart_id
        LEFT JOIN sign_main sm ON sd.main_id = sm.main_id 
        LEFT JOIN edoc ed ON sm.edoc_id = ed.edoc_id 
        LEFT JOIN department dd ON ed.depart_id = dd.depart_id
        LEFT JOIN edoc_type et ON ed.edoc_type_id = et.edoc_type_id
        LEFT JOIN secrets sc ON ed.secrets = sc.secrets_id
        LEFT JOIN rapid rp ON ed.rapid = rp.rapid_id
        WHERE t.e_passport = '$postjson[e_passport]' 
        AND sd.sign_status = '1'
        AND sd.detail_status = '1' 
        AND (ed.headline like '%$postjson[text_search]%' 
            OR sm.doc_receive_no like '%$postjson[text_search]%' 
            OR dd.depart_name like '%$postjson[text_search]%'
            OR ed.sender_depart like '%$postjson[text_search]%'
            OR ed.doc_date like '%$postjson[text_search]%'
            OR ed.doc_no like '%$postjson[text_search]%'
        )
        GROUP BY sd.main_id
        ORDER BY sd.detail_id DESC 
        LIMIT $postjson[start], $postjson[limit]");

    $No = 0;

    while($row = mysqli_fetch_array($query)){ $No++;
        if($row['doc_no'] != ''){
            $doc_no = $row['doc_no'];
            $arr_doc_date = explode("-",$row['doc_date']);
            $doc_date = $arr_doc_date[2].'/'.$arr_doc_date[1].'/'.($arr_doc_date[0]+543);
        }else{
            $doc_no = 'รอดำเนินการ';
            $doc_date = 'รอดำเนินการ';
        }
       

        $receiveNo = $row['doc_receive_no'];
        //check more file not sign.
        $objsql_file_detail = mysqli_query($mysqli, "SELECT count(detail_id) as row_more_file 
            FROM sign_detail 
            WHERE main_id = '$row[main_id]' 
            AND sign_status = '0' 
            AND detail_status = '1'
            AND tposition_id = '$row[tposition_id]'
            ");

        $row_file = mysqli_fetch_array($objsql_file_detail);

        if($row_file['row_more_file'] == 0){

            $data[] = array(
                'No'                    => $No,
                'doc_type'              => $row['doc_type'],
                'SecretsId'             => $row['secrets_id'],
                'Secrets'               => $row['secrets_name'],
                'RapidId'               => $row['rapid_id'],
                'Rapid'                 => $row['rapid_name'],
                'detail_id'             => $row['detail_id'],
                'upload_date'           => $row['upload_date'],
                'file_path'             => $row['file_path'],
                'file_name'             => $row['file_name'],
                'headline'              => $row['headline'],
                'depart_id'             => $row['depart_id'],
                'edoc_id'               => $row['edoc_id'],
                'edoc_type_name'        => $row['edoc_type_name'],
                'sender_depart'         => $row['sender_depart'],
                'edoc_depart_name'      => $row['edoc_depart_name'],
                'doc_date'              => $doc_date,
                'main_id'               => $row['main_id'],
                'tposition_id'          => $row['tposition_id'],
                'depart_name'           => $row['depart_name'],
                'tposition_name'        => $row['tposition_name'],
                'position_name'         => $row['position_name'],
                'read_status'           => $row['read_status'],
                'first_text_headline'   => $first_text_headline,
                'receiveNo'             => $receiveNo,
                'docNo'                 => $doc_no
            );
        }
    }
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
}

else if($postjson['aksi'] == "del_users"){
    $query = mysqli_query($mysqli,"DELETE FROM tb_users WHERE id_user = $postjson[id]");
    
    if($query){
        $result = json_encode(array('success'=>true));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
}

else if($postjson['aksi'] == "proses_crud"){

    $cekpass = mysqli_fetch_array(mysqli_query($mysqli,"SELECT password FROM tb_users WHERE id_user = '$postjson[id]'"));

    if($postjson['password'] == ""){
        $password = $cekpass['password'];
    }else{
        $password = md5($postjson['password']);
    }

    if($postjson['action'] == "Create"){

        $checkmail = mysqli_fetch_array(mysqli_query($mysqli,"SELECT email_address FROM tb_users WHERE email_address = '$postjson[email_address]'"));

        if($checkmail['email_address'] == $postjson['email_address']){
            $result = json_encode(array('success'=>false, 'msg'=>'Email is already'));
        }else{
            
            $insert = mysqli_query($mysqli, "INSERT INTO tb_users SET 
                your_name       = '$postjson[your_name]',
                gender          = '$postjson[gender]',
                date_birthday   = '$postjson[date_birth]',
                email_address   = '$postjson[email_address]',
                password        = '$password',
                created_at      = '$today'
            ");
        
            if($insert){
                $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
            }else{
                $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
            }
        }
    }else{
        $update = mysqli_query($mysqli, "UPDATE tb_users SET 
            your_name       = '$postjson[your_name]',
            gender          = '$postjson[gender]',
            date_birthday   = '$postjson[date_birth]',
            password = '$password' WHERE id_user = '$postjson[id]'
            ");

        if($update){
            $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
        }else{
            $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
        }
    }

    echo $result;

}
else if($postjson['aksi'] == "proses_senddoc"){
    $insert_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
        main_id         = '$postjson[main_id]',
        tposition_id    = '$postjson[tposition_id]',
        activity        = 'Signatured',
        user_id         = '',
        time            = '$today'
        ");

    $insert_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
        main_id         = '$postjson[main_id]',
        tposition_id    = '$postjson[tposition_id]',
        file_name       = '$postjson[file_name]',
        file_path       = '$postjson[file_path]',
        sign_status     = '2',
        upload_date     = '$today',
        sign_date       = '$today'
        ");

    $insert_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
        sign_status     = '1' 
        WHERE detail_id = '$postjson[detail_id]'
        ");

    //check การเซ็นไฟล์หนังสือทุกฉบับของเรื่องนี้ 
    //แล้วะUpdate sing main => main_status = 2 หากเซ็นหมดแล้ว

        
    if($insert_detail){

        $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
    }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
    }

    echo $result;
}
else if($postjson['aksi'] == "proses_opendoc"){
    
    $update_detail = mysqli_query($mysqli, "UPDATE sign_detail SET read_status = '1' 
        WHERE detail_id = '$postjson[detail_id]'
    "); 
               
    if($update_detail){
        $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
    }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
    }

    echo $result;
}
else if($postjson['aksi'] == "proses_opendoc_signed"){
    
    $update_detail = true;
               
    if($update_detail){
        $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
    }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
    }

    echo $result;
}
// guide
else if($postjson['aksi'] == "load_guide_data"){
    $data = array();
    //$query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $postjson[start], $postjson[limit]");
    
    $query = mysqli_query($mysqli,"SELECT type_contract, url_image, detail  
        FROM sign_contract WHERE type_contract = '1'");

    $No = 0;

    while($row = mysqli_fetch_array($query)){
        $data[] = array(
            'urlImage'    => $row['url_image'],
            'guideDetail' => $row['detail']
        );
        
    }
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}else if($postjson['aksi'] == "proses_sendback_cancel"){ //กรณีส่งกลับเนื่องจากมีปัญหาการเปิด หรือส่ง
    
    $insert_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
    main_id         = '$postjson[main_id]',
    tposition_id    = '$postjson[tposition_id]',
    file_name       = '$postjson[file_name]',
    file_path       = '$postjson[file_path]',
    sign_status     = '5',
    remark          = '$postjson[text_sendback]',
    upload_date     = '$today',
    sign_date       = '$today'
    ");

    $detail_id = mysqli_insert_id($mysqli);


    $insert_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
    main_id         = '$postjson[main_id]',
    tposition_id    = '$postjson[tposition_id]',
    detail_id       = '$detail_id',
    activity        = 'Remark',
    user_id         = '',
    time            = '$today'
    ");

    // update สถานะรายการก่อนหน้า
    $update_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
    sign_status     = '1'
    WHERE detail_id = '$postjson[detail_id]'
    ");

    $update_main = mysqli_query($mysqli, "UPDATE sign_main SET 
    main_status     = '1' 
    WHERE main_id = '$postjson[main_id]'
    ");
               
    if($update_main){
        $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
    }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
    }

    echo $result;
}else if($postjson['aksi'] == "proses_sendback_notsign"){ //กรณีไม่มีการเกษียณ หรือลงนาม ต้องการตรวจสอบ
    
    // ตรวจสอบการยกเลิก
    $sql_detail = "SELECT detail_status FROM sign_detail 
    WHERE detail_id = '$postjson[detail_id]'";
    $res_detail = mysqli_query($mysqli, $sql_detail);
    $row_detail = mysqli_fetch_array($res_detail);

    $detail_status = $row_detail['detail_status'];

    if($detail_status == '1'){

        $no = 1;

        $year = date('Y');

        $location = '../../document/';
    
        // insert detail new signatured
        
        $ins_detail = mysqli_query($mysqli, "INSERT INTO sign_detail SET 
        main_id         = '$postjson[main_id]',
        tposition_id    = '$postjson[tposition_id]',
        file_name       = '$postjson[file_name]',
        file_path       = '$postjson[file_path]',
        sign_status     = '2',
        upload_date     = '$today',
        sign_date       = '$today',
        remark          = '$postjson[text_sendback]'
        ");
        
        $ins_detail_id = mysqli_insert_id($mysqli); // id detail new

        //Add row new sign_move
        $ins_move = mysqli_query($mysqli, "INSERT INTO sign_move SET 
        main_id         = '$postjson[main_id]',
        detail_id       = '$ins_detail_id',
        tposition_id    = '$postjson[tposition_id]',
        activity        = 'Signatured',
        time            = '$today'
        ");

        // update sign_status row before signature form
        $upd_detail = mysqli_query($mysqli, "UPDATE sign_detail SET 
            sign_status     = '1' 
            WHERE detail_id = '$postjson[detail_id]'
            ");

        // check num row sing of doc
        $num_row_not_sign = mysqli_num_rows(mysqli_query($mysqli, "SELECT sign_status 
            FROM sign_detail 
            WHERE main_id='$postjson[main_id]' 
            AND tposition_id = '$postjson[tposition_id]' 
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
            WHERE main_id = '$postjson[main_id]'
        ");

        // update sign sequence
        $upds_sequence =  mysqli_query($mysqli, "UPDATE sign_sequence SET sequ_signed_status  = '$sequ_signed_status' 
            WHERE main_id = '$postjson[main_id]' 
            AND tposition_id = '$postjson[tposition_id]'
        ");

        //check sequence
        $row_sequence = mysqli_fetch_array(
            mysqli_query($mysqli, "SELECT tposition_id, count(sequ_no) AS num_sequ_no
            FROM sign_sequence 
            WHERE main_id = '$postjson[main_id]' 
            AND sequ_signed_status = 'W' 
            AND sequ_status = '1'
            ORDER BY sequ_no ASC 
            LIMIT 0,1
        "));

        //check detail more file of tposition
        $row_file_more = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(tposition_id) AS num_more_file
            FROM sign_detail
            WHERE main_id = '$postjson[main_id]' 
            AND tposition_id = '$postjson[tposition_id]' 
            AND sign_status = '0' 
            AND detail_status = '1'
        "));

        $more_file = $row_file_more['num_more_file'];

        // ** กรณีส่งต่อให้อัตโนมัติ **//
        if($postjson['main_type'] == '2' && $row_sequence['num_sequ_no'] >= 1 && $more_file == 0) {

            $path_approv_not_signed = 'approv_not_signed/D00'.$postjson['depart_id'].'/'.$year.'/';
            $path = $location.$path_approv_not_signed;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $test_to_auto_transfer = 'test_to_auto_transfer';

            $to_tposition_id = $row_sequence['tposition_id'];

            $obj_detail_at = "SELECT detail_id, file_name, file_path FROM sign_detail 
                WHERE main_id = '$postjson[main_id]' 
                AND tposition_id = '$postjson[tposition_id]' 
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
                        main_id         = '$postjson[main_id]',
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
                    main_id         = '$postjson[main_id]',
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
                        WHERE main_id = '$postjson[main_id]'
                    ");

                    // update sequence status = 1
                    $upd_sequence = mysqli_query($mysqli, "UPDATE sign_sequence SET sequ_transfer  = '1' 
                    WHERE main_id = '$postjson[main_id]' 
                    AND tposition_id = '$postjson[tposition_id]' 
                    AND sequ_status = '1' 
                    AND sequ_signed_status = 'C'");
                    
                }else {
                    echo json_encode(array(
                        'msg' => 'error upload !',
                        'status' => false,
                    ));
                }

            } //end while

        }//end if check auto send

        if($upds_sequence){
            $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
        }else{
            $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
        }
    }else {
        $result = json_encode(array('success'=>false, 'msg'=>'หนังสือถูกยกเลิก'));
    }

    echo $result;
}


