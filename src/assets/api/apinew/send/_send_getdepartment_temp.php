<?php
session_start();
require('../db.php');
require('../fn.php');
@$edocType = $_GET['edoctype'];
@$edocId = $_GET['edocid'];
@$univId = $_GET['univid'];
@$otherDept = $_GET['other_dept'];
@$departId = $_GET['departid_user']; //หน่วยงานรับหนังสือ
@$userType = $_GET['user_type'];

@$departIdCore = getDepartmentIdCore($departId);

if($edocType != 'e'){ //ไม่ใช่หน่วยงานภายนอก
    $condtion = "WHERE depart_id NOT IN
            (
                SELECT depart_id
                FROM edoc_sent_temp
                WHERE edoc_id='$edocId'
            )
            AND univ_id = '$univId'";
}else{//หน่วยงานภายนอก
    $condtion = "WHERE univ_id = '$univId'";
}

// If selected other Department.
if($otherDept == '2')
    $condtion .= "AND depart_parent = '0' AND depart_id NOT IN ($departIdCore) AND depart_cancel = '0'";
else{
    $condtion .= "AND depart_id = '$departIdCore' AND depart_cancel = '0'";
}    
$objsql = "SELECT depart_id, depart_name, depart_parent 
        FROM department 
        $condtion
        ORDER BY depart_name ASC";
        
$data[] = array(
        'Id'=>'',
        'Name'=>'โปรดเลือกหน่วยงาน'
    );

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    //if($otherDept == '2'){
        //if($objdata['depart_id'] != $departId){
            $data[] = array(
                'Id'=>$objdata['depart_id'],
                'Name'=>$objdata['depart_name'],
            );
        //}
    //}
    $objsql2 = "SELECT depart_id, depart_name, depart_parent 
        FROM department 
        WHERE depart_id NOT IN
            (
                SELECT depart_id
                FROM edoc_sent_temp
                WHERE edoc_id='$edocId'
            )
        AND univ_id = '$univId'  
        AND depart_parent = '$objdata[depart_id]' AND depart_cancel = '0'
        ORDER BY depart_name ASC";
    $objrs2 = mysqli_query($con, $objsql2);
    while($objdata2 = mysqli_fetch_array($objrs2)){
        $data[] = array(
            'Id'=>$objdata2['depart_id'],
            'Name'=>'==='.$objdata2['depart_name']
        );
        
        
        $objsql3 = "SELECT depart_id, depart_name, depart_parent 
            FROM department 
            WHERE depart_id NOT IN
                (
                    SELECT depart_id
                    FROM edoc_sent_temp
                    WHERE edoc_id='$edocId'
                )
            AND univ_id = '$univId'
            AND depart_parent = '$objdata2[depart_id]' AND depart_cancel = '0'
            ORDER BY depart_name ASC";
        $objrs3 = mysqli_query($con, $objsql3);
        while($objdata3 = mysqli_fetch_array($objrs3)){
            $data[] = array(
                'Id'=>$objdata3['depart_id'],
                'Name'=>'======'.$objdata3['depart_name']
            );
            
            $objsql4 = "SELECT depart_id, depart_name, depart_parent 
                FROM department 
                WHERE depart_id NOT IN
                    (
                        SELECT depart_id
                        FROM edoc_sent_temp
                        WHERE edoc_id='$edocId'
                    )
                AND univ_id = '$univId'
                AND depart_parent = '$objdata3[depart_id]' AND depart_cancel = '0'
                ORDER BY depart_name ASC";
            $objrs4 = mysqli_query($con, $objsql4);
            while($objdata4 = mysqli_fetch_array($objrs4)){
                $data[] = array(
                    'Id'=>$objdata4['depart_id'],
                    'Name'=>'========='.$objdata4['depart_name']
                );
                
                $objsql5 = "SELECT depart_id, depart_name, depart_parent 
                    FROM department 
                    WHERE depart_id NOT IN
                        (
                            SELECT depart_id
                            FROM edoc_sent_temp
                            WHERE edoc_id='$edocId'
                        )
                    AND univ_id = '$univId'
                    AND depart_parent = '$objdata4[depart_id]' AND depart_cancel = '0'
                    ORDER BY depart_name ASC";
                $objrs5 = mysqli_query($con, $objsql5);
                while($objdata5 = mysqli_fetch_array($objrs5)){
                    $data[] = array(
                        'Id'=>$objdata5['depart_id'],
                        'Name'=>'============'.$objdata5['depart_name']
                    );
        
                }
    
            }
      
        }
        
    }
}

//$data[] = array('resp'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>