<?php
require('../db.php');
require('../fn.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding, X-Auth-Token, content-type');
header("Content-Type: application/json");

$univId = $_GET['univ'];
$userType = $_GET['usertype'];
$departId = $_GET['departid'];

if($userType == 'SA'){
    $condition = "AND univ_id = '$univId' AND depart_parent = '0'";
    $data[] = array(
        'Id'=>'0',
        'Name'=>'ไม่มี (หน่วยงานหลัก)',
        'Code'=>''
    );
}else if($userType == 'A'){
    $condition = "AND univ_id = '$univId' AND depart_id = '$departId'";
}else{
    //
}
/*
$objsql = "SELECT depart_id, depart_name, depart_code FROM department
    WHERE depart_cancel = '0'
    AND depart_parent = '0'
    AND univ_id='$univId'
    $condition
    ORDER BY depart_name ASC";
*/

$objsql = "SELECT depart_id, depart_name, depart_code
    FROM department
    WHERE depart_cancel = '0'
    $condition
    ORDER BY depart_name ASC";

$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs) ){
    if($userType == 'SA'){
        $data[] = array(
            'Id'=>$objdata['depart_id'],
            'Name'=>$objdata['depart_name'],
            'Code'=>$objdata['depart_code']
        );
    }else{
    
        $data[] = array(
            'Id'=>$objdata['depart_id'],
            'Name'=>getDepartmentClass($objdata['depart_id']),
            'Code'=>$objdata['depart_code']
        );
    }
    
    $objsql2 = "SELECT depart_id, depart_name, depart_code
        FROM department
        WHERE depart_parent = '$objdata[depart_id]'
        
        ORDER BY depart_name ASC";
    $objrs2 = mysqli_query($con, $objsql2);
    while($objdata2 = mysqli_fetch_array($objrs2)){
        $data[] = array(
            'Id'=>$objdata2['depart_id'],
            'Name'=>'==='.$objdata2['depart_name'],
            'Code'=>$objdata2['depart_code']
        );
        
        $objsql3 = "SELECT depart_id, depart_name, depart_code
            FROM department
            WHERE depart_parent = '$objdata2[depart_id]'
            
            ORDER BY depart_name ASC";
        $objrs3 = mysqli_query($con, $objsql3);
        while($objdata3 = mysqli_fetch_array($objrs3)){
            $data[] = array(
                'Id'=>$objdata3['depart_id'],
                'Name'=>'======='.$objdata3['depart_name'],
                'Code'=>$objdata3['depart_code']
            );
            
            $objsql4 = "SELECT depart_id, depart_name, depart_code
                FROM department
                WHERE depart_parent = '$objdata3[depart_id]'
                
                ORDER BY depart_name ASC";
            $objrs4 = mysqli_query($con, $objsql4);
            while($objdata4 = mysqli_fetch_array($objrs4)){
                $data[] = array(
                    'Id'=>$objdata4['depart_id'],
                    'Name'=>'==========='.$objdata4['depart_name'],
                    'Code'=>$objdata4['depart_code']
                );
            }
        }
        
    }
}

//$data[] = array('resp'=>'');

print json_encode(array("data"=>$data));
?>