<?php
require('../db.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding, X-Auth-Token, content-type');
header("Content-Type: application/json");

$objsql = "SELECT * FROM edoc_type WHERE edoc_type_status = '1'";
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['edoc_type_id'],
        'Name'=>$objdata['edoc_type_name']
    );
}

print json_encode(array("data"=>$data));
?>