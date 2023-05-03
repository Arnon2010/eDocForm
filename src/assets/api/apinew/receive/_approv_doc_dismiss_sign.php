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

            //step 3
            //กรณีมีการส่งต่อแล้วยกเลิก 


            if($dataMove['activity'] == 'Signatured'){
                $update_detail_before = "UPDATE sign_detail SET sign_status = '2'  
                    WHERE main_id='$main_id' 
                    AND detail_id = '$dataMove[detail_id]'
                    ";
                mysqli_query($con, $update_detail_before);

                $main_status = '2';
            }else{
                $main_status = '0'; //ยังไม่มีการเกษียนหรือลงนาม
            }

            $update_main_before = "UPDATE sign_main SET main_status = '$main_status'  
                WHERE main_id='$main_id'";
            mysqli_query($con, $update_main_before);

            $status = true;
        }
       
    }
        
}else{
   //test
}

$data[] = array(
    'status'=> $status,
    'resp'=>$objsql_move
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));