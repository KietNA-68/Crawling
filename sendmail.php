<?php
require_once 'simple_html_dom.php';
require_once 'conf.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
function Send_Mail($to,$addcc,$subject,$body){
  $receive = explode("," , $addcc);
  $mail = new PHPMailer;
  $mail->CharSet = "UTF-8";                               
  $mail->isSMTP();                                   
  $mail->Host = 'smtp.gmail.com';                   
  $mail->SMTPAuth = true;
  // $mail->SMTPDebug = 2; 
  $mail->Username   = 'test@gmail.com';                    
  $mail->Password   = 'test';                              
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
  $mail->SMTPSecure = 'tls';                            
  $mail->Port = 587;                                    
  $mail->setFrom('test@gmail.com', 'HSE');
  $address = $to;
  $mail->addAddress($address, $to);
  foreach($receive as $ccmail){
  $mail->addCC($ccmail);
  }
  $mail->addReplyTo('test@gmail.com', 'HSE');
  $mail->isHTML(true);                                  
  $mail->Subject = $subject;
  $mail->Body    = $body;
  $mail->AltBody = $body;
  $mail->send();
}
$result = $conn->query("SELECT * FROM tb_thau");
$row = $result->fetch_all();
$keyword = $final_keyword;
foreach($row as $rows){
  for ($s=0; $s < count($keyword); $s++) { 
    if(strpos($rows[0], $keyword[$s]) OR strpos($rows[1], $keyword[$s]) !== FALSE){
      $result_time = $conn->query("SELECT SUBSTRING(time, 1,10) FROM tb_thau");
      while($row_time_assoc = $result_time->fetch_assoc()){
      if($row_time_assoc["SUBSTRING(time, 1,10)"] !== NULL){
        $result_check = $conn->query("SELECT * FROM mail_thau WHERE SoTBMT = '$rows[6]'");
        $row_check = $result_check->fetch_assoc();
        if(!$row_check){
          $sql = "INSERT INTO `mail_thau`(`TenGoiThau`, `BenMoiThau`, `ThoiDiemDangTai`, `ThoiDiemPhatHanh`, `ThoiDiemDongThau`, `Link`, `SoTBMT`, `time`) VALUES ('$rows[0]','$rows[1]','$rows[2]','$rows[3]','$rows[4]','$rows[5]','$rows[6]',NOW())";
          $conn->query($sql);
        }
      }
    }
    }
  }
}
$result_test = $conn->query("SELECT * FROM test");
$row_test = $result_test->fetch_all();
foreach($row_test as $rows_test){
  for ($a=0; $a < count($final_keyword); $a++) { 
    if(strpos($rows_test[2], $final_keyword[$a]) OR strpos($rows_test[3], $final_keyword[$a]) !== FALSE){
        $result_check_test = $conn->query("SELECT * FROM mail_thau WHERE SoTBMT = '$rows_test[1]'");
        $row_check_test = $result_check_test->fetch_assoc();
        if(!$row_check_test){
          $sql = "INSERT INTO `mail_thau`(`TenGoiThau`, `BenMoiThau`, `ThoiDiemDangTai`, `ThoiDiemPhatHanh`, `ThoiDiemDongThau`, `Link`, `SoTBMT`, `time`) VALUES ('$rows_test[2]','$rows_test[3]','$rows_test[4]','$rows_test[5]','$rows_test[6]','$rows_test[7]','$rows_test[1]',NOW())";
          $conn->query($sql);
        }
    }
  }
}
$test = $conn->query("SELECT * FROM mail_thau");
while($rowtest = $test->fetch_assoc()){
      $arr = explode(" ", $rowtest["ThoiDiemPhatHanh"]);
      $result2 = $arr[1] . " " . $arr[0];
      $replace = str_replace("/", "-", $result2);
      $string1 = substr($replace, 6,2);
      $string1_result = "20".$string1;
      $string2 = substr($replace, 2,3);
      $string2_1_result = $string1_result.$string2;
      $string3 = substr($replace, 0,2);
      $string3_2_1_result = $string2_1_result."-".$string3;
      $string4 = substr($replace, 9,5);
      $string_final = $string3_2_1_result. " " . $string4.":00";
      $id = $rowtest['id'];
      $string_muasam = substr($rowtest["ThoiDiemPhatHanh"], 0,10);
      $replace_musam = str_replace("/", "-", $string_muasam);
      $string_muasam1 = substr($replace_musam, 6,4);
      $finals = $string_muasam1.substr($replace_musam, 2,3)."-".substr($replace_musam, 0,2);
      $conn->query("UPDATE mail_thau SET `time_test` = '$string_final',`time_test2` = '$finals' WHERE `id` = '$id'");
    }

$time = time();
$date = date("Y-m-d", $time);
$result = $conn->query("SELECT * FROM mail_thau WHERE SUBSTRING(time, 1, 10) = '$date'");
$row_mail = $result->fetch_all();
$data = '';
$m = 0;
while($m < count($row_mail)) {
    for($n = 1; $n < 8; $n++) {
      if($n == 1){
        $data .= "Tên gói thầu:"." ";
      }
      if($n == 2){
        $data .= "Bên mời thầu:"." ";
      }
      if($n == 3){
        $data .= "Thời điểm đăng tải:"." ";
      }
      if($n == 4){
        $data .= "Thời điểm phát hành:"." ";
      }
      if($n == 5){
        $data .= "Thời điểm đóng thầu:"." ";
      }
      if($n == 6){
        $data .= "Link:"." ";
      }
      if($n == 7){
        $data .= "Số TBMT:"." ";
      }
      $data .= $row_mail[$m][$n] . '<br>';
    }
    $data .= '<br><br>';
    $m++;
}
$result_mail = $conn->query("SELECT * FROM keyword");
$row_result_mail = $result_mail->fetch_assoc();
Send_Mail($row_result_mail['mail'],$row_result_mail['cc'],'HSE', $data);
?>