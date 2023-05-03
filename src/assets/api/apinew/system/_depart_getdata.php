<?php
require('../db.php');
@$departId = $_GET['departid'];
@$univId = $_GET['univ'];

if($departId == 'null' || $departId == '0')
    @$condition = "AND depart_parent = '0'";
else
    @$condition = "AND depart_parent = '$departId'";
    
    /*
    $objsql = "SELECT da.depart_id,
        da.depart_parent,
        dh.head_depart_id,
        da.depart_name AS dept_name,
        CASE
            WHEN da.depart_parent = 0 THEN 'ไม่มีสังกัด'
            WHEN da.depart_parent <> 0 THEN da.depart_name
            END AS core_depart,
        da.depart_code
        FROM department da
        LEFT JOIN department_hier dh ON da.depart_id = dh.under_depart_id
        WHERE da.univ_id = '$univId'
        $condition
        AND  da.depart_cancel = '0'
        ORDER BY da.depart_id ASC";
    */
    
$objsql = "SELECT depart_id,
        depart_parent,
        depart_name AS dept_name,
        depart_code,
        depart_cancel,
        version_new,
        univ_no_status,
        get_univ_no
        FROM department
        WHERE univ_id = '$univId'
        $condition
        ORDER BY depart_name ASC";
        
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'deptId'=>$objdata['depart_id'],
        'coreDeptId'=>$objdata['depart_parent'],
        'deptCode'=>$objdata['depart_code'],
        'deptName'=>$objdata['dept_name'],
        'deptStatus'=>$objdata['depart_cancel'],
        'newVersion'=>$objdata['version_new'],
        'univNoStatus'=>$objdata['univ_no_status'],
        'getUnivNo'=>$objdata['get_univ_no']
    );
}

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);
@print json_encode(array("data"=>$data));
?>