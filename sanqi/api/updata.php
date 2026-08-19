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
	$arr = [["code" => "201", "msg" => "请先登录"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
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
	$arr = [["code" => "201", "msg" => "账号信息异常,请重新登录!"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$lx = addslashes(htmlspecialchars($_POST["lx"]));
if ($lx == "aq") {
	$userzh = addslashes(htmlspecialchars($_POST["userzh"]));
	$usermm = addslashes(htmlspecialchars($_POST["usermm"]));
	$userxmm = addslashes(htmlspecialchars($_POST["userxmm"]));
	$userem = addslashes(htmlspecialchars($_POST["userem"]));
	if ($usermm != "" && $userxmm != "") {
		if ($usermm == $userxmm) {
			$arr = [["code" => "201", "msg" => "新密码不能与旧密码相同"]];
			exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
		if (ispassword($userxmm)) {
			$mm = md5($userxmm);
			if (md5($usermm) == $zjpassword) {
				$sql = "UPDATE user SET password='{$mm}' WHERE id='{$zid}'";
				$result = $conn->query($sql);
				if ($result) {
					$arr = [["code" => "200", "msg" => "密码修改成功"]];
					echo json_encode($arr, JSON_UNESCAPED_UNICODE);
				} else {
					$arr = [["code" => "201", "msg" => mysql_errno()]];
					echo json_encode($arr, JSON_UNESCAPED_UNICODE);
				}
			} else {
				$arr = [["code" => "201", "msg" => "旧密码错误"]];
				echo json_encode($arr, JSON_UNESCAPED_UNICODE);
			}
		} else {
			$arr = [["code" => "201", "msg" => "密码不符合规则"]];
			echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		}
	} else {
		if ($userem != "") {
			$checkmail = "/\\w+([-+.']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/";
			if (preg_match($checkmail, $userem)) {
			} else {
				$arr = [["code" => "201", "msg" => "电子邮箱格式不正确"]];
				echo json_encode($arr, JSON_UNESCAPED_UNICODE);
				exit;
			}
			$sql = "UPDATE user SET email='{$userem}' WHERE id='{$zid}'";
			$result = $conn->query($sql);
			if ($result) {
				$arr = [["code" => "200", "msg" => "邮箱修改成功"]];
				echo json_encode($arr, JSON_UNESCAPED_UNICODE);
			} else {
				$arr = [["code" => "201", "msg" => mysql_errno()]];
				echo json_encode($arr, JSON_UNESCAPED_UNICODE);
			}
		}
	}
}
if ($lx == "zlnc") {
	$usernc = addslashes(htmlspecialchars($_POST["usernc"]));
	if ($usernc == "") {
		$arr = [["code" => "201", "msg" => "名字不可为空"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if (iconv_strlen($usernc, "UTF-8") > 10) {
		$arr = [["code" => "201", "msg" => "名字不可超过10个字符"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if (strpos($usernc, "匿名用户") !== false) {
		$arr = [["code" => "201", "msg" => "此昵称为禁用词"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	$sql = "select * from user where name= '{$usernc}'";
	$result = $conn->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$arr = [["code" => "201", "msg" => "昵称已存在"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	} else {
		if ($usernc != "") {
			$usernc = str_replace(PHP_EOL, "", $usernc);
			$sql = "UPDATE user SET name='{$usernc}' WHERE id='{$zid}'";
			$result = $conn->query($sql);
			if ($result) {
				$sqlol = "select * from essay where ptpuser= '{$user_zh}'";
				$resultol = mysqli_query($conn, $sqlol);
				if (mysqli_num_rows($resultol) > 0) {
					while ($rowo = mysqli_fetch_assoc($resultol)) {
						$ncid = $rowo["id"];
						$sql = "UPDATE essay SET ptpname='{$usernc}' WHERE id='{$ncid}'";
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
						$sql = "UPDATE lcke SET lname='{$usernc}' WHERE id='{$ncid}'";
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
						$sql = "UPDATE comm SET coname='{$usernc}' WHERE id='{$ncid}'";
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
						$sql = "UPDATE message SET fname='{$usernc}' WHERE id='{$ncid}'";
						$result = $conn->query($sql);
						if ($result) {
						}
					}
				}
			} else {
				$arr = [["code" => "201", "msg" => mysql_errno()]];
				echo json_encode($arr, JSON_UNESCAPED_UNICODE);
			}
		}
		$arr = [["code" => "200", "msg" => "名字更新成功"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	}
}
if ($lx == "zlqm") {
	$userqm = addslashes(htmlspecialchars($_POST["userqm"]));
	if (iconv_strlen($userqm, "UTF-8") > 50) {
		$arr = [["code" => "201", "msg" => "签名不可超过50个字符"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	$userqm = str_replace(PHP_EOL, "", $userqm);
	$sql = "UPDATE user SET sign='{$userqm}' WHERE id='{$zid}'";
	$result = $conn->query($sql);
	if ($result) {
		$arr = [["code" => "200", "msg" => "签名更新成功"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	} else {
		$arr = [["code" => "201", "msg" => mysql_errno()]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	}
}
if ($lx == "zlwz") {
	$userurl = addslashes(htmlspecialchars($_POST["userurl"]));
	if ($userurl != "") {
		if (strpos($userurl, "http://") !== false || strpos($userurl, "https://") !== false) {
		} else {
			$arr = [["code" => "201", "msg" => "网址必须含有http或https"]];
			exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
	}
	$userurl = str_replace("#\$#", "&", $userurl, $count);
	$userurl = $userurl . PHP_EOL;
	$userurl = str_replace(PHP_EOL, "", $userurl);
	$sql = "UPDATE user SET url='{$userurl}' WHERE id='{$zid}'";
	$result = $conn->query($sql);
	if ($result) {
		$arr = [["code" => "200", "msg" => "网址更新成功"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		$sqlol = "select * from comm where couser= '{$user_zh}'";
		$resultol = mysqli_query($conn, $sqlol);
		if (mysqli_num_rows($resultol) > 0) {
			while ($rowo = mysqli_fetch_assoc($resultol)) {
				$urid = $rowo["id"];
				$sql = "UPDATE comm SET courl='{$userurl}' WHERE id='{$urid}'";
				$result = $conn->query($sql);
				if ($result) {
				}
			}
		}
	} else {
		$arr = [["code" => "201", "msg" => mysql_errno()]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	}
}
$conn->close();
function ispassword($str)
{
	if (preg_match("/^[_.0-9a-z]{3,16}\$/i", $str)) {
		return true;
	} else {
		return false;
	}
}
