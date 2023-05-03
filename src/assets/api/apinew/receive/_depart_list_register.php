<?php
require('../db.php');
$departId = $_GET[departid];
$userType = $_GET[usertype];
$univId = $_GET[univ];
$edocType = $_GET[edoctype];

$condition = '';

/*
if($edocType == '1' || $edocType == 'e')
    $condition = "AND univ_id = '4'";
else
    $condition = "AND univ_id != '4'";
*/

$objsql = "SELECT depart_id, depart_name, depart_code
    FROM department
    WHERE depart_cancel = '0'
    AND depart_parent = '0'
    $condition
    ORDER BY depart_name ASC";

$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs) ){

    $data[] = array(
        'Id'=>$objdata['depart_id'],
        'Name'=>$objdata['depart_name'],
        'deptName'=>$objdata['depart_name'],
        'Code'=>$objdata['depart_code']
    );
    
    $objsql2 = "SELECT depart_id, depart_name, depart_code
        FROM department
        WHERE depart_parent = '$objdata[depart_id]'
        $condition
        ORDER BY depart_name ASC";
    $objrs2 = mysqli_query($con, $objsql2);
    while($objdata2 = mysqli_fetch_array($objrs2)){
        $data[] = array(
            'Id'=>$objdata2['depart_id'],
            'Name'=>'===='.$objdata2['depart_name'],
            'deptName'=>$objdata2['depart_name'],
            'Code'=>$objdata2['depart_code']
        );
        
        $objsql3 = "SELECT depart_id, depart_name, depart_code
            FROM department
            WHERE depart_parent = '$objdata2[depart_id]'
            $condition
            ORDER BY depart_name ASC";
        $objrs3 = mysqli_query($con, $objsql3);
        while($objdata3 = mysqli_fetch_array($objrs3)){
            $data[] = array(
                'Id'=>$objdata3['depart_id'],
                'Name'=>'========'.$objdata3['depart_name'],
                'deptName'=>$objdata3['depart_name'],
                'Code'=>$objdata3['depart_code']
            );
            
            $objsql4 = "SELECT depart_id, depart_name, depart_code
                FROM department
                WHERE depart_parent = '$objdata3[depart_id]'
                $condition
                ORDER BY depart_name ASC";
            $objrs4 = mysqli_query($con, $objsql4);
            while($objdata4 = mysqli_fetch_array($objrs4)){
                $data[] = array(
                    'Id'=>$objdata4['depart_id'],
                    'Name'=>'==========='.$objdata4['depart_name'],
                    'deptName'=>$objdata4['depart_name'],
                    'Code'=>$objdata4['depart_code']
                );
            }
        }
        
    }
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>