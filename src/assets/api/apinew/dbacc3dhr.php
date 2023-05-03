<?php 
 //server acc3d
    //*
    $host="172.16.162.39"; //203.158.177.8 (เดิม)
    $user="useracc3druts";
    $pass="acc3d%19701";
    $dbname="ACC3D";
    /*
    $dbacc3d_connect = mssql_connect($serversacc3d,$useracc3d,$passsacc3d);
    mssql_select_db($dbnameacc3d,$dbacc3d_connect);
    */
    $connect=mssql_connect($host,$user,$pass);
    $data=mssql_query($dbname);
    $objDB = mssql_select_db($dbname);
	mssql_query("SET NAMES UTF8");
	mssql_query("SET character_set_results=utf8");
	mssql_query("SET character_set_client=utf8");
	mssql_query("SET character_set_connection=utf8");
	mb_internal_encoding('UTF-8');
	mb_http_output('UTF-8');
	mb_http_input('UTF-8');
	mb_language('uni');
	mb_regex_encoding('UTF-8');
	ob_start('mb_output_handler');
	setlocale(LC_ALL, 'th_TH');
	//echo mssql_error();
?>