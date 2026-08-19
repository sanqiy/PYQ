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
$wzdid = addslashes(htmlspecialchars($_POST["wzdid"]));
if ($wzdid == "") {
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
	exit("账号信息异常,请重新登录!");
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$glyadmin = $row["username"];
}
if ($zjzhq == $glyadmin) {
} else {
	$sql = "select * from essay where cid= '{$wzdid}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$ptpuser = $row["ptpuser"];
	} else {
		exit("未获取到数据!");
	}
	if ($zjzhq == $ptpuser) {
	} else {
		exit("您无权删除其他用户的文章!");
	}
}
$sql = "SELECT * FROM essay";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$wcid = $row["cid"];
	if ($wcid == $wzdid) {
		$id = $row["id"];
		$ptpimag = $row["ptpimag"];
		$ptpvideo = $row["ptpvideo"];
		$ptpmusic = $row["ptpmusic"];
		$partssp = explode("|", $ptpvideo);
		$ptpvideo = $partssp[0];
		$ptpvideofm = $partssp[1];
		$sqlq = "delete from essay where id='{$id}'";
		$resultq = $conn->query($sqlq);
		if ($resultq) {
			echo "已删除该文章!";
		} else {
			echo mysql_errno();
		}
	}
}
$sql = "SELECT * FROM comm";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$wcid = $row["wzcid"];
	if ($wcid == $wzdid) {
		$id = $row["id"];
		$sqlq = "delete from comm where id='{$id}'";
		$resultq = $conn->query($sqlq);
		if ($resultq) {
		} else {
			echo mysql_errno();
		}
	}
}
$sql = "SELECT * FROM lcke";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$wcid = $row["lwz"];
	if ($wcid == $wzdid) {
		$id = $row["id"];
		$sqlq = "delete from lcke where id='{$id}'";
		$resultq = $conn->query($sqlq);
		if ($resultq) {
		} else {
			echo mysql_errno();
		}
	}
}
if ($ptpimag != "") {
	$imgar = explode("(+@+)", $ptpimag);
	$coun = count($imgar);
	for ($i = 0; $i < $coun; $i++) {
		$tuimg = $imgar[$i];
		if (strpos($tuimg, "/upload/") !== false) {
			$abstu = realpath("." . $tuimg);
			if (!is_file($abstu)) {
			} else {
				unlink($abstu);
			}
		}
	}
}
if ($ptpvideo != "") {
	if (strpos($ptpvideo, "/upload/") !== false) {
		$absolutePath = realpath(".." . $ptpvideo);
		if (!is_file($absolutePath)) {
		} else {
			unlink($absolutePath);
		}
	}
}
$conn->close();
