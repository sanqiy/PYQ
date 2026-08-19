<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	$arr = [["code" => "201", "msg" => "非法请求"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
	exit("请先登录");
}
$wid = addslashes(htmlspecialchars($_POST["wid"]));
$lx = addslashes(htmlspecialchars($_POST["lx"]));
if ($wid == "" || $lx == "") {
	exit("未传入参数");
}
$iteace = 1;
include "../config.php";
include "./wz.php";
$data_result = mysqli_query($conn, "select * from user where username='{$user_zh}'");
$data2_row = mysqli_fetch_array($data_result);
$zjzhq = $data2_row["username"];
$zjnc = $data2_row["name"];
$zjimg = $data2_row["img"];
$zjhomeimg = $data2_row["homeimg"];
$zjsign = $data2_row["sign"];
$zjemail = $data2_row["email"];
$zjurl = $data2_row["url"];
$zjfbqx = $data2_row["essqx"];
$zjemtz = $data2_row["esseam"];
$passid = $data2_row["passid"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("账号信息异常,请重新登录");
}
if ($user_zh == $glyadmin) {
} else {
	exit("没有权限");
}
if ($lx == "sw") {
	$arsj = explode(PHP_EOL, $topes);
	if (in_array($wid, $arsj)) {
		exit("该文章已置顶过了");
	}
	$xtopes = $topes . PHP_EOL . $wid;
	$sql = "UPDATE admin SET topes='{$xtopes}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
		exit("已设为置顶");
	} else {
		exit("置顶失败");
	}
}
if ($lx == "qx") {
	if ($topes == "") {
		exit("没有任何置顶文章");
	}
	if (strstr($topes, $wid . PHP_EOL)) {
		$qxsu = $wid . PHP_EOL;
	} else {
		if (strstr($topes, PHP_EOL . $wid)) {
			$qxsu = PHP_EOL . $wid;
		} else {
			if (strstr($topes, PHP_EOL . $wid . PHP_EOL)) {
				$qxsu = PHP_EOL . $wid . PHP_EOL;
			} else {
				$qxsu = $wid;
			}
		}
	}
	$xtopsu = str_replace($qxsu, "", $topes);
	$sql = "UPDATE admin SET topes='{$xtopsu}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
		exit("已取消置顶");
	} else {
		exit("取消置顶失败");
	}
}
$conn->close();
