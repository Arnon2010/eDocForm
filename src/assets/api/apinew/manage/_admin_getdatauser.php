<?php
require('../db.php');

if($_GET['facid']){
    $facId = $_GET['facid'];
    $cond = " AND u.fac_id = '$facId'";
}
$objsql = "SELECT * FROM tbuser u
    LEFT JOIN tbfaculty f ON u.fac_id = f.fac_id 
    LEFT JOIN tbprivbrow pb ON u.privbrow_id = pb.privbrow_id
    LEFT JOIN tbprivedit pe ON u.privedit_id = pe.privedit_id
    LEFT JOIN tbsecrets s ON u.secrets_id = s.secrets_id
    LEFT JOIN tbusertype ut ON u.usertype_id = ut.usertype_id
    WHERE userStatus = '1' $cond";
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'userId'=>$objdata['userId'],
        'userFname'=>$objdata['userFname'],
        'userLname'=>$objdata['userLname'],
        'userEpass'=>$objdata['userEpass'],
        'secretsName'=>$objdata['secrets_name'],
        'priveditName'=>$objdata['privedit_name'],
        'privbrowName'=>$objdata['privbrow_name'],
        'userType'=>$objdata['usertype_name'],
        'facName'=>$objdata['fac_name']
        
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>