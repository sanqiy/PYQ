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
	exit("请先登录!");
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
	exit("账号信息异常,请重新登录!");
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$str = "../user/";
$dir = iconv("UTF-8", "GBK", strval($str));
if (!file_exists($dir)) {
	mkdir($dir, 511, true);
}
$str = "../user/cover/";
$dir = iconv("UTF-8", "GBK", strval($str));
if (!file_exists($dir)) {
	mkdir($dir, 511, true);
}
$file = $_FILES["file"];
$allowedExts = ["gif", "jpeg", "jpg", "png", "webp"];
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if ($extension == "") {
	exit("未选择图片!");
}
if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" && in_array($extension, $allowedExts)) {
} else {
	exit("文件类型错误,请上传图片!");
}
if ($file["size"] > 5242880) {
	exit("请上传5MB以内的图片!");
}
$sql = "select * from user where username= '{$zjzhq}'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_assoc($result);
	$bdhomebjt = "." . $row["homeimg"];
	if (strstr($bdhomebjt, "/user/cover")) {
		if (file_exists($bdhomebjt)) {
			unlink($bdhomebjt);
		}
	}
}
$path = "../user/cover/";
$file_name = mt_rand() . str_replace(".", "", microtime(true)) . substr(md5($zjzhq), 0, 12) . $file["name"];
$file_path = $path . $file_name;
$userimg = "./user/cover/" . $file_name;
if (move_uploaded_file($file["tmp_name"], $file_path)) {
	$sql = "UPDATE user SET homeimg='{$userimg}' WHERE username='{$zjzhq}'";
	$result = $conn->query($sql);
	if ($result) {
		exit("修改封面成功");
	}
} else {
	exit("修改封面失败");
}
$conn->close();
