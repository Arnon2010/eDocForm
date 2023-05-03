    <?php
        require('../db.php');
        require('../fn.php');
        $request = json_decode(file_get_contents("php://input"));    
        //date_default_timezone_set("Asia/Bangkok");

        @$fileName = $_FILES['file']['name'][0];
        @$fileSize = $_FILES['file']['size'][0];
        @$fileType = $_FILES['file']['type'][0];
        
        $File_Type_Allow = array("application/pdf"); //กำหนดประเภทของไฟล์ว่าไฟล์ประเภทใดบ้างที่อนุญาตให้ upload มาที่ Server
        $Max_File_Size = 100000000; //กำหนดขนาดไฟล์ที่ ใหญ่ที่สุดที่อนุญาตให้ upload มาที่ Server มีหน่วยเป็น byte
        
        function validate_form($file_input,$file_size,$file_type) { //เป็น function ที่เอาไว้ตรวจสอบว่าไฟล์ที่ผู้ใช้ upload ตรงตามเงื่อนไขหรือเปล่า
            global $Max_File_Size,$File_Type_Allow;
            if (!$file_input) {
               $error = "กรุณาเลือกไฟล์หนังสือ/เอกสาร !!";
            } elseif ($file_size > $Max_File_Size) {
               $error = "ขนาดไฟล์ใหญ่กว่า $Max_File_Size ไบต์ !!";
            } elseif (!check_type($file_type,$File_Type_Allow)) {
               $error = "อัพโหลดได้เฉพาะไฟล์นามสกุล .pdf !!";
            } else {
               $error = false;
            }
         
            return $error;
       }
       
       function check_type($type_check) { //เป็น ฟังก์ชัน ที่ตรวจสอบว่า ไฟล์ที่ upload อยู่ในประเภทที่อนุญาตหรือเปล่า
            global $File_Type_Allow;
            for ($i=0;$i<count($File_Type_Allow);$i++) {
               if ($File_Type_Allow[$i] == $type_check) {
                  return true;
               }
            }
            return false;
       }
       
       $error_msg = validate_form($fileName, $fileSize, $fileType); // ตรวจดูว่า ไฟล์ที่ upload ตรงตามเงื่อนไขหรือเปล่า
       
       $data[] = array(
            'validFile'=>$error_msg
        );
        
        header("Access-Control-Allow-Origin: *");
        header("content-type:text/javascript;charset=utf-8");
        header("Content-Type: application/json; charset=utf-8", true, 200);
        print json_encode(array("data"=>$data));
    ?>