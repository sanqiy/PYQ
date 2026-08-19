<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
	exit("<script language=\"JavaScript\">;alert(\"请先登录!\");location.href=\"../index.php\";</script>");
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
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$glyzhuser = $row["username"];
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
$zid = $data2_row["id"];
$zjpassword = $data2_row["password"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"../index.php\";</script>");
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$str = "../user/";
$dir = iconv("UTF-8", "GBK", strval($str));
if (!file_exists($dir)) {
	mkdir($dir, 511, true);
}
$str = "../user/headimg/";
$dir = iconv("UTF-8", "GBK", strval($str));
if (!file_exists($dir)) {
	mkdir($dir, 511, true);
}
$file = $_FILES["file"];
$allowedExts = ["gif", "jpeg", "jpg", "png", "webp"];
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if ($extension == "") {
	exit("<script language=\"JavaScript\">;alert(\"未选择图片!\");location.href=\"../setup.php\";</script>;");
}
if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" || $file["type"] == "image/webp" && in_array($extension, $allowedExts)) {
} else {
	exit("<script language=\"JavaScript\">;alert(\"文件类型错误,请上传图片!\");location.href=\"../setup.php\";</script>;");
}
if ($file["size"] > 5242880) {
	exit("<script language=\"JavaScript\">;alert(\"请上传5MB以内的图片!\");location.href=\"../setup.php\";</script>;");
}
$path = "../user/headimg/";
$file_name = mt_rand() . str_replace(".", "", microtime(true)) . substr(md5($zjzhq), 0, 12) . $file["name"];
$file_path = $path . $file_name;
$userimg = "./user/headimg/" . $file_name;
if (move_uploaded_file($file["tmp_name"], $file_path)) {
	$sql = "UPDATE user SET img='{$userimg}' WHERE id='{$zid}'";
	$result = $conn->query($sql);
	if ($result) {
		$sqlo = "select * from essay where ptpuser= '{$user_zh}'";
		$resulto = mysqli_query($conn, $sqlo);
		if (mysqli_num_rows($resulto) > 0) {
			while ($rowo = mysqli_fetch_assoc($resulto)) {
				$txid = $rowo["id"];
				$sql = "UPDATE essay SET ptpimg='{$userimg}' WHERE id='{$txid}'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
		$sqlol = "select * from lcke where luser= '{$user_zh}'";
		$resultol = mysqli_query($conn, $sqlol);
		if (mysqli_num_rows($resultol) > 0) {
			while ($rowo = mysqli_fetch_assoc($resultol)) {
				$ncid = $rowo["id"];
				$sql = "UPDATE lcke SET limg='{$userimg}' WHERE id='{$ncid}'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
		$sqlol = "select * from comm where couser= '{$user_zh}'";
		$resultol = mysqli_query($conn, $sqlol);
		if (mysqli_num_rows($resultol) > 0) {
			while ($rowo = mysqli_fetch_assoc($resultol)) {
				$ncid = $rowo["id"];
				$sql = "UPDATE comm SET coimg='{$userimg}' WHERE id='{$ncid}'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
		$sqlol = "select * from message where fuser= '{$user_zh}'";
		$resultol = mysqli_query($conn, $sqlol);
		if (mysqli_num_rows($resultol) > 0) {
			while ($rowo = mysqli_fetch_assoc($resultol)) {
				$ncid = $rowo["id"];
				$sql = "UPDATE message SET fimg='{$userimg}' WHERE id='{$ncid}'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
	}
	?><script language="JavaScript">;alert("上传成功！");location.href="../setup.php";</script>;<?php
} else {
	?><script language="JavaScript">;alert("上传失败！");location.href="../setup.php";</script>;<?php
}
$conn->close();
