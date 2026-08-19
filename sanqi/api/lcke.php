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
	$tieid = addslashes(htmlspecialchars($_POST["tieid"]));
	$zts = addslashes(htmlspecialchars($_POST["zts"]));
	if ($tieid == "" || $zts == "") {
		$arr = [["code" => "201", "msg" => "参数不完整!"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	include "../config.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		$arr = [["code" => "201", "msg" => "连接数据库失败"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	session_start();
	$randomString = addslashes(htmlspecialchars($_SESSION["visykmz_userzh"]));
	$randomStringip = addslashes(htmlspecialchars($_SESSION["visykmz_userip"]));
	if ($randomString == "" || $randomStringip == "") {
		function generateRandomString($length = 5)
		{
			$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$randomString = "";
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}
		$randomString2 = "游客" . generateRandomString(5);
		$user_zh = "vis#-[" . $ip . "]-#vis";
		$user_name = $randomString2;
		$user_img = "./assets/img/tx.png";
	} else {
		$randomString2 = $randomString;
		$randomString3 = $randomStringip;
		$user_zh = $randomString3;
		$user_name = $randomString2;
		$user_img = "./assets/img/tx.png";
	}
	if ($zts == 0) {
		$sql = "select * from lcke where luser= '{$user_zh}' and lwz='{$tieid}'";
		$result = $conn->query($sql);
		$row = mysqli_fetch_array($result);
		if ($row) {
			$arr = [["code" => "201", "msg" => "您已经点过赞了"]];
			echo json_encode($arr, JSON_UNESCAPED_UNICODE);
			exit;
		}
		$bdm = "lcke";
		$sql = "INSERT INTO {$bdm} (luser,lname,limg,lwz,ltime)
        VALUES ('{$user_zh}','{$user_name}','{$user_img}','{$tieid}','{$sj}')";
		if ($conn->query($sql) === true) {
			$data_result = mysqli_query($conn, "select * from essay where cid='{$tieid}'");
			$data_row = mysqli_fetch_array($data_result);
			$wzfszh = $data_row["ptpuser"];
			$data_resultl = mysqli_query($conn, "select * from user where username='{$wzfszh}'");
			$data_rowl = mysqli_fetch_array($data_resultl);
			$wzfszh = $data_rowl["username"];
			$wzfsnc = $data_rowl["name"];
			$wzfstx = $data_rowl["img"];
			$wzfseam = $data_rowl["img"];
			$bdm2 = "message";
			$btmnr = $user_name . "给你点赞啦!";
			$sql2 = "INSERT INTO {$bdm2} (fuser,fimg,fname,suser,title,text,ftime,msg)
               VALUES ('{$user_zh}','{$user_img}','{$user_name}','{$wzfszh}','新的点赞','{$btmnr}','{$sj}','0')";
			if ($conn->query($sql2) === true) {
			} else {
				echo "Error: " . $sql2 . "<br>" . $conn->error;
			}
			$sqly = "select * from lcke where lwz='{$tieid}'";
			$resulty = $conn->query($sqly);
			$dwys = 0;
			while ($rowy = $resulty->fetch_assoc()) {
				$ykdzm = $rowy["luser"];
				if (strpos($ykdzm, "vis#-[") !== false || strpos($ykdzm, "]-#vis") !== false) {
					$dwys++;
				}
			}
			$arr = [["code" => "200", "msg" => $dwys . "位访客", "img" => $user_img]];
			echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	} elseif ($zts == -1) {
		$arr = [["code" => "201", "msg" => "游客暂不支持取消点赞哦"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	}
	exit;
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$user_zhsg = addslashes(htmlspecialchars($user_zh));
$sqls = "select * from user where username='{$user_zhsg}'";
$data_results = mysqli_query($conn, $sqls);
$data2s_row = mysqli_fetch_array($data_results);
$user_zh = $data2s_row["username"];
$user_name = $data2s_row["name"];
$user_img = $data2s_row["img"];
$user_url = $data2s_row["url"];
$user_id = $user_zh;
$user_name = $user_name;
$user_img = $user_img;
$tieid = addslashes(htmlspecialchars($_POST["tieid"]));
$zts = addslashes(htmlspecialchars($_POST["zts"]));
if ($user_id == "" || $user_name == "" || $user_img == "" || $tieid == "" || $zts == "") {
	$arr = [["code" => "201", "msg" => "参数不完整!"]];
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	exit;
}
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
$ban = $data2_row["ban"];
$bantime = $data2_row["bantime"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	$arr = [["code" => "201", "msg" => "账号信息异常,请重新登录!"]];
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	exit;
}
if ($ban != "0" && $bantime != "false") {
	if ($bantime === "true") {
		setcookie("username", "", time() + -1, "/");
		setcookie("passid", "", time() + -1, "/");
		$arr = [["code" => "201", "msg" => "该账号涉嫌违规,已被永久封禁!"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		exit;
	} else {
		setcookie("username", "", time() + -1, "/");
		setcookie("passid", "", time() + -1, "/");
		$arr = [["code" => "201", "msg" => "你的账号已被封禁！解封时间为:" . $bantime]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		exit;
	}
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
if ($zts == 0) {
	$sql = "select * from lcke where luser= '{$zjzhq}' and lwz='{$tieid}'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_array($result);
	if ($row) {
		$arr = [["code" => "201", "msg" => "您已经点过赞了"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		exit;
	}
	$data_result = mysqli_query($conn, "select * from essay where cid='{$tieid}'");
	$data_row = mysqli_fetch_array($data_result);
	$wzfszh = $data_row["ptpuser"];
	$data_resultl = mysqli_query($conn, "select * from user where username='{$wzfszh}'");
	$data_rowl = mysqli_fetch_array($data_resultl);
	$wzfszh = $data_rowl["username"];
	$wzfsnc = $data_rowl["name"];
	$wzfstx = $data_rowl["img"];
	$wzfseam = $data_rowl["img"];
	$bdm = "lcke";
	$sql = "INSERT INTO {$bdm} (luser,lname,limg,lwz,ltime)
    VALUES ('{$zjzhq}','{$zjnc}','{$zjimg}','{$tieid}','{$sj}')";
	if ($conn->query($sql) === true) {
		$arr = [["code" => "200", "msg" => $zjnc, "img" => $zjimg]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	if ($wzfszh != $user_zh) {
		$bdm2 = "message";
		$btmnr = $zjnc . "给你点赞啦!";
		$sql2 = "INSERT INTO {$bdm2} (fuser,fimg,fname,suser,title,text,ftime,msg)
VALUES ('{$zjzhq}','{$zjimg}','{$zjnc}','{$wzfszh}','新的点赞','{$btmnr}','{$sj}','0')";
		if ($conn->query($sql2) === true) {
		} else {
			echo "Error: " . $sql2 . "<br>" . $conn->error;
		}
	}
} elseif ($zts == -1) {
	$sql = "select * from lcke where luser= '{$zjzhq}' and lwz='{$tieid}'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_array($result);
	if ($row) {
		$wida = $row["id"];
		$sql = "delete from lcke where id='{$wida}'";
		$result = $conn->query($sql);
		if ($result) {
			$arr = [["code" => "200", "msg" => $zjnc]];
			echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		} else {
			echo mysql_errno();
		}
	}
}
$conn->close();
