<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$iteace = "adminapi";
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
	exit("<script language=\"JavaScript\">;alert(\"请先登录!\");location.href=\"../login.php\";</script>");
}
include "../../config.php";
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
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"../login.php\";</script>");
}
if ($zjzhq != $glyzhuser) {
	exit("<script language=\"JavaScript\">;alert(\"没有权限!\");history.go(-1);</script>");
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$ztm = addslashes(htmlspecialchars($_POST["ztm"]));
$cs = addslashes(htmlspecialchars($_POST["cs"]));
if ($ztm == "" || $cs == "") {
	exit("参数不完整!");
}
if ($ztm == "shwz") {
	$sql = "UPDATE essay SET ptpaud='1' WHERE cid='{$cs}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "200";
	} else {
		echo mysql_errno();
	}
}
if ($ztm == "bhwz") {
	$sql = "SELECT ptpimag, ptpvideo, ptpmusic FROM essay WHERE cid = '{$cs}'";
	$result = mysqli_query($conn, $sql);
	if ($result) {
		$row = mysqli_fetch_assoc($result);
		$id = $row["id"];
		$ptpimag = $row["ptpimag"];
		$ptpvideo = $row["ptpvideo"];
		$ptpmusic = $row["ptpmusic"];
	}
	$sql = "delete from essay where cid='{$cs}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "200";
	} else {
		echo mysql_errno();
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
				if (!is_file("../." . $tuimg)) {
				} else {
					unlink("../." . $tuimg);
				}
			}
		}
	}
	if ($ptpvideo != "") {
		if (strpos($ptpvideo, "/upload/") !== false) {
			if (!is_file("../.." . $ptpvideo)) {
			} else {
				unlink("../.." . $ptpvideo);
			}
		}
	}
}
if ($ztm == "shpl") {
	$sql = "UPDATE comm SET comaud='1' WHERE ecid='{$cs}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "200";
	} else {
		echo mysql_errno();
	}
}
if ($ztm == "bhpl") {
	$sql = "delete from comm where ecid='{$cs}'";
	$result = $conn->query($sql);
	if ($result) {
		echo "200";
	} else {
		echo mysql_errno();
	}
}
$conn->close();
