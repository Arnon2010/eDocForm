<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("content-type:text/javascript;charset=utf-8");

header("Content-Type: application/json; charset=utf-8", true, 200);

include "config.php";

$positjson = json_decode(file_get_contents('php://input'), true);

@$today = date('Y-m-d H:i:s');



if($positjson['aksi'] == "proses_register"){

    @$checkmail = mysqli_fetch_array(mysqli_query($mysqli,"SELECT email_address FROM tb_users WHERE email_address = '$positjson[email_address]'"));

    if($checkmail['email_address'] == $positjson['email_address']){
        $result = json_encode(array('success'=>false, 'msg'=>'Email is already'));
    }else{
        $password = md5($positjson['password']);
        $insert = mysqli_query($mysqli, "INSERT INTO tb_users SET 
            your_name = '$positjson[your_name]',
            gender = '$positjson[gender]',
            date_birthday = '$positjson[date_birth]',
            email_address = '$positjson[email_address]',
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

}else if($positjson['aksi'] == "proses_login"){
    $password = md5($positjson['password']);
    @$logindata = mysqli_fetch_array(mysqli_query($mysqli,"SELECT * FROM tb_users WHERE email_address = '$positjson[email_address]'
        AND   password = '$password'"));
    $data = array(
        'id_user'         => $logindata['id_user'],
        'your_name'       => $logindata['your_name'],
        'gender'          => $logindata['gender'],
        'date_birthday'   => $logindata['date_birthday'],
        'email_address'   => $logindata['email_address']
    );
    
    if($logindata){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}else if($positjson['aksi'] == "load_users"){
    $data = array();
    $query = mysqli_query($mysqli,"SELECT * FROM tb_users ORDER BY id_user DESC LIMIT $positjson[start], $positjson[limit]");
    
    while($row = mysqli_fetch_array($query)){
        $data[] = array(
            'id_user'         => $row['id_user'],
            'your_name'       => $row['your_name'],
            'gender'          => $row['gender'],
            'date_birthday'   => $row['date_birthday'],
            'email_address'   => $row['email_address']
        );
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}else if($positjson['aksi'] == "del_users"){
    $query = mysqli_query($mysqli,"DELETE FROM tb_users WHERE id_user = $positjson[id]");
    
    if($query){
        $result = json_encode(array('success'=>true));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;
}

else if($positjson['aksi'] == "proses_crud"){

    $cekpass = mysqli_fetch_array(mysqli_query($mysqli,"SELECT password FROM tb_users WHERE id_user = '$positjson[id]'"));

    if($positjson['password'] == ""){
        $password = $cekpass['password'];
    }else{
        $password = md5($positjson['password']);
    }

    if($positjson['action'] == "Create"){

        $checkmail = mysqli_fetch_array(mysqli_query($mysqli,"SELECT email_address FROM tb_users WHERE email_address = '$positjson[email_address]'"));

        if($checkmail['email_address'] == $positjson['email_address']){
            $result = json_encode(array('success'=>false, 'msg'=>'Email is already'));
        }else{
            
            $insert = mysqli_query($mysqli, "INSERT INTO tb_users SET 
                your_name       = '$positjson[your_name]',
                gender          = '$positjson[gender]',
                date_birthday   = '$positjson[date_birth]',
                email_address   = '$positjson[email_address]',
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
            your_name       = '$positjson[your_name]',
            gender          = '$positjson[gender]',
            date_birthday   = '$positjson[date_birth]',
            password = '$password' WHERE id_user = '$positjson[id]'
            ");

        if($update){
            $result = json_encode(array('success'=>true, 'msg'=>'Successfuly'));
        }else{
            $result = json_encode(array('success'=>false, 'msg'=>'Proses error'));
        }
    }

    echo $result;

}

else if($positjson['aksi'] == "load_single_data"){
    
    $query = mysqli_query($mysqli,"SELECT * FROM tb_users WHERE id_user = '$positjson[id]'");
    
    while($row = mysqli_fetch_array($query)){
        $data = array(
            'id_user'         => $row['id_user'],
            'your_name'       => $row['your_name'],
            'gender'          => $row['gender'],
            'date_birthday'   => $row['date_birthday'],
            'email_address'   => $row['email_address']
        );
    }
    
    
    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
    }else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;

}

