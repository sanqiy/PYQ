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
$ztys = addslashes(htmlspecialchars($_POST["ztys"]));
$ztwid = addslashes(htmlspecialchars($_POST["ztwid"]));
if ($ztys == "" || $ztwid == "") {
	exit("参数不完整");
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接数据库失败");
}
$user_zhsg = addslashes(htmlspecialchars($user_zh));
$sqls = "select * from user where username='{$user_zhsg}'";
$data_results = mysqli_query($conn, $sqls);
$data2s_row = mysqli_fetch_array($data_results);
$user_zh = $data2s_row["username"];
$user_name = $data2s_row["name"];
$user_img = $data2s_row["img"];
$user_url = $data2s_row["url"];
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
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$glyadmin = $row["username"];
}
if ($zjzhq == $glyadmin) {
} else {
	$sql = "select * from essay where cid= '{$ztys}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$ptpuser = $row["ptpuser"];
	} else {
		exit("未获取到数据!");
	}
	if ($zjzhq == $ptpuser) {
	} else {
		exit("您无权操作其他用户的文章!");
	}
}
if ($ztwid == 0) {
	$data_result = mysqli_query($conn, "select * from essay where cid='{$ztys}'");
	$data_row = mysqli_fetch_array($data_result);
	$zid = $data_row["id"];
	$sql = "UPDATE essay SET ptpys='1' WHERE id='{$zid}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "已解除私密";
	} else {
		echo "解除私密失败";
	}
} elseif ($ztwid == 1) {
	$data_result = mysqli_query($conn, "select * from essay where cid='{$ztys}'");
	$data_row = mysqli_fetch_array($data_result);
	$zid = $data_row["id"];
	$sql = "UPDATE essay SET ptpys='0' WHERE id='{$zid}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "已设置私密";
	} else {
		echo "设置私密失败";
	}
}
$conn->close();
