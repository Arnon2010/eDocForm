<?php
require('../db.php');

$userEpass = $_GET['user'];

$objsqlchk ="SELECT u.*, d.depart_id, d.depart_name, d.univ_id
    FROM edoc_user u
    LEFT JOIN department d ON u.depart_id = d.depart_id
    WHERE user_name = '$userEpass'";
$objrschk = mysqli_query($con, $objsqlchk);
$objdatachk = mysqli_num_rows($objrschk);



if($objdatachk >= 1){
    $status = 'true';
    while($objdata = mysqli_fetch_array($objrschk)){
        $user[] = array(
            'userFname'=>$objdata['user_fname'],
            'userLname'=>$objdata['user_lname'],
            'userEpass'=>$objdata['user_name'],
            'secretsId'=>$objdata['secrets_id'],
            'priveditId'=>$objdata['privedit_id'],
            'privbrowId'=>$objdata['privbrow_id'],
            'userType'=>$objdata['user_type'],
            'univId'=>$objdata['univ_id'],
            'departId'=>$objdata['depart_id'],
            'departName'=>$objdata['depart_name'],
            'status'=>$objdata['user_status']
            
        );
    }
    
}else if($objdatachk == 0){
    $status = 'false';
    $user[] = array(
            'userFname'=>'');
}

$data[] = array(
    'check'=>$status,
    'resp'=>$objsqlchk
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data, "user"=>$user));
?>

