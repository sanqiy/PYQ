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
$useke = addslashes(htmlspecialchars($_POST["useke"]));
$useem = addslashes(htmlspecialchars($_POST["useem"]));
$useyz = addslashes(htmlspecialchars($_POST["safyzm"]));
$usexm = addslashes(htmlspecialchars($_POST["safxmm"]));
$tig = addslashes(htmlspecialchars($_POST["tig"]));
if ($tig == "") {
	exit("未传入参数!");
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
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
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
	$copyright = $row["copyright"];
	$beian = $row["beian"];
	$topes = $row["topes"];
}
if ($tig == 0) {
	if ($useke == "" || $useem == "") {
		exit("未传入账号或邮箱!");
	}
	$sql = "select * from user where username= '{$useke}' and email='{$useem}'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_array($result);
	if ($row) {
		$suiyzm = rand(1000000, 9999999);
		if ($emydz != "" && $emssl != "" && $emduk != "" && $emkey != "" && $emzh != "" && $emfs != "" && $emfszm != "") {
			$mailapfs = "nopost";
			$xiangym = "ok";
			$mailtitle = "[" . $name . "]" . "验证码";
			$title = "您正在网站：【" . $name . "】申请找回密码，若不是您本人操作,您的账号可能存在安全隐患，两分钟内有效，验证码为：";
			$text = $suiyzm;
			$mailbox = $useem;
			include "./sendmail.php";
			setcookie("suiyzm", md5(md5($suiyzm)), time() + 120, "/");
		} else {
			exit("请网站管理员先配置邮箱!");
		}
	} else {
		echo "账号与邮箱不匹配!";
	}
}
if ($tig == 1) {
	if (strlen($usexm) < 3 || strlen($usexm) > 16) {
		exit("密码参数格式不正确");
	}
	$sql = "select * from user where username= '{$useke}' and email='{$useem}'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_array($result);
	if ($row) {
	} else {
		echo "账号与邮箱不匹配!";
		exit;
	}
	$hcyzm = $_COOKIE["suiyzm"];
	if ($hcyzm == "") {
		echo "请先获取验证码!";
		exit;
	}
	$jmhyz = md5(md5($useyz));
	if ($jmhyz == $hcyzm) {
		$data_result = mysqli_query($conn, "select * from user where username='{$useke}'");
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
		$zid = $data2_row["id"];
		$zjpassword = $data2_row["password"];
		$usexm = md5($usexm);
		if ($zjpassword == $usexm) {
			exit("新密码不能与旧密码相同!");
		}
		$sql = "UPDATE user SET password='{$usexm}' WHERE id='{$zid}'";
		$result = $conn->query($sql);
		if ($result) {
			setcookie("suiyzm", "", time() + 0, "/");
			exit("密码已重置!");
		} else {
			exit("重置密码失败!");
		}
	} else {
		echo "验证码错误!";
	}
}
