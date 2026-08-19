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
$lx = addslashes(htmlspecialchars($_POST["lx"]));
if ($lx == "ylgl") {
	$linkid = addslashes(htmlspecialchars($_POST["linkid"]));
	$linmz = addslashes(htmlspecialchars($_POST["linmz"]));
	$linimg = addslashes(htmlspecialchars($_POST["linimg"]));
	$linurl = addslashes(htmlspecialchars($_POST["linurl"]));
	$delyl = addslashes(htmlspecialchars($_POST["delyl"]));
	if ($linkid != -1) {
		if ($linkid != "" && $delyl == -1) {
			$sql = "delete from link where id='{$linkid}'";
			$result = $conn->query($sql);
			exit("<script language=\"JavaScript\">;alert(\"友链已删除\");location.href=\"../linkset.php\";</script>;");
		} else {
			if ($delyl == 0) {
				if ($linmz != "") {
					$sql = "UPDATE link SET urls='{$linmz}' WHERE id='{$linkid}'";
					$result = $conn->query($sql);
				}
				if ($linimg != "") {
					$sql = "UPDATE link SET urlimg='{$linimg}' WHERE id='{$linkid}'";
					$result = $conn->query($sql);
				}
				if ($linurl != "") {
					$sql = "UPDATE link SET url='{$linurl}' WHERE id='{$linkid}'";
					$result = $conn->query($sql);
				}
				exit("<script language=\"JavaScript\">;alert(\"更新友链成功\");location.href=\"../linkset.php\";</script>;");
			}
		}
	} else {
		exit("<script language=\"JavaScript\">;alert(\"未选择友链\");location.href=\"../linkset.php\";</script>;");
	}
}
if ($lx == "ylnew") {
	$linmz = addslashes(htmlspecialchars($_POST["linmz"]));
	$linimg = addslashes(htmlspecialchars($_POST["linimg"]));
	$linurl = addslashes(htmlspecialchars($_POST["linurl"]));
	if ($linmz != "" && $linimg != "" && $linurl != "") {
		$sql = "INSERT INTO link (url,urls,urlimg)
        VALUES ('{$linurl}','{$linmz}','{$linimg}')";
		if ($conn->query($sql) === true) {
			exit("<script language=\"JavaScript\">;alert(\"添加友链成功\");location.href=\"../linkset.php\";</script>;");
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	} else {
		exit("<script language=\"JavaScript\">;alert(\"参数不完整\");location.href=\"../linkset.php\";</script>;");
	}
}
if ($lx == "poln") {
	$likid = addslashes(htmlspecialchars($_POST["likid"]));
	if ($likid != -1 && $likid != "") {
		$data_result = mysqli_query($conn, "select * from link where id='{$likid}'");
		$data_row = mysqli_fetch_array($data_result);
		$arr = [["url" => $data_row["url"], "urlbt" => $data_row["urls"], "urlimg" => $data_row["urlimg"]]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}
}
$conn->close();
