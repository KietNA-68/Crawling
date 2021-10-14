<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'simple_html_dom.php';
require_once 'config.php';
$t = time();
$s = date("d/m/Y",$t);
$t1 = date('d/m/Y',strtotime("-1 days"));
$url = '[CENSORED]';
$dom = file_get_html($url);
$result = [];
foreach ($dom->find('.bidding-list-body > .item') as $key => $item) {
	$data = [];

	$TBMT = explode('  ', $item->find('.c-number', 0)->plaintext);
	array_push($data, ['TBMT' => $TBMT[1]]);

	$tenGoiThau = $item->find('a.bidding_link', 0);
	$domain = '[CENSORED]';
	array_push($data, [
		'name' => [
			'title' => $tenGoiThau->title,
			'url' => $domain.$tenGoiThau->href
		]
	]);

	$benThau = $item->find('.c-author a', 0)->title;
	array_push($data, ['author' => $benThau]);

	$closeTime = explode('  ', $item->find('.c-close', 0)->plaintext);
	array_push($data, ['close_time' => $closeTime[1]]);

	$publishTime = explode('  ', $item->find('.c-pub', 0)->plaintext);
	array_push($data, ['publish_time' => $publishTime[1]]);

	array_push($result, $data);

}
if(is_array($result)){
	foreach ($result as $row) {
		$tbmt = mysqli_real_escape_string($conn, $row[0]['TBMT']);
		$author = mysqli_real_escape_string($conn, $row[2]['author']);
		$pub = mysqli_real_escape_string($conn, $row[4]['publish_time']);
		$close = mysqli_real_escape_string($conn, $row[3]['close_time']);
		$title = mysqli_real_escape_string($conn, $row[1]['name']['title']);
		$url1 = mysqli_real_escape_string($conn, $row[1]['name']['url']);
		$sql1 = "SELECT SoTBMT FROM tb_thau WHERE SoTBMT='$tbmt'";
			//echo $sql1;
		$result1 = $conn->query($sql1);
		$rows = $result1->fetch_assoc();
			//var_dump($rows);
		if(!$rows){
			$sql = "INSERT INTO `tb_thau`(`TenGoiThau`, `BenMoiThau`, `ThoiDiemDangTai`, `ThoiDiemPhatHanhHSMT`, `ThoiDiemDongThau`, `Link`, `SoTBMT`,`time`) VALUES ('".$title."','".$author."','".$pub."','".$close."','".$close."','".$url1."','".$tbmt."',NOW())";
			$result = $conn->query($sql);}
		}
		$test = $conn->query("SELECT * FROM tb_thau");
		while($rowtest = $test->fetch_assoc()){
			$arr = explode(" ", $rowtest["ThoiDiemPhatHanhHSMT"]);
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
			$id = $rowtest['Id'];
			$conn->query("UPDATE tb_thau SET `time_test` = '$string_final' WHERE `Id` = '$id'");
		}
	}
			//$sql1 = "SELECT Link,TenGoiThau,SoTBMT,ThoiDiemDangTai,ThoiDiemDongThau,BenMoiThau, COUNT(*) FROM tb_thau GROUP BY Link,TenGoiThau,SoTBMT,ThoiDiemDangTai,ThoiDiemDongThau,BenMoiThau HAVING COUNT(*) > 1";
			//$result1 = $conn->query($sql1);
	?>
