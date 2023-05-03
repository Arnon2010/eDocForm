<?php
    require('../db.php');

    //$departIduser = $_GET['depart_iduser'];
    $Year = ($_GET['year']-543);

    //กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
        
    $objsql = "SELECT count(main_id) as amount_order
        FROM sign_main
        WHERE main_status = '2'
        AND year(create_date) = '$Year'
        AND doc_type = '1' 
        AND apply_number_univ IN (1,2)
        ";
        
    $objrs = mysqli_query($con, $objsql);
    $i = 0;
    $objdata = mysqli_fetch_assoc($objrs);
    $amount = $objdata['amount_order'];
    
    $data[] = array(
        'amountOrder'=>$amount,
        'resp sql:'=>''
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
