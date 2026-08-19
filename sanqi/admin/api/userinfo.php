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
if (addslashes(htmlspecialchars($_POST["ids"])) != "") {
	$id = addslashes(htmlspecialchars($_POST["userid"]));
	$data = addslashes(htmlspecialchars($_POST["ids"]));
	if ($lx == "0") {
		$ids = explode(",", $data);
		foreach ($ids as $id) {
			$sql = "UPDATE user SET ban = '0', bantime = 'false' WHERE id = '{$id}'";
			$result = $conn->query($sql);
			if ($result) {
			} else {
				$arr = [["code" => "200", "msg" => "更新失败"]];
				exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
			}
		}
		$arr = [["code" => "200", "msg" => "更新成功"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($lx == "-1") {
		$ids = explode(",", $data);
		foreach ($ids as $id) {
			$sql = "UPDATE user SET ban = '-1', bantime = 'true' WHERE id = '{$id}'";
			$result = $conn->query($sql);
			if ($result) {
			} else {
				$arr = [["code" => "200", "msg" => "更新失败"]];
				exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
			}
		}
		$arr = [["code" => "200", "msg" => "更新成功"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($lx == "del") {
		$ids = explode(",", $data);
		foreach ($ids as $id) {
			$sql = "DELETE FROM user WHERE id = '{$id}'";
			$result = $conn->query($sql);
			if ($result) {
			} else {
				$arr = [["code" => "200", "msg" => "用户删除失败"]];
				exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
			}
		}
		$arr = [["code" => "200", "msg" => "用户已删除"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	exit;
}
if ($lx == "userbj") {
	$userid = addslashes(htmlspecialchars($_POST["userid"]));
	$userzh = addslashes(htmlspecialchars($_POST["uuserzh"]));
	$usermm = addslashes(htmlspecialchars($_POST["upwd"]));
	$userem = addslashes(htmlspecialchars($_POST["uuserem"]));
	$username = addslashes(htmlspecialchars($_POST["uusername"]));
	$userimg = addslashes(htmlspecialchars($_POST["uuserimg"]));
	$userwz = addslashes(htmlspecialchars($_POST["uuserwz"]));
	$userbj = addslashes(htmlspecialchars($_POST["uuserbj"]));
	$userqm = addslashes(htmlspecialchars($_POST["uuserqm"]));
	$userqx = addslashes(htmlspecialchars($_POST["uuserqx"]));
	$useres = addslashes(htmlspecialchars($_POST["uuseres"]));
	$userban = addslashes(htmlspecialchars($_POST["uuserban"]));
	$userbantime = addslashes(htmlspecialchars($_POST["uuserbantime"]));
	$sql = "SELECT * FROM user WHERE id = '{$userid}'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$juserzh = $row["username"];
		}
	}
	if ($userzh != "") {
		if (strpos($userzh, "vis#-[") !== false || strpos($userzh, "]-#vis") !== false) {
		} else {
			$userzh = str_replace(PHP_EOL, "", $userzh);
			if (strlen($userzh) >= 5) {
				$sql = "select * from user where username = '{$userzh}'";
				$result = $conn->query($sql);
				if (mysqli_num_rows($result) > 0) {
				} else {
					$sql = "UPDATE user SET username='{$userzh}' WHERE id='{$userid}'";
					$result = $conn->query($sql);
					$sql = "UPDATE essay SET ptpuser = '{$userzh}' WHERE ptpuser = '{$juserzh}'";
					$result = $conn->query($sql);
					$sql = "UPDATE comm SET couser = '{$userzh}',bcouser = '{$userzh}' WHERE couser = '{$juserzh}'";
					$result = $conn->query($sql);
					$sql = "UPDATE lcke SET luser = '{$userzh}' WHERE luser = '{$juserzh}'";
					$result = $conn->query($sql);
					$sql = "UPDATE message SET fuser = '{$userzh}', suser = '{$userzh}' WHERE fuser = '{$juserzh}'";
					$result = $conn->query($sql);
				}
			}
		}
	}
	if ($usermm != "") {
		$usermm = str_replace(PHP_EOL, "", $usermm);
		$usermm = md5($usermm);
		if (strlen($usermm) > 3) {
			$sql = "UPDATE user SET password='{$usermm}' WHERE id='{$userid}'";
			$result = $conn->query($sql);
		}
	}
	if ($userem != "") {
		$userem = str_replace(PHP_EOL, "", $userem);
		$sql = "select * from user where email = '{$userem}'";
		$result = $conn->query($sql);
		if (mysqli_num_rows($result) > 0) {
		} else {
			$sql = "UPDATE user SET email='{$userem}' WHERE id='{$userid}'";
			$result = $conn->query($sql);
		}
	}
	if (strpos($username, "匿名用户") !== false) {
		$ncjyc = "0";
	} else {
		$ncjyc = "1";
	}
	if ($username != "" && $ncjyc == "1") {
		$username = str_replace(PHP_EOL, "", $username);
		$sql = "select * from user where name = '{$username}'";
		$result = $conn->query($sql);
		if (mysqli_num_rows($result) > 0) {
		} else {
			$sql = "UPDATE user SET name='{$username}' WHERE id='{$userid}'";
			$result = $conn->query($sql);
			if ($result) {
				$sqlol = "select * from essay where ptpuser= '{$userzh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE essay SET ptpname='{$username}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
				$sqlol = "select * from lcke where luser= '{$userzh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE lcke SET lname='{$username}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
				$sqlol = "select * from comm where couser= '{$userzh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE comm SET coname='{$username}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
				$sqlol = "select * from message where fuser= '{$userzh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE message SET fname='{$username}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
			}
		}
	}
	if ($userimg != "") {
		$userimg = str_replace(PHP_EOL, "", $userimg);
		$sql = "UPDATE user SET img='{$userimg}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
		if ($result) {
			$sqlo = "select * from essay where ptpuser= '{$userzh}'";
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
			$sqlol = "select * from lcke where luser= '{$userzh}'";
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
			$sqlol = "select * from comm where couser= '{$userzh}'";
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
			$sqlol = "select * from message where fuser= '{$userzh}'";
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
	}
	if ($userwz != "") {
		$userwz = str_replace(PHP_EOL, "", $userwz);
		if (strpos($userwz, "http://") !== false || strpos($userwz, "https://") !== false) {
			$sql = "UPDATE user SET url='{$userwz}' WHERE id='{$userid}'";
			$result = $conn->query($sql);
			if ($result) {
				$sqlol = "select * from comm where couser= '{$userzh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE comm SET courl='{$userwz}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
			}
		}
	}
	if ($userbj != "") {
		$userbj = str_replace(PHP_EOL, "", $userbj);
		$sql = "UPDATE user SET homeimg='{$userbj}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
	}
	if ($userqm != "") {
		$userqm = str_replace(PHP_EOL, "", $userqm);
		$sql = "UPDATE user SET sign='{$userqm}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
	}
	if ($userqx != "") {
		$userqx = str_replace(PHP_EOL, "", $userqx);
		$sql = "UPDATE user SET essqx='{$userqx}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
	}
	if ($useres != "") {
		$useres = str_replace(PHP_EOL, "", $useres);
		$sql = "UPDATE user SET esseam='{$useres}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
	}
	if ($userban != "") {
		if ($userzh != $glyzhuser) {
			$userban = str_replace(PHP_EOL, "", $userban);
			$sql = "UPDATE user SET ban='{$userban}' WHERE id='{$userid}'";
			$result = $conn->query($sql);
		}
	}
	if ($userbantime != "") {
		$userbantime = str_replace(PHP_EOL, "", $userbantime);
		$sql = "UPDATE user SET bantime='{$userbantime}' WHERE id='{$userid}'";
		$result = $conn->query($sql);
	}
	exit("<script language=\"JavaScript\">;location.href=\"../userlist.php\";</script>;");
}
if ($lx == "deluser") {
	$uid = addslashes(htmlspecialchars($_POST["userid"]));
	if ($uid == "") {
		exit("<script language=\"JavaScript\">;alert(\"没有传入参数\");location.href=\"../userlist.php;</script>");
	}
	$sql = "SELECT * FROM user WHERE id = ?";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "s", $uid);
	mysqli_stmt_execute($stmt);
	$data_results = mysqli_stmt_get_result($stmt);
	if ($datas_row = mysqli_fetch_array($data_results)) {
		$del_zh = $datas_row["username"];
		$del_img = $datas_row["img"];
		$del_homeimg = $datas_row["homeimg"];
	}
	$sql = "SELECT * FROM user WHERE username = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"账号不存在\");location.href=\"../userlist.php\";</script>");
	}
	if ($del_zh == $glyzhuser) {
		exit("<script language=\"JavaScript\">;alert(\"不可删除管理员账号\");location.href=\"../userlist.php\";</script>");
	}
	$sql = "DELETE FROM user WHERE username=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	if ($stmt->execute()) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"删除账号失败\");location.href=\"../userlist.php\";</script>");
	}
	$sql = "SELECT * FROM essay WHERE ptpuser=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		$ptpimag = $row["ptpimag"];
		$ptpvideo = $row["ptpvideo"];
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
		$sql = "DELETE FROM essay WHERE ptpuser=?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $del_zh);
		if ($stmt->execute()) {
		}
	}
	$sql = "DELETE FROM comm WHERE couser=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	if ($stmt->execute()) {
	}
	$sql = "DELETE FROM lcke WHERE luser=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	if ($stmt->execute()) {
	}
	$sql = "DELETE FROM message WHERE fuser=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $del_zh);
	if ($stmt->execute()) {
	}
	$sql = "select * from user where username= '{$del_zh}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$bdhomebjt = "." . $row["img"];
		if (strstr($bdhomebjt, "/user/headimg")) {
			if (file_exists($bdhomebjt)) {
				unlink($bdhomebjt);
			}
		}
	}
	$sql = "select * from user where username= '{$del_zh}'";
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
	exit("<script language=\"JavaScript\">alert(\"账号[" . $del_zh . "]已删除\");location.href=\"../userlist.php\";</script>;");
}
$conn->close();
