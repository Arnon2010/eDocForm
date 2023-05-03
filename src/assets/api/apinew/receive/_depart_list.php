<?php
require('../db.php');

$univId = $_GET[univ];

$objsql = "SELECT depart_id, depart_name FROM department
    WHERE depart_cancel = '0'
    AND depart_parent = '0' 
    AND univ_id='$univId'
    ORDER BY depart_name ASC";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['depart_id'],
        'Name'=>$objdata['depart_name']
    );
    
    $objsql2 = "SELECT depart_id, depart_name
        FROM department
        WHERE depart_parent = '$objdata[depart_id]'
        ORDER BY depart_name ASC";
    $objrs2 = mysqli_query($con, $objsql2);
    while($objdata2 = mysqli_fetch_array($objrs2)){
        $data[] = array(
            'Id'=>$objdata2['depart_id'],
            'Name'=>'===='.$objdata2['depart_name']
        );
        
        $objsql3 = "SELECT depart_id, depart_name
            FROM department
            WHERE depart_parent = '$objdata2[depart_id]'
            ORDER BY depart_name ASC";
        $objrs3 = mysqli_query($con, $objsql3);
        while($objdata3 = mysqli_fetch_array($objrs3)){
            $data[] = array(
                'Id'=>$objdata3['depart_id'],
                'Name'=>'========'.$objdata3['depart_name']
            );
            
            $objsql4 = "SELECT depart_id, depart_name
                FROM department
                WHERE depart_parent = '$objdata3[depart_id]'
                ORDER BY depart_name ASC";
            $objrs4 = mysqli_query($con, $objsql4);
            while($objdata4 = mysqli_fetch_array($objrs4)){
                $data[] = array(
                    'Id'=>$objdata4['depart_id'],
                    'Name'=>'============'.$objdata4['depart_name']
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