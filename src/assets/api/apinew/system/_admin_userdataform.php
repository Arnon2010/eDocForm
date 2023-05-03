<?php
require('../db.php');
$userId = $_GET['userid'];
$objsql = "SELECT * FROM edoc_user WHERE user_id = $userId";
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'userFname'=>$objdata['user_fname'],
        'userLname'=>$objdata['user_lname'],
        'userEpass'=>$objdata['user_name'],
        'secretsId'=>$objdata['secrets_id'],
        'priveditId'=>$objdata['privedit_id'],
        'privbrowId'=>$objdata['privbrow_id'],
        'userType'=>$objdata['user_type'],
        'departId'=>$objdata['depart_id'],
        'status'=>$objdata['user_status']
        
    );
}
//$data[] = array('sql'=>$objsql);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>