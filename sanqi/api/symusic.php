<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$arr = [["code" => "201", "msg" => "连接数据库失败"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$q = $_POST["q"];
if ($q == "") {
	$arr = [["code" => "201", "msg" => "空参数!"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$subtitle = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$username = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
}
if ($wzmusic == -1) {
	$arr = [["code" => "201", "mum" => $mum, "muurl" => $muurl]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$arr = explode(PHP_EOL, $wzmusic);
$arry = array_filter($arr);
$ary = count($arry);
$mu = rand(0, $ary - 1);
$yiny = $arry[$mu];
$arys = explode("|", $yiny);
$mum = $arys[0];
$muurl = $arys[1];
$muurl = preg_replace("/[\\s\\r\\n]+/", "", $muurl);
if ($mum == "网易音乐" && is_numeric($muurl)) {
	$mum = $arys[0];
	$muurl = "//music.163.com/song/media/outer/url?id=" . $muurl . ".mp3";
}
$arr = [["code" => "200", "mum" => $mum, "muurl" => $muurl]];
echo json_encode($arr);
$conn->close();
