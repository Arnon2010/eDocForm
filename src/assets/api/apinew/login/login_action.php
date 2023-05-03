<?php
require('../db.php');
require('../fn.php');
include_once"../rmutsv_login.php";
$data = json_decode(file_get_contents("php://input"));

$User = mysqli_real_escape_string($con, $data->User);
$Pass = mysqli_real_escape_string($con, $data->Pass);
$departAllow = mysqli_real_escape_string($con, $data->depart_allow);
    
if(rmutsv_login::login("$User","$Pass")){

	$charmap="1234ABCDEFGHIJKLMNOPQRSTUYWXYZabcdefghijklmnopqrstuvwxyz";
	$codRandom = str_shuffle($charmap);
	
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
	
	$query = mysqli_query($con, $sql);
	$rows = mysqli_fetch_assoc($query);
	if(!$rows){
		//echo 'false';
		$admin[] = array(
		'status' => 'false',
		'message' => 'คุณไม่มีสิทธิ์ในการเข้าใช้งานระบบ !! กรุณาติดต่อผู้ดูแล'
		);
	}else{
		session_start();
		$_SESSION["token"] = $codRandom;
		$_SESSION["UserName"] = $rows["user_name"];
        $_SESSION["UserId"] = $rows["user_id"];
		$_SESSION["Fname"] = $rows["user_fname"];
		$_SESSION["Lname"] = $rows["user_lname"];
		$_SESSION["departName"] = getDepartmentClass($rows["depart_id"]);
		$_SESSION["departId"] = $rows["depart_id"];
		$_SESSION["departCode"] = $rows["depart_code"];
		$_SESSION["userType"] = $rows["usertype_code"];
		$_SESSION["univId"] = $rows["univ_id"];

		$_SESSION["version_new"] = $rows["version_new"]; // New version
		$_SESSION["acknow_permis"] = $rows["acknow_permis"]; // acknow_permis
		$_SESSION["map_dept_code"] = $rows["map_dept_code"];
		 
		$admin[] = array(
			'status' => 'true',
			'message' => 'this log in user',
			'User' => $_SESSION["UserName"],
			'access_token' => $_SESSION["token"],
			'Fname' => $_SESSION["Fname"],
			'Lname' => $_SESSION["Lname"],
			'departName' => $_SESSION["departName"],
			'departId' => $_SESSION["departId"],
			'departCode' => $_SESSION["departCode"],
			'userType' => $_SESSION["userType"],
			'userId' => $_SESSION["UserId"],
			'univId' => $_SESSION["univId"],
			'newVersion' => $_SESSION["version_new"],
			'acknowPermis' => $_SESSION["acknow_permis"],
			'mapDeptCode' => $_SESSION["map_dept_code"]
			
		);
	}
	if(strpos($_SERVER['REMOTE_ADDR'], "178.16.163") === 0)
	{
		//die();
		$alow_login = true;
	}else {
		$alow_login = false;
	}
	$admin[] = array('IP' =>$_SERVER['REMOTE_ADDR'], 'alow_acess'=>$alow_login);

}

else{
	$admin[] = array(
		'status' => 'false',
		'message' => 'ชื่อผู้ใช้ หรือรหัสผ่านของท่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง !!'
		);
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$admin));

