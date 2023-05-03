<?php
require('../db.php');
@$userId = $_GET['userid'];

$objsql = "SELECT 
        d.version_new,
        d.univ_no_status,
        u.univ_number_allow
        FROM edoc_user u 
        LEFT JOIN department d ON u.depart_id = d.depart_id
        WHERE u.user_id = '$userId'
    ";
        
$objrs = mysqli_query($con, $objsql);
$i=0;
$objdata = mysqli_fetch_assoc($objrs);
$data[] = array(
    'newVersion'=>$objdata['version_new'],
    'univNoStatus'=>$objdata['univ_no_status'],
    'univNumberAllow'=>$objdata['univ_number_allow'],
    'res'=>$objsql
);

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);
@print json_encode(array("data"=>$data));
?>