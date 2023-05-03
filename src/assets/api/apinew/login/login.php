<?php
require('../db.php');
require('../fn.php');
include_once "../rmutsv_login.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding, X-Auth-Token, content-type');
header("Content-Type: application/json");

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$User = mysqli_real_escape_string($con, trim($request->User));
$Pass = mysqli_real_escape_string($con, trim($request->Pass));
$departAllow = mysqli_real_escape_string($con, $request->depart_allow);

if(rmutsv_login::login("$User","$Pass")){

    if (isset($postdata) && !empty($postdata)) {
       
        if($departAllow){
            $condition = "AND u.user_status = '1' AND u.depart_id = '$departAllow' AND d.depart_cancel = '0'";
        }else{
            $condition = "AND u.user_status = '1' AND d.depart_cancel = '0'";
        }

        $sql = "SELECT u.user_id, u.user_name, u.user_fname, u.user_lname, t.usertype_code,
            d.depart_name, d.depart_id, d.depart_code, d.univ_id, d.version_new, d.acknow_permis,d.map_dept_code
            FROM edoc_user u
            LEFT JOIN department d ON u.depart_id = d.depart_id
            LEFT JOIN tbusertype t ON u.user_type = t.usertype_id
            WHERE u.user_name = '$User'
            AND u.depart_id IN (
                SELECT depart_id FROM department
            )
            $condition
            ";
        
        if ($result = mysqli_query($con, $sql)) {
            $rows = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            http_response_code(404);
        }
    }

}else {

}