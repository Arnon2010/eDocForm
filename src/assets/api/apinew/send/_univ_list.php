<?php
require('../db.php');

@$edocType = $_GET['edoctype'];
if($edocType == 'e'){
    $objsql = "SELECT univ_id, univ_name, univ_code FROM univ WHERE univ_status = '1' AND univ_code = 'e'";
}else{
    $objsql = "SELECT univ_id, univ_name, univ_code FROM univ WHERE univ_status = '1'";
}

    
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['univ_id'],
        'Name'=>$objdata['univ_name'],
        'Code'=>$objdata['univ_code']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>