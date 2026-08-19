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
	exit("请先登录!");
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
$plid = addslashes(htmlspecialchars($_POST["plid"]));
$type = addslashes(htmlspecialchars($_POST["type"]));
if ($plid == "" || $type == "") {
	exit("参数不完整!");
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
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("账号信息异常,请重新登录!");
}
if ($type == 0) {
	$sql = "UPDATE message SET msg='1' WHERE id='{$plid}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "消息已读";
	} else {
		echo mysql_errno();
	}
} elseif ($type == -1) {
	$sql = "UPDATE message SET msg='-1' WHERE id='{$plid}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "消息已删除";
		$sql6 = "delete from message where id='{$plid}'";
		$result6 = $conn->query($sql6);
	} else {
		echo mysql_errno();
	}
} elseif ($type == -2) {
	$sql6 = "delete from message where suser='{$zjzhq}'";
	$result6 = $conn->query($sql6);
	if ($result6) {
		echo "消息已删除";
	} else {
		echo mysql_errno();
	}
} elseif ($type == -3) {
	$sql = "UPDATE message SET msg = '1' WHERE suser = '{$zjzhq}'";
	if ($conn->query($sql) === true) {
		echo "消息已读";
	} else {
		echo $conn->error;
	}
}
$conn->close();
