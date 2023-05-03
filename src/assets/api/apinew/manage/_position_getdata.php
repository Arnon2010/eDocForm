<?php
require('../db.php');
$facId = $_GET['facid'];
$objsql = "SELECT * FROM tbposition WHERE status = '1' AND fac_id = '$facId'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['position_id'],
        'Name'=>$objdata['position_name'],
        'FacultyId'=>$objdata['fac_id'],
        'Status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
