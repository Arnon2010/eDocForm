<?php
header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");
$data[] = array('Cache'=>'clear success');

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>