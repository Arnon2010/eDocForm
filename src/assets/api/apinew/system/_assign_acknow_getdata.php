<?php
require('../db.php');
   
$objsql = "SELECT d.depart_id, d.depart_name, a.id,
        a.e_passport,
        a.Name,
        a.status
        FROM department_acknow a
        LEFT JOIN department d ON a.depart_id = d.depart_id
       
        ORDER BY a.Name ASC";
        
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'acknowId'=>$objdata['id'],
        'deptId'=>$objdata['depart_id'],
        'deptName'=>$objdata['depart_name'],
        'ePassport'=>$objdata['e_passport'],
        'managerName'=>$objdata['Name'],
        'assignStatus'=>$objdata['status']
    );
}

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);
@print json_encode(array("data"=>$data));
?>