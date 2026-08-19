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
$plid = addslashes(htmlspecialchars($_POST["plid"]));
if ($plid == "") {
	exit("参数不完整!");
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
	exit("账号信息异常,请重新登录!");
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$glyadmin = $row["username"];
}
if ($zjzhq == $glyadmin) {
	$sql = "delete from comm where ecid='{$plid}'";
	$result = $conn->query($sql);
	if ($result) {
		exit("删除评论成功!");
	} else {
		exit("删除评论失败!");
	}
} else {
	$sql = "select * from comm where ecid= '{$plid}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$sswid = $row["wzcid"];
	} else {
		exit("该评论不存在!");
	}
	$sql = "select * from essay where cid= '{$sswid}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$wsuzh = $row["ptpuser"];
	} else {
		exit("对应文章不存在!");
	}
	if ($zjzhq == $wsuzh) {
		$sql = "delete from comm where ecid='{$plid}'";
		$result = $conn->query($sql);
		if ($result) {
			exit("删除评论成功!");
		} else {
			exit("删除评论失败!");
		}
	} else {
		exit("您无权删除其他用户的文章评论!");
	}
}
