<?php
require_once "dbacc3dhr.php";

$pattern = $_POST['pattern'];
$deptcode = $_POST['deptcode'];

$data = array();

// $strSQL = "SELECT
//     U.CITIZEN_ID,
//     U.STF_FNAME,
//     U.STF_LNAME,
//     U.FACULTY_CODE,
//     F.FACULTY_TNAME,
//     PF.PREFIXOFFICE_TNAME,
//     PN.PREFIX_NAME
// FROM UOC_STAFF U 
// INNER JOIN REF_PREFIX PN ON U.PREFIX_ID = PN.PREFIX_ID
// INNER JOIN FACULTY F ON U.FACULTY_CODE = F.FACULTY_CODE
// INNER JOIN PREFIXOFFICE PF ON F.PREFIXOFFICE_CODE = PF.PREFIXOFFICE_CODE
// WHERE  (U.UOC_STAFF_ASTATUS = 1)
// AND U.FACULTY_CODE = '$deptcode' 
// AND (U.STF_FNAME like '%$pattern%' OR U.STF_LNAME like '%$pattern%')
// ORDER BY F.FACULTY_TNAME, U.STF_FNAME, U.STF_LNAME ASC";

// $objQuery = sqlsrv_query($dbacc3d_connect, $strSQL);
// //$objnumRow = sqlsrv_num_rows($objQuery);

// //sqlsrv_close($dbacc3d_connect);
// $No = 0;

// while($objResult = sqlsrv_fetch_array($objQuery, SQLSRV_FETCH_ASSOC)){ $No++;
//     $CITIZEN_ID = trim($objResult['CITIZEN_ID']);
//     $PREFIX_NAME = trim($objResult['PREFIX_NAME']);
//     $STF_FNAME_TH = trim($objResult['STF_FNAME']);
//     $STF_LNAME_TH = trim($objResult['STF_LNAME']);
//     $FACULTY_CODE = trim($objResult['FACULTY_CODE']);
//     $FACULTY_TNAME= trim($objResult['FACULTY_TNAME']);
//     $PREFIXOFICE_TNAME= trim($objResult['PREFIXOFFICE_TNAME']);

//     $data[] = array(
//         'Name'=>$PREFIX_NAME.$STF_FNAME_TH.' '.$STF_LNAME_TH,
//         'PersonId'=>$CITIZEN_ID,
//         'FACULTY_CODE'=>$FACULTY_CODE,
//         'FACULTY_TNAME'=>$PREFIXOFICE_TNAME.''.$FACULTY_TNAME
//     );

// }

$data[] = array(
    'Name'=>'อานนท์ หลงหัน',
);
    

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>