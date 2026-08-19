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
$em = addslashes(htmlspecialchars($_POST["em"]));
$mm = addslashes(htmlspecialchars($_POST["mm"]));
$yzm = addslashes(htmlspecialchars($_POST["yzm"]));
$fsyzm = addslashes(htmlspecialchars($_POST["fsyzm"]));
if ($fsyzm != "1") {
	if ($zh == "" || $em == "" || $mm == "") {
		exit("参数不完整");
	} else {
		if (strlen($zh) < 5 || strlen($zh) > 32) {
			exit("账号参数格式不正确");
		} else {
			if (strlen($mm) < 3 || strlen($mm) > 16) {
				exit("密码参数格式不正确");
			} else {
				$checkmail = "/\\w+([-+.']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/";
				if (preg_match($checkmail, $em)) {
				} else {
					exit("电子邮箱格式不正确");
				}
			}
		}
	}
}
$allkey = addslashes(htmlspecialchars($_POST["allkey"]));
if ($allkey == "") {
	exit("请传入秘钥");
}
session_start();
$cloudallkey = $_SESSION["allkey"];
$appintkey = md5(md5($_SERVER["HTTP_HOST"] . "fC4gT5uU2pW7kU8eL8dI4nK5xE9uT6iW"));
if ($allkey != $cloudallkey) {
	if ($allkey == $appintkey) {
	} else {
		exit("秘钥错误");
	}
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$sql = "select * from admin where regqx='-1'";
$result = $conn->query($sql);
$row = mysqli_fetch_array($result);
if ($row) {
	exit("管理员未开启用户注册!");
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$wzname = $row["name"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
	$glyusername = $row["username"];
}
$sql = "select * from admin where regverify='1'";
$result = $conn->query($sql);
$row = mysqli_fetch_array($result);
if ($row) {
	session_start();
	if ($fsyzm == "1") {
		if ($emydz == "" || $emssl == "" || $emduk == "" || $emkey == "" || $emzh == "" || $emfs == "" || $emfszm == "") {
			exit("请管理员先配置邮箱信息");
		}
		if (isset($_SESSION["regyz"])) {
			$regyzTime = $_SESSION["regyz_time"];
			$currentTime = time();
			if ($currentTime - $regyzTime > 60) {
				$randomCode = generateRandomCode(6);
				$_SESSION["regyz"] = $randomCode;
				$_SESSION["regyz_time"] = $currentTime;
				$mailapfs = "nopost";
				$xiangym = "ok";
				$mailtitle = "[" . $wzname . "]" . "账号注册验证码";
				$title = "您正在网站：【" . $wzname . "】申请账号注册，验证码60秒内有效，您的验证码是：";
				$text = $randomCode;
				$mailbox = $em;
				include "./sendmail.php";
			} else {
				$secondsToWait = 60 - ($currentTime - $regyzTime);
				exit("请等待" . $secondsToWait . "秒后再试");
			}
		} else {
			$randomCode = generateRandomCode(6);
			$_SESSION["regyz"] = $randomCode;
			$_SESSION["regyz_time"] = time();
			$mailapfs = "nopost";
			$xiangym = "ok";
			$mailtitle = "[" . $wzname . "]" . "账号注册验证码";
			$title = "您正在网站：【" . $wzname . "】申请账号注册，验证码60秒内有效，您的验证码是：";
			$text = $randomCode;
			$mailbox = $em;
			include "./sendmail.php";
		}
		exit;
	}
	if ($yzm == "") {
		exit("请输入验证码");
	}
	if (!isset($_SESSION["regyz"])) {
		exit("请先发送验证码");
	} else {
		$currentTime = time();
		$regyzTime = $_SESSION["regyz_time"];
		$timeDifference = $currentTime - $regyzTime;
		if ($timeDifference > 60) {
			unset($_SESSION["regyz"]);
			unset($_SESSION["regyz_time"]);
			exit("请先发送验证码");
		}
	}
	if ($yzm != $_SESSION["regyz"]) {
		exit("验证码错误");
	}
}
if (strpos($zh, "vis#-[") !== false || strpos($zh, "]-#vis") !== false) {
	exit("该字符禁止注册!");
}
$sql = "select * from user where email = '{$em}'";
$result = $conn->query($sql);
if (mysqli_num_rows($result) > 0) {
	exit("该邮箱已被注册!");
}
$sql = "select * from user where username = '{$zh}'";
$result = $conn->query($sql);
if (mysqli_num_rows($result) > 0) {
	exit("账号已存在!");
}
$lx = 7;
$num = 64;
$gs = 1;
if ($lx == 1) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 2) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 3) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
} elseif ($lx == 4) {
	$strPol = "abcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 5) {
	$strPol = "0123456789";
} elseif ($lx == 6) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
} elseif ($lx == 7) {
	$strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
} else {
	echo "{\"201\":\"错误的类型\"}";
	exit;
}
for ($x = 1; $x <= $gs; $x++) {
	$max = strlen($strPol) - 1;
	for ($i = 0; $i < $num; $i++) {
		$str .= $strPol[rand(0, $max)];
	}
	$str = $str;
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
$bdm = "user";
$yhmia = md5($mm);
$mrmzs = "用户" . $zh;
$sql = "INSERT INTO {$bdm} (username,password,email,name,img,url,homeimg,sign,essqx,esseam,regtime,regip,logtime,logip,ban,bantime,passid)
VALUES ('{$zh}','{$yhmia}','{$em}','{$mrmzs}','./assets/img/tx.png','','-1','这里什么都没有哦!','1','1','{$sj}','{$ip}','{$sj}','{$ip}','0','false','{$str}')";
if ($conn->query($sql) === true) {
	echo "账号注册成功!";
	unset($_SESSION["regyz"]);
	unset($_SESSION["regyz_time"]);
	$allkey = generateRandomString($length);
	$_SESSION["allkey"] = $allkey;
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
function generateRandomString($length)
{
	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$randomString = "";
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}
function generateRandomCode($length)
{
	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$code = "";
	$isAlphaNumeric = false;
	for ($i = 0; $i < $length; $i++) {
		if ($isAlphaNumeric) {
			$code .= $characters[rand(0, 25)];
			$isAlphaNumeric = false;
		} else {
			$code .= $characters[rand(26, 61)];
			$isAlphaNumeric = true;
		}
	}
	return $code;
}
