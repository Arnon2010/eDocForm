<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$tbooksendId = $request->tbooksendId;
$nosendNext = $request->nosendNext;
$facId = $request->facId;

$sql = "SELECT * FROM tbnsend WHERE tbooksend_id = '$tbooksendId' AND fac_id = '$facId'";
$objrs = mysqli_query($con, $sql);
$row = mysqli_num_rows($objrs);

if($row == 0){
    $objsql = "INSERT INTO tbnsend(nsend_id, fac_id, tbooksend_id, nsend_no, status)
            VALUES(null, '$facId','$tbooksendId', '$nosendNext', '1')";
    mysqli_query($con, $objsql);
}else{
    $objsql = "UPDATE tbnsend SET nsend_no = '$nosendNext' WHERE tbooksend_id = '$tbooksendId' AND fac_id = '$facId'";
    mysqli_query($con, $objsql);
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

echo $nosendNext;

?>