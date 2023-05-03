<?php
include_once "../db.php";

//Update  deptcode parant

$objsql = "SELECT depart_parent, depart_id, depart_name, map_dept_code 
FROM department 
WHERE depart_parent = 0 ORDER BY depart_name ASC";
$objres = mysqli_query($con, $objsql);
while($data = mysqli_fetch_array($objres)) {
    $depart_id = $data['depart_id'];
    $map_dept_code = $data['map_dept_code'];
    $depart_name = $data['depart_name'];
    $depart_parent = $data['depart_parent'];

    echo $depart_name.' map_dept_code= '.$map_dept_code;
    echo '<br/>';

    // where parent
    echo $objsql_upd = "UPDATE department SET map_dept_code = '$data[map_dept_code]' 
        WHERE depart_parent = '$data[depart_id]'";
    mysqli_query($con, $objsql_upd);
    echo '<br/>';
}

?>