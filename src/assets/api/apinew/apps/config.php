<?php
// define('DB_NAME', 'edoc_new');
// define('DB_USER', 'root');
// define('DB_PASSWORD', 'arnonrmutsv');
// define('DB_HOST', 'localhost');

define('DB_NAME', 'edoc');
define('DB_USER', 'edoc');
define('DB_PASSWORD', 'vkoomN@edoc2019');
define('DB_HOST', 'localhost');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

@date_default_timezone_set("Asia/Bangkok");

function DateThai($strDate)
{
	$strYear = date("Y",strtotime($strDate))+543;
	$strMonth= date("n",strtotime($strDate));
	$strDay= date("j",strtotime($strDate));
	$strHour= date("H",strtotime($strDate));
	$strMinute= date("i",strtotime($strDate));
	$strSeconds= date("s",strtotime($strDate));
	//$strMonthCut = array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	//$strMonthThai=$strMonthCut[$strMonth];
    	$datethai = $strDay.'/'.$strMonth.'/'.$strYear.', '.$strHour.':'.$strMinute.':'.$strSeconds;
	return $datethai;
}


