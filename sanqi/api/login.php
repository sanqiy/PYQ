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
$zh = addslashes(htmlspecialchars($_POST["zh"]));
$mm = addslashes(htmlspecialchars($_POST["mm"]));
if ($zh == "" || $mm == "") {
	exit("参数不完整");
} else {
	if (strlen($zh) < 5 || strlen($zh) > 32) {
		exit("账号参数格式不正确");
	} else {
		if (strlen($mm) < 3 || strlen($mm) > 16) {
			exit("密码参数格式不正确");
		}
	}
}
if (ctype_space($zh) || ctype_space($mm)) {
	exit("参数不合法!");
}
$Y = "Y";
$m = "m";
$d = "d";
$H = "H";
$i = "i";
$s = "s";
$sj = date($Y . "-" . $m . "-" . $d . " " . $H . ":" . $i . ":" . $s);
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
} elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
} elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) {
	$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
	$ip = getenv("HTTP_X_FORWARDED_FOR");
} elseif (getenv("HTTP_CLIENT_IP")) {
	$ip = getenv("HTTP_CLIENT_IP");
} elseif (getenv("REMOTE_ADDR")) {
	$ip = getenv("REMOTE_ADDR");
} else {
	$ip = "Unknown";
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接数据库失败");
}
$sql = "select * from user where username = '{$zh}'";
$result = $conn->query($sql);
if (mysqli_num_rows($result) > 0) {
} else {
	exit("账号不存在!");
}
$mm = md5($mm);
$sql = "select * from user where username= '{$zh}' and password='{$mm}'";
$result = $conn->query($sql);
$row = mysqli_fetch_array($result);
if ($row) {
	$username = $row["username"];
	$email = $row["email"];
	$name = $row["name"];
	$img = $row["img"];
	$url = $row["url"];
	$passid = $row["passid"];
	$zzid = $row["id"];
	if ($row["ban"] == "-1") {
		if ($row["bantime"] != "true") {
			$dqsj = date("YmdHi");
			$fwsji = $row["bantime"];
			$fwsjio = str_ireplace("-", "", $fwsji);
			$fwsjop = str_ireplace(":", "", $fwsjio);
			$fwsj = str_ireplace(" ", "", $fwsjop);
			if ($fwsj < $dqsj) {
				$bdm = "user";
				$sql = "UPDATE {$bdm} SET ban='0',bantime='false' WHERE id='{$zzid}'";
				$result = $conn->query($sql);
				if ($result) {
				} else {
					echo mysql_errno();
				}
			}
			exit("你的账号已被封禁！解封时间为:" . $fwsji);
		} else {
			exit("该账号涉嫌违规,已被永久封禁!");
		}
	}
	$bdm = "user";
	$sql = "UPDATE {$bdm} SET logtime='{$sj}',logip='{$ip}' WHERE id='{$zzid}'";
	$result = $conn->query($sql);
	if ($result) {
	} else {
		exit("时间错误!");
	}
	setcookie("username", $username, time() + 604800, "/");
	setcookie("passid", $passid, time() + 604800, "/");
	echo "登录成功!";
} else {
	echo "密码错误！";
}
$conn->close();
