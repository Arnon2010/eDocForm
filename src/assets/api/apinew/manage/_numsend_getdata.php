<?php
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

require('../db.php');
$facId = $_GET['facid'];
$tbookId = $_GET['tbookid'];
$objsql = "SELECT * FROM tbnsend WHERE status = '1' AND fac_id = '$facId' AND tbooksend_id = '$tbookId'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
$row = mysqli_num_rows($objrs);
if($row == 0){
    echo 1;
}else{
    echo $objdata[nsend_no]+1;
}
