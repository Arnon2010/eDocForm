<?php
require('../db.php');

//if($_GET['departid']){
    $dept = $_GET['departid'];
    $cond = " u.depart_id = '$dept'";
//}

$objsql = "SELECT * FROM edoc_user u
    LEFT JOIN department d ON u.depart_id = d.depart_id
    LEFT JOIN tbprivbrow pb ON u.privbrow_id = pb.privbrow_id
    LEFT JOIN tbprivedit pe ON u.privedit_id = pe.privedit_id
    LEFT JOIN secrets s ON u.secrets_id = s.secrets_id
    LEFT JOIN tbusertype ut ON u.user_type = ut.usertype_id
    WHERE $cond";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;


while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $edocOfUser = 0;
    // check edoc table
    $objsql = "SELECT user_id FROM edoc WHERE user_id = '$objdata[user_id]'";
    $objres = mysqli_query($con, $objsql);
    
    if($row = mysqli_num_rows($objres) >= 1 ){
        $edocOfUser = 1;
    }else{
        // check edoc_sent table
        $objsql = "SELECT user_id FROM edoc_sent WHERE user_id = '$objdata[user_id]'";
        $objres = mysqli_query($con, $objsql);
        if($row = mysqli_num_rows($objres) >= 1 ){
            $edocOfUser = 1;
        }else{
            // check edoc_receive table
            $objsql = "SELECT user_id FROM edoc_receive WHERE user_id = '$objdata[user_id]'";
            $objres = mysqli_query($con, $objsql);
            if($row = mysqli_num_rows($objres) >= 1 ){
                $edocOfUser = 1;
            }
        }
    }
    

    $data[] = array(
        'No'=>$i,
        'userId'=>$objdata['user_id'],
        'userFname'=>$objdata['user_fname'],
        'userLname'=>$objdata['user_lname'],
        'userEpass'=>$objdata['user_name'],
        'secretsName'=>$objdata['secrets_name'],
        'priveditName'=>$objdata['privedit_name'],
        'privbrowName'=>$objdata['privbrow_name'],
        'userType'=>$objdata['usertype_name'],
        'departId'=>$objdata['depart_id'],
        'departName'=>$objdata['depart_name'],
        'status'=>$objdata['user_status'],
        'univNumberAllow'=>$objdata['univ_number_allow'],
        'univNoStatus'=>$objdata['univ_no_status'],
        'edocOfUser'=>$edocOfUser
    );
}
//$data[] = array('sql'=>$objsql);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>