<?php
require('../db.php');
$main_id = $_GET['main_id'];
$tposition_id = $_GET['tposition_id'];

@$status = false; 

//step 1
$update_detail = "UPDATE sign_detail SET detail_status = 'D'  
    WHERE main_id='$main_id' 
    AND tposition_id = '$tposition_id'
    ";

if(mysqli_query($con, $update_detail)){
    $update_move = "UPDATE sign_move SET move_status = 'D'  
    WHERE main_id='$main_id' 
    AND tposition_id = '$tposition_id'
    ";
    if(mysqli_query($con, $update_move)){

        $update_sequ = "UPDATE sign_sequence SET sequ_status = 'D'  
        WHERE main_id='$main_id' 
        AND tposition_id = '$tposition_id'
        ";
        if(mysqli_query($con, $update_sequ)){

            //step 2
            //last move signed
            $objsql_move = "SELECT mv.activity, mv.detail_id, mv.tposition_id, mx.moveidMax
                FROM (
                    SELECT main_id, MAX(move_id) as moveidMax FROM sign_move 
                    WHERE main_id = '$main_id' 
                    AND move_status = '1' 
                    GROUP BY main_id
                    
                ) mx
                INNER JOIN sign_move mv ON mv.main_id = mx.main_id AND mv.move_id = mx.moveidMax
                WHERE mv.main_id = '$main_id' 
                AND mv.move_status = '1' 
                GROUP BY mv.main_id
                ";

            $objrs_move = mysqli_query($con, $objsql_move);
            $dataMove = mysqli_fetch_array($objrs_move);

            $mainStatus = '0';

            //step 3
            //กรณีมีการส่งต่อแล้วยกเลิก
            if($dataMove['activity'] == 'Signatured'){
                $update_detail_before = "UPDATE sign_detail SET sign_status = '2'  
                WHERE main_id='$main_id' 
                AND detail_id = '$dataMove[detail_id]'
                ";
                mysqli_query($con, $update_detail_before);

                $mainStatus = '2';

            }

            //กรณีมีการเสนอลงนามหลังออกเลขแล้ว
            $row_main = mysqli_fetch_array(mysqli_query($con, "SELECT status_get_no 
                FROM sign_main 
                WHERE main_id = '$main_id'"));

            if($row_main['status_get_no'] == '1')
                $mainStatus = '3';
            
            $update_main = mysqli_query($con, "UPDATE sign_main SET main_status = '$mainStatus' 
                WHERE main_id = '$main_id'");

            $status = true;
        }
       
    }
        
}else{
   //test
}

$data[] = array(
    'status'=> $status,
    'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));