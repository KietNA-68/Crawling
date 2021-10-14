<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'simple_html_dom.php';
require_once 'config.php';
$t = time();
$s = date("d/m/Y",$t);
$t1 = date('d/m/Y',strtotime("-1 days"));
$url = '[CENSORED]';
$data_url =  '[CENSORED]';
for ($i=0; $i < count($data_url) ; $i++) { 
	//echo $data_url[$i].'<br>';	
	$urlcheck = str_replace("/keyword/", $data_url[$i], $url);
	$dom = file_get_html($urlcheck);
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
	}
}
			//$sql1 = "SELECT Link,TenGoiThau,SoTBMT,ThoiDiemDangTai,ThoiDiemDongThau,BenMoiThau, COUNT(*) FROM tb_thau GROUP BY Link,TenGoiThau,SoTBMT,ThoiDiemDangTai,ThoiDiemDongThau,BenMoiThau HAVING COUNT(*) > 1";
			//$result1 = $conn->query($sql1);
?>
