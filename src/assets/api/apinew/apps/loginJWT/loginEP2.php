<?php
$username= "user";
$password= "password";

$data = elogin2($username, $password);

if ($data->{"success"} == "true") {
	echo "fullname = ".$data->{"fullname"};
	echo "<br>name = ".$data->{"name"};
	echo "<br>surname = ".$data->{"surname"};
	echo "<br>personalid = ".$data->{"personalid"};
	echo "<br>email = ".$data->{"email"};
}else {
	echo "not success";
}

function elogin2($username, $password) {
	$loginUrl = 'https://elogin2.rmutsv.ac.th';
 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$resultjson = curl_exec($ch);
	return json_decode($resultjson);
}
?>
