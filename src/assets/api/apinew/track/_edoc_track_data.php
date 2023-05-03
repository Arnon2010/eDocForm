<?php
require('../db.php');
require('../fn.php');

$edocId = $_GET['edocid'];

$objsql = "SELECT t.operation, d.depart_id, d.depart_name, t.track_time, t.track_date, t.detail, u.user_fname, u.user_lname
    FROM  edoc_track t 
    LEFT JOIN edoc_user u ON t.user_id = u.user_id
    LEFT JOIN department d ON t.depart_id = d.depart_id
    WHERE t.edoc_id = '$edocId'
    GROUP BY t.track_time, track_date
    ORDER BY t.track_date DESC, t.track_time DESC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
      
    $trackDate = explode("-",$objdata['track_date']);
    $trackDate_new = $trackDate[2].'/'.$trackDate[1].'/'.($trackDate[0]+543);
    
    $departName = getDepartmentClass($objdata['depart_id']);

    $data[] = array(
        'No'=>$i,
        'trackTime'=>$objdata['track_time'],
        'departName'=>$departName,
        'trackDate'=>$trackDate_new,
        'Operation'=>$objdata['operation'],
        'userOperate'=>$objdata['user_fname'].' '.$objdata['user_lname'],
        'Detail'=>$objdata['detail']
    );
    
}
//$data[] = array('sql'=>$departIduser);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
