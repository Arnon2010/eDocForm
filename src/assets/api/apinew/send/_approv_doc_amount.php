<?php
    require('../db.php');

    $departId = $_GET['depart_id'];
    $Year = ($_GET['year']-543);

    //กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
        
    $objsql = "SELECT count(main_id) as amount_order
        FROM sign_main
        WHERE main_status in (1,2,3,0)
        AND year(create_date) = '$Year'
        AND depart_id = '$departId' 
        AND doc_type = '1' 
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
