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
	$userdlzt = "0";
} else {
	$userdlzt = "1";
}
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
if (!isset($iteace)) {
	exit("请设置状态!");
}
if ($iteace == "0") {
	if (is_dir("./install")) {
		if (!is_file("./install/ins.bak")) {
			exit("请先安装程序！<a href=\"./install/\">点击安装</a>");
		}
	}
	if (!is_file("./config.php")) {
		exit("未检测到配置文件：<span style=\"color: red;\">config.php</span>，若您是首次使用请先进行安装,删除文件夹 <span style=\"color: red;\">[install/]</span> 下的 <span style=\"color: red;\">[ins.bak]</span> 文件后进行安装。");
	}

	$pass_user = $pass_user_commit;
	$re = md5(md5($pass_user));
} elseif ($iteace == "1") {
	if (is_dir("../install")) {
		if (!is_file("../install/ins.bak")) {
			exit("请先安装程序！<a href=\"../install/\">点击安装</a>");
		}
	}
	if (!is_file("../config.php")) {
		exit("未检测到配置文件：<span style=\"color: red;\">config.php</span>，若您是首次使用请先进行安装,删除文件夹 <span style=\"color: red;\">[install/]</span> 下的 <span style=\"color: red;\">[ins.bak]</span> 文件后进行安装。");
	}

	$pass_user = $pass_user_commit;
	$re = md5(md5($pass_user));
} elseif ($iteace == "2") {
	$pass_user = $pass_user_commit;
	$re = md5(md5($pass_user));

	if (!extension_loaded("sg11")) {
		exit("sg11扩展未安装");
	}
	if (!extension_loaded("exif")) {
		exit("exif扩展未安装");
	}
} else {
	exit("状态错误!");
}
if (!isset($_COOKIE["dark_theme"])) {
	setcookie("dark_theme", "root", 0, "/");
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$subtitle = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$username = $row["username"];
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
	$comaud = $row["comaud"];
	$ptpaud = $row["ptpaud"];
	$ptpfan = $row["ptpfan"];
	$loginkg = $row["loginkg"];
	$notname = $row["notname"];
	$imgpres = $row["imgpres"];
	$rosdomain = $row["rosdomain"];
	$daymode = $row["daymode"];
	$gotop = $row["gotop"];
	$search = $row["search"];
	$videoauplay = $row["videoauplay"];
	$regverify = $row["regverify"];
	$pagepass = $row["pagepass"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
	$copyright = $row["copyright"];
	$beian = $row["beian"];
	$topes = $row["topes"];
	$scfont = $row["scfont"];
	$viscomm = $row["viscomm"];
	$musplay = $row["musplay"];
	$date = $row["date"];
}
if ($pagepass == "") {
	if (isset($_COOKIE["pagepass"])) {
		setcookie("pagepass", "", time() + 0, "/");
	}
}
$filtertext = "";
$sql = "SELECT text FROM configx WHERE title = 'filtertext'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filtertext = $row["text"];
}
$filterupy = "";
$sql = "SELECT text FROM configx WHERE title = 'upyun'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filterupy = $row["text"];
}
$filterpiccir = "";
$sql = "SELECT text FROM configx WHERE title = 'piccir'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filterpiccir = $row["text"];
}
$filtercucss = "";
$sql = "SELECT text FROM configx WHERE title = 'cucss'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filtercucss = htmlspecialchars_decode($row["text"]);
}
$filtercujs = "";
$sql = "SELECT text FROM configx WHERE title = 'cujs'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filtercujs = htmlspecialchars_decode($row["text"]);
}
if ($iteace == "0") {
	if ($zt != 1) {
		header("Location: ../404.php?close=true");
		exit;
	}
} elseif ($iteace == "1") {
} else {
	exit("状态错误!");
}
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
} elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
} elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) {
	$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
	$ip = getenv("HTTP_X_FORWARDED_FOR");
} elseif (getenv("HTTP_CLIENT_IP")) {
	$ip = getenv("HTTP_CLIENT_IP");
} elseif (getenv("REMOTE_ADDR")) {
	$ip = getenv("REMOTE_ADDR");
} else {
	$ip = "Unknown";
}
$length = 64;
$allkey = generateRandomString($length);
$visykmz_userzh = "游客" . generateRandomString(5);
$visykmz_userip = "vis#-[" . $ip . "]-#vis";
session_start();
$_SESSION["allkey"] = $allkey;
if ($_SESSION["visykmz_userzh"] == "" || $_SESSION["visykmz_userip"] == "") {
	$_SESSION["visykmz_userzh"] = $visykmz_userzh;
	$_SESSION["visykmz_userip"] = $visykmz_userip;
}
if ($scfont != "") {
	$scfontzt = "<style>
        @font-face {
          font-family: \"MyFont\";
          src: url(\"" . $scfont . "\");
        }
        body{
            font-family: \"MyFont\";
        }
        </style>";
} else {
	$scfontzt = "";
}
$data_result = mysqli_query($conn, "select * from user where username='{$username}'");
$data_row = mysqli_fetch_array($data_result);
$glyname = $data_row["name"];
$glyimg = $data_row["img"];
$glyuserzha = $data_row["username"];
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
$ban = $data2_row["ban"];
$bantime = $data2_row["bantime"];
if ($userdlzt == 1) {
	if ($ban != "0" && $bantime != "false") {
		if ($bantime === "true") {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			exit("<script language=\"JavaScript\">;alert(\"该账号涉嫌违规,已被永久封禁!\");location.href=\"./index.php\";</script>");
		} else {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			exit("<script language=\"JavaScript\">;alert(\"你的账号已被封禁！解封时间为:" . $bantime . "\");location.href=\"./index.php\";</script>");
		}
	}
}
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"./index.php\";</script>");
}
$iconurl_url = "//at.alicdn.com/t/c/font_3781624_acf7eqdy5ke.css";
$key = "faf663802de1ecc07c099cd65664a356";
$url = "http://www.qemao.com/files/lan/api/updatedow.php";
$data = ["key" => $key];
$headers = ["Content-Type: application/x-www-form-urlencoded"];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($curl);
curl_close($curl);
$arr = json_decode($result, true);
$code = $arr[0]["code"];
$vernumx = $arr[0]["vernumx"];
$uplog = $arr[0]["uplog"];
$uplog = explode(PHP_EOL, $uplog);
$dow = $arr[0]["dow"];
$resversion = $vernumx;
if ($vernumx == "" || $vernumx == null) {
	$resversion = date("Y.m.d");
}
if ($iteace == "0") {
	if (file_exists("./upfile/")) {
		if (file_exists("./upfile/up.ins")) {
			$folderPath = "./upfile/";
			if (is_dir($folderPath)) {
				$files = glob($folderPath . "/*");
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}
				}
				rmdir($folderPath);
			}
		} else {
			header("location:./upfile/");
			exit;
		}
	}
	$zipFile = "./lan.zip";
	if (file_exists($zipFile)) {
		unlink($zipFile);
	}
} else {
	if (file_exists("../upfile/")) {
		if (file_exists("../upfile/up.ins")) {
			$folderPath = "../upfile/";
			if (is_dir($folderPath)) {
				$files = glob($folderPath . "/*");
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}
				}
				rmdir($folderPath);
			}
		} else {
			header("location:../upfile/");
			exit;
		}
	}
	$zipFile = "../lan.zip";
	if (file_exists($zipFile)) {
		unlink($zipFile);
	}
}
if (file_exists("./updatb.php")) {
	exit("<style>
    .sh-k-suha2517270540{
        width: 100%;
        /*height: 100vh;*/
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .sh-abq{
        background: #09c362;
        color: #ffffff;
        text-decoration: none;
        padding: 10px 10px;
        margin-top: 25px;
        font-size: 15px;
        border-radius: 4px;
    }
    h4{
        color:#586c97;
    }
    .sh-gxnr{
        color:#4c5254;
    }
    </style>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0, user-scalable=no minimal-ui\">
    <div class=\"sh-k-suha2517270540\">
    <div><h4>升级数据库</h4></div>
    <div class=\"sh-gxnr\">本次更新了部分数据库字段,请先升级数据库再运行此程序!</div>
    <a href=\"./updatb.php\" class=\"sh-abq\">点击一键升级</a>
    </div>");
}
function generateRandomString($length)
{
	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$randomString = "";
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}
