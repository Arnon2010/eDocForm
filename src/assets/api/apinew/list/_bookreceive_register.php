<?php
session_start();
require('../db.php');
$booksendId = $_POST['booksenddetail_id'];
$nReceive = $_POST['nreceive'];
$facCode = $_POST['faccode'];
$bookCode = $_POST['bookcode'];
$userId = $_POST['userid'];
$tbookreceiveId = $_POST['tbookreceive'];

$bookreceiveNumber = $bookCode.''.$nReceive;

$sql_main = "INSERT INTO tbbookreceive_main(
        breceive_id,
        booksenddetail_id,
        breceive_number,
        breceive_date,
        tbookreceive_id,
        faccode,
        userid,
        datemodifiled,
        status
        )
    VALUES(
        null,
        '$booksendId',
        '$bookreceiveNumber',
        '".date('Y-m-d')."',
        '$tbookreceiveId',
        '$facCode',
        '$userId',
        '".date('Y-m-d H:i:s')."',
        '1'
    )";
if(mysqli_query($con, $sql_main) ){
    
        if($nReceive == 1){
        $objsql2 = "INSERT INTO tbnreceive(nreceive_id, fac_code, tbookreceive_id, nreceive_no, status)
            values(
            null,'$facCode', '$tbookreceiveId', '$nReceive', '1'
            )";
        }else{
            $objsql2 = "UPDATE tbnreceive SET nreceive_no='$nReceive' WHERE fac_code='$facCode' AND tbookreceive_id = '$tbookreceiveId'";
        }
   
        $objsql = "UPDATE tbbooksend_detail SET status = 'C'
            WHERE booksenddetail_id = '$booksendId'";
        mysqli_query($con, $objsql);
        $status = 1;
    
}


$arr = array(
    'status'=> $status
);

echo json_encode($arr);
