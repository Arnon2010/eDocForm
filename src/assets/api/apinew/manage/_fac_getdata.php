<?php
require('../db.php');
$userType = $_GET['usertype'];
if($userType == 'U'){
    $facId = $_GET['facid'];
    $cond = " AND fac_id = '$facId'";
}
$objsql = "SELECT * FROM tbfaculty WHERE status = '1' $cond";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['fac_id'],
        'Code'=>$objdata['fac_code'],
        'Name'=>$objdata['fac_name'],
        'Parents'=>$objdata['parent'],
        'Status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>