<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$userFname = $request->userFname;
$userLname = $request->userLname;
$userEpass = $request->userEpass;
$Secrets = $request->Secrets;
$privilegeEdit = $request->privilegeEdit;
$privilegeBrowsing = $request->privilegeBrowsing;
$userType = $request->userType;
$facultyCode = $request->facultyCode;
$facultyCodeNew = $request->facultyCodeNew;

$objsql = "INSERT INTO tbuser(userId,
    userFname,
    userLname,
    userEpass,
    secrets_id,
    privbrow_id,
    privedit_id,
    usertype_id,
    fac_id,
    userStatus) VALUES(null,
    '$userFname',
    '$userLname',
    '$userEpass',
    '$Secrets',
    '$privilegeBrowsing',
    '$privilegeEdit',
    '$userType',
    '$facultyCode',
    '1'
    )";
            
if(mysqli_query($con, $objsql))
    $status = "true";
else
    $status = "false";
    
$data[] = array(
    'status'=>$status,
    'facid'=>$facultyCode
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>