<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$iteace = "adminapi";
include "../../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$glyzhuser = $row["username"];
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
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
$usernamezh = addslashes(htmlspecialchars($_POST["username"]));
$passwordmm = addslashes(htmlspecialchars($_POST["password"]));
if ($usernamezh == "" || $passwordmm == "") {
	exit("<script language=\"JavaScript\">;alert(\"参数不完整!\");location.href=\"../login.php\";</script>;");
} else {
	if (strlen($usernamezh) < 5 || strlen($usernamezh) > 32) {
		exit("<script language=\"JavaScript\">;alert(\"账号参数格式不正确!\");location.href=\"../login.php\";</script>;");
	} else {
		if (strlen($passwordmm) < 3 || strlen($passwordmm) > 16) {
			exit("<script language=\"JavaScript\">;alert(\"密码参数格式不正确!\");location.href=\"../login.php\";</script>;");
		}
	}
}
if (ctype_space($usernamezh) || ctype_space($passwordmm)) {
	exit("<script language=\"JavaScript\">;alert(\"参数不合法!\");location.href=\"../login.php\";</script>;");
}
$passwordmm = md5($passwordmm);
if ($usernamezh != $glyzhuser) {
	exit("<script language=\"JavaScript\">;alert(\"管理员账号错误!\");location.href=\"../login.php\";</script>;");
}
$sql = "SELECT * FROM user WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usernamezh, $passwordmm);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
if ($row) {
	$username = $row["username"];
	$email = $row["email"];
	$name = $row["name"];
	$img = $row["img"];
	$url = $row["url"];
	$passid = $row["passid"];
	$zzid = $row["id"];
	$bdm = "user";
	$sql = "UPDATE {$bdm} SET logtime='{$sj}',logip='{$ip}' WHERE id='{$zzid}'";
	$result = $conn->query($sql);
	if ($result) {
	} else {
		echo mysql_errno();
		exit("<script language=\"JavaScript\">;alert(\"时间错误!\");location.href=\"../login.php\";</script>;");
	}
	setcookie("username", $username, time() + 604800, "/");
	setcookie("passid", $passid, time() + 604800, "/");
	header("Location: ../index.php");
} else {
	exit("<script language=\"JavaScript\">;alert(\"密码错误!\");location.href=\"../login.php\";</script>;");
}
$conn->close();
