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
if ($lx == "wzpz") {
	$wzbt = addslashes(htmlspecialchars($_POST["wzbt"]));
	$wzms = addslashes(htmlspecialchars($_POST["wzms"]));
	$wzglyzh = addslashes(htmlspecialchars($_POST["wzglyzh"]));
	$wzqm = addslashes(htmlspecialchars($_POST["wzqm"]));
	$wzmrxw = addslashes(htmlspecialchars($_POST["wzmrxw"]));
	$wzmrxp = addslashes(htmlspecialchars($_POST["wzmrxp"]));
	$wzba = addslashes(htmlspecialchars($_POST["wzba"]));
	$wzzit = addslashes(htmlspecialchars($_POST["wzzit"]));
	$wzcopy = addslashes(htmlspecialchars($_POST["wzcopy"]));
	$pagepass = addslashes(htmlspecialchars($_POST["pagepass"]));
	$wzmask = addslashes(htmlspecialchars($_POST["mask"]));
	$wzmusic = addslashes(htmlspecialchars($_POST["wzmusic"]));
	$wzmusplay = addslashes(htmlspecialchars($_POST["wzmusplay"]));
	$regverify = addslashes(htmlspecialchars($_POST["regverify"]));
	if ($wzbt != "") {
		$sql = "UPDATE admin SET name='{$wzbt}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($wzms != "") {
		$sql = "UPDATE admin SET subtitle='{$wzms}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($wzglyzh != "") {
		$sql = "select * from user where username = '{$wzglyzh}'";
		$result = $conn->query($sql);
		if (mysqli_num_rows($result) > 0) {
			if (strlen($wzglyzh) < 5 || strlen($wzglyzh) > 32) {
			} else {
				$sql = "UPDATE admin SET username='{$wzglyzh}' WHERE id='1'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
	}
	if ($wzqm != "") {
		$sql = "UPDATE admin SET sign='{$wzqm}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($wzmrxw != "" && $wzmrxw != 0) {
		if (strlen($wzmrxw) >= 3) {
		} else {
			$sql = "UPDATE admin SET essgs='{$wzmrxw}' WHERE id='1'";
			$result = $conn->query($sql);
			if ($result) {
			}
		}
	}
	if ($wzmrxp != "" && $wzmrxp != 0) {
		if (strlen($wzmrxp) >= 3) {
		} else {
			$sql = "UPDATE admin SET commgs='{$wzmrxp}' WHERE id='1'";
			$result = $conn->query($sql);
			if ($result) {
			}
		}
	}
	$sql = "UPDATE admin SET beian='{$wzba}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	$sql = "UPDATE admin SET scfont='{$wzzit}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	$sql = "UPDATE admin SET copyright='{$wzcopy}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	$sql = "UPDATE admin SET pagepass='{$pagepass}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	if (strpos($wzmask, "||") !== false) {
		$data = explode("||", $wzmask);
		$jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		examine("filtertext", $jsonData, $conn, "ok");
	} else {
		examine("filtertext", $jsonData, $conn, "ok");
	}
	if ($wzmusic == "") {
		$wzmusic = "-1";
	}
	$sql = "UPDATE admin SET music='{$wzmusic}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	if ($wzmusplay == "") {
		$wzmusplay = "1";
	}
	$sql = "UPDATE admin SET musplay='{$wzmusplay}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	if ($regverify == "") {
		$regverify = "0";
	}
	$sql = "UPDATE admin SET regverify='{$regverify}' WHERE id='1'";
	$result = $conn->query($sql);
	if ($result) {
	}
	$cucss = addslashes(htmlspecialchars($_POST["cucss"]));
	examine("cucss", $cucss, $conn, "ok");
	$cujs = addslashes(htmlspecialchars($_POST["cujs"]));
	examine("cujs", $cujs, $conn, "ok");
	exit("<script language=\"JavaScript\">;location.href=\"../basic.php\";</script>");
}
if ($lx == "wzqxp") {
	if (addslashes(htmlspecialchars($_POST["develop_zt"])) == 0) {
		$sql = "UPDATE admin SET zt='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_zt"])) == 1) {
		$sql = "UPDATE admin SET zt='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_reg"])) == 0) {
		$sql = "UPDATE admin SET regqx='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_reg"])) == -1) {
		$sql = "UPDATE admin SET regqx='-1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_qlogin"])) == 0) {
		$sql = "UPDATE admin SET loginkg='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_qlogin"])) == 1) {
		$sql = "UPDATE admin SET loginkg='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_lnk"])) == 0) {
		$sql = "UPDATE admin SET lnkzt='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_lnk"])) == 1) {
		$sql = "UPDATE admin SET lnkzt='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_fowz"])) == 0) {
		$sql = "UPDATE admin SET kqsy='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_fowz"])) == 1) {
		$sql = "UPDATE admin SET kqsy='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_qfowz"])) == 0) {
		$sql = "UPDATE admin SET ptpfan='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_qfowz"])) == 1) {
		$sql = "UPDATE admin SET ptpfan='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_nmfowz"])) == 0) {
		$sql = "UPDATE admin SET notname='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_nmfowz"])) == 1) {
		$sql = "UPDATE admin SET notname='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_imgys"])) == 0) {
		$sql = "UPDATE admin SET imgpres='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_imgys"])) == 1) {
		$sql = "UPDATE admin SET imgpres='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_vispl"])) == 0) {
		$sql = "UPDATE admin SET viscomm='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_vispl"])) == -1) {
		$sql = "UPDATE admin SET viscomm='-1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_plsh"])) == 0) {
		$sql = "UPDATE admin SET comaud='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_plsh"])) == 1) {
		$sql = "UPDATE admin SET comaud='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_wzsh"])) == 0) {
		$sql = "UPDATE admin SET ptpaud='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_wzsh"])) == 1) {
		$sql = "UPDATE admin SET ptpaud='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_kyurl"])) == 0) {
		$sql = "UPDATE admin SET rosdomain='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_kyurl"])) == 1) {
		$sql = "UPDATE admin SET rosdomain='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_ryms"])) == 0) {
		$sql = "UPDATE admin SET daymode='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_ryms"])) == 1) {
		$sql = "UPDATE admin SET daymode='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_gotop"])) == 0) {
		$sql = "UPDATE admin SET gotop='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_gotop"])) == 1) {
		$sql = "UPDATE admin SET gotop='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_so"])) == 0) {
		$sql = "UPDATE admin SET search='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_so"])) == 1) {
		$sql = "UPDATE admin SET search='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	if (addslashes(htmlspecialchars($_POST["develop_videoplay"])) == 0) {
		$sql = "UPDATE admin SET videoauplay='0' WHERE id='1'";
		$result = $conn->query($sql);
	} elseif (addslashes(htmlspecialchars($_POST["develop_videoplay"])) == 1) {
		$sql = "UPDATE admin SET videoauplay='1' WHERE id='1'";
		$result = $conn->query($sql);
	}
	$piccir = addslashes(htmlspecialchars($_POST["develop_piccir"]));
	examine("piccir", $piccir, $conn, "no");
	exit("<script language=\"JavaScript\">;location.href=\"../authority.php\";</script>");
}
if ($lx == "upyun") {
	$upfm = addslashes(htmlspecialchars($_POST["upfm"]));
	$upcz = addslashes(htmlspecialchars($_POST["upcz"]));
	$upcm = addslashes(htmlspecialchars($_POST["upcm"]));
	$upby = addslashes(htmlspecialchars($_POST["upby"]));
	$data = ["bucketName" => $upfm, "operatorName" => $upcz, "operatorPassword" => $upcm, "operatorurl" => $upby];
	$jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	examine("upyun", $jsonData, $conn, "ok");
	exit("<script language=\"JavaScript\">;location.href=\"../imgset.php\";</script>;");
}
if ($lx == "icon") {
	$iconurl = addslashes(htmlspecialchars($_POST["iconurl"]));
	if ($iconurl != "") {
		$sql = "select * from admin where id= '1'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$bdhomebjt = "../." . $row["icon"];
			if (strstr($bdhomebjt, "/user/pubces")) {
				if (file_exists($bdhomebjt)) {
					unlink($bdhomebjt);
				}
			}
		}
		$sql = "UPDATE admin SET icon='{$iconurl}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			exit("<script language=\"JavaScript\">;location.href=\"../imgset.php\";</script>;");
		} else {
			exit("<script language=\"JavaScript\">;alert(\"Favicon图标更新失败\");location.href=\"../imgset.php\";</script>;");
		}
	}
	$str = "../../user/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$str = "../../user/pubces/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$file = $_FILES["file"];
	$allowedExts = ["gif", "jpeg", "jpg", "png"];
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if ($extension == "") {
		exit("<script language=\"JavaScript\">;alert(\"未选择图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" && in_array($extension, $allowedExts)) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"文件类型错误,请上传图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["size"] > 5242880) {
		exit("<script language=\"JavaScript\">;alert(\"请上传5MB以内的图片!\");location.href=\"../imgset.php\";</script>;");
	}
	$sql = "select * from admin where id= '1'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$bdhomebjt = "../." . $row["icon"];
		if (strstr($bdhomebjt, "/user/pubces")) {
			if (file_exists($bdhomebjt)) {
				unlink($bdhomebjt);
			}
		}
	}
	$path = "../../user/pubces/";
	$file_name = hexdec(uniqid()) . rand(0, 99999) . md5($zjzhq) . date("YmdHis") . $file["name"];
	$file_path = $path . $file_name;
	$userimg = "./user/pubces/" . $file_name;
	if (move_uploaded_file($file["tmp_name"], $file_path)) {
		$sql = "UPDATE admin SET icon='{$userimg}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			?><script language="JavaScript">;location.href="../imgset.php";</script>;<?php
		}
	} else {
		?><script language="JavaScript">;alert("Favicon图标更新失败");location.href="../imgset.php";</script>;<?php
	}
}
if ($lx == "logo") {
	$logourl = addslashes(htmlspecialchars($_POST["logourl"]));
	if ($logourl != "") {
		$sql = "select * from admin where id= '1'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$bdhomebjt = "../." . $row["logo"];
			if (strstr($bdhomebjt, "/user/pubces")) {
				if (file_exists($bdhomebjt)) {
					unlink($bdhomebjt);
				}
			}
		}
		$sql = "UPDATE admin SET logo='{$logourl}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			exit("<script language=\"JavaScript\">;location.href=\"../imgset.php\";</script>;");
		} else {
			exit("<script language=\"JavaScript\">;alert(\"Logo图标更新失败\");location.href=\"../imgset.php\";</script>;");
		}
	}
	$str = "../../user/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$str = "../../user/pubces/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$file = $_FILES["file"];
	$allowedExts = ["gif", "jpeg", "jpg", "png"];
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if ($extension == "") {
		exit("<script language=\"JavaScript\">;alert(\"未选择图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" && in_array($extension, $allowedExts)) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"文件类型错误,请上传图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["size"] > 5242880) {
		exit("<script language=\"JavaScript\">;alert(\"请上传5MB以内的图片!\");location.href=\"../imgset.php\";</script>;");
	}
	$sql = "select * from admin where id= '1'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$bdhomebjt = "../." . $row["logo"];
		if (strstr($bdhomebjt, "/user/pubces")) {
			if (file_exists($bdhomebjt)) {
				unlink($bdhomebjt);
			}
		}
	}
	$path = "../../user/pubces/";
	$file_name = hexdec(uniqid()) . rand(0, 99999) . md5($zjzhq) . date("YmdHis") . $file["name"];
	$file_path = $path . $file_name;
	$userimg = "./user/pubces/" . $file_name;
	if (move_uploaded_file($file["tmp_name"], $file_path)) {
		$sql = "UPDATE admin SET logo='{$userimg}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			?><script language="JavaScript">;location.href="../imgset.php";</script>;<?php
		}
	} else {
		?><script language="JavaScript">;alert("Logo图标更新失败");location.href="../imgset.php";</script>;<?php
	}
}
if ($lx == "coverimg") {
	$coverimgurl = addslashes(htmlspecialchars($_POST["coverimgurl"]));
	if ($coverimgurl != "") {
		$sql = "select * from admin where id= '1'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$bdhomebjt = "../." . $row["homimg"];
			if (strstr($bdhomebjt, "/user/pubces")) {
				if (file_exists($bdhomebjt)) {
					unlink($bdhomebjt);
				}
			}
		}
		$sql = "UPDATE admin SET homimg='{$coverimgurl}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			exit("<script language=\"JavaScript\">;location.href=\"../imgset.php\";</script>;");
		} else {
			exit("<script language=\"JavaScript\">;alert(\"相册封面更新失败\");location.href=\"../imgset.php\";</script>;");
		}
	}
	$str = "../../user/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$str = "../../user/pubces/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$file = $_FILES["file"];
	$allowedExts = ["gif", "jpeg", "jpg", "png"];
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if ($extension == "") {
		exit("<script language=\"JavaScript\">;alert(\"未选择图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" && in_array($extension, $allowedExts)) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"文件类型错误,请上传图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["size"] > 5242880) {
		exit("<script language=\"JavaScript\">;alert(\"请上传5MB以内的图片!\");location.href=\"../imgset.php\";</script>;");
	}
	$sql = "select * from admin where id= '1'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$bdhomebjt = "../." . $row["homimg"];
		if (strstr($bdhomebjt, "/user/pubces")) {
			if (file_exists($bdhomebjt)) {
				unlink($bdhomebjt);
			}
		}
	}
	$path = "../../user/pubces/";
	$file_name = hexdec(uniqid()) . rand(0, 99999) . md5($zjzhq) . date("YmdHis") . $file["name"];
	$file_path = $path . $file_name;
	$userimg = "./user/pubces/" . $file_name;
	if (move_uploaded_file($file["tmp_name"], $file_path)) {
		$sql = "UPDATE admin SET homimg='{$userimg}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
			?><script language="JavaScript">;location.href="../imgset.php";</script>;<?php
		}
	} else {
		?><script language="JavaScript">;alert("相册封面更新失败");location.href="../imgset.php";</script>;<?php
	}
}
if ($lx == "bobgsc") {
	$str = "../../user/bobg/";
	$dir = iconv("UTF-8", "GBK", strval($str));
	if (!file_exists($dir)) {
		mkdir($dir, 511, true);
	}
	$file = $_FILES["file"];
	$allowedExts = ["gif", "jpeg", "jpg", "png"];
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if ($extension == "") {
		exit("<script language=\"JavaScript\">;alert(\"未选择图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["type"] == "image/jpeg" || $file["type"] == "image/png" || $file["type"] == "image/jpg" || $file["type"] == "image/gif" && in_array($extension, $allowedExts)) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"文件类型错误,请上传图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if ($file["size"] > 5242880) {
		exit("<script language=\"JavaScript\">;alert(\"请上传5MB以内的图片!\");location.href=\"../imgset.php\";</script>;");
	}
	if (move_uploaded_file($file["tmp_name"], "../../user/bobg/bobg." . $extension)) {
		?><script language="JavaScript">;location.href="../imgset.php";</script>;<?php
	} else {
		?><script language="JavaScript">;alert("网页背景图上传失败");location.href="../imgset.php";</script>;<?php
	}
}
if ($lx == "bobgde") {
	if (file_exists("../../user/bobg/bobg.png")) {
		unlink("../../user/bobg/bobg.png");
		?><script language="JavaScript">;alert("已删除现有网页背景图!");location.href="../imgset.php";</script>;<?php
	} elseif (file_exists("../../user/bobg/bobg.jpg")) {
		unlink("../../user/bobg/bobg.jpg");
		?><script language="JavaScript">;alert("已删除现有网页背景图!");location.href="../imgset.php";</script>;<?php
	} elseif (file_exists("../../user/bobg/bobg.jpeg")) {
		unlink("../../user/bobg/bobg.jpeg");
		?><script language="JavaScript">;alert("已删除现有网页背景图!");location.href="../imgset.php";</script>;<?php
	} elseif (file_exists("../../user/bobg/bobg.gif")) {
		unlink("../../user/bobg/bobg.gif");
		?><script language="JavaScript">;alert("已删除现有网页背景图!");location.href="../imgset.php";</script>;<?php
	} else {
		?><script language="JavaScript">;alert("当前没有网页背景图!");location.href="../imgset.php";</script>;<?php
	}
}
if ($lx == "emailset") {
	$emdz = addslashes(htmlspecialchars($_POST["emdz"]));
	$emssl = addslashes(htmlspecialchars($_POST["emssl"]));
	$emdk = addslashes(htmlspecialchars($_POST["emdk"]));
	$emkey = addslashes(htmlspecialchars($_POST["emkey"]));
	$emfzh = addslashes(htmlspecialchars($_POST["emfzh"]));
	$emfsy = addslashes(htmlspecialchars($_POST["emfsy"]));
	$emfsm = addslashes(htmlspecialchars($_POST["emfsm"]));
	if ($emdz != "") {
		$sql = "UPDATE admin SET emydz='{$emdz}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emssl != "") {
		$sql = "UPDATE admin SET emssl='{$emssl}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emdk != "") {
		$sql = "UPDATE admin SET emduk='{$emdk}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emkey != "") {
		$sql = "UPDATE admin SET emkey='{$emkey}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emfzh != "") {
		$sql = "UPDATE admin SET emzh='{$emfzh}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emfsy != "") {
		$sql = "UPDATE admin SET emfs='{$emfsy}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	if ($emfsm != "") {
		$sql = "UPDATE admin SET emfszm='{$emfsm}' WHERE id='1'";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
	?><script language="JavaScript">;location.href="../emailset.php";</script>;<?php
}
$conn->close();
function examine($title, $text, $conn, $exa)
{
	if ($text == "") {
		if ($exa == "no") {
			return null;
		}
	}
	$sql = "SELECT * FROM configx WHERE title = '{$title}'";
	$result = $conn->query($sql);
	if ($result && mysqli_num_rows($result) > 0) {
		$sql = "UPDATE configx SET text = '{$text}' WHERE title = '{$title}'";
		$result = $conn->query($sql);
		if ($result) {
		}
	} else {
		$sql = "INSERT INTO configx (title, text) VALUES ('{$title}', '{$text}')";
		$result = $conn->query($sql);
		if ($result) {
		}
	}
}
