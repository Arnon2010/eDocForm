<?php
include "../db.php";

$facId = $_GET['facid'];
$sql = "SELECT fac_bookcode FROM tbfaculty WHERE fac_id = '$facId'";
$result = mysqli_query($con, $sql);
while($rows = mysqli_fetch_assoc($result))
{
    $data[] = array(
        "facBookcode"=> $rows['fac_bookcode']
    );
}
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
