<?php
require('../db.php');
date_default_timezone_set("Asia/Bangkok");

$edocId = $_GET['edoc_id'];
$searchType = $_GET['search_type'];
$departId = $_GET['depart_id'];
$mainId = $_GET['main_id'];
$userId = $_GET['user_id'];

@$timeUpdate = date('Y-m-d H:i:s');

$docYear = date('Y');

$status = false;

$objsqlInsMove = "INSERT INTO  sign_move SET 
    main_id = '$mainId',
    activity = 'DoneOriginal',
    user_id = '$userId',
    time = '$timeUpdate'
";
if(mysqli_query($con, $objsqlInsMove)){

    $objsqlUpdMain = "UPDATE sign_main SET main_status = '4' 
        WHERE main_id = '$mainId'"; 
    if(mysqli_query($con, $objsqlUpdMain))
        $status = true;
}
    
$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>