<?php
require('../db.php');

$objsql = "SELECT * FROM system_update 
    WHERE status = '1'";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;

while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'Id'=>$objdata['id'],
        'Topic'=>$objdata['topic'],
        'Detail'=>$objdata['detail'],
        'dateModified'=>$objdata['date_modified'],
        'Status'=>$objdata['status']  
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>