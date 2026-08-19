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
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$arr = [["code" => "201", "msg" => "连接数据库失败"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$user_zhsg = addslashes(htmlspecialchars($user_zh));
$sqls = "select * from user where username='{$user_zhsg}'";
$data_results = mysqli_query($conn, $sqls);
$data2s_row = mysqli_fetch_array($data_results);
$user_zh = $data2s_row["username"];
$user_name = $data2s_row["name"];
$user_img = $data2s_row["img"];
$user_url = $data2s_row["url"];
$filtertext = "";
$sql = "SELECT text FROM configx WHERE title = 'filtertext'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filtertext = $row["text"];
}
if ($user_zh == "" || $user_passid == "") {
	$vis_name = addslashes(htmlspecialchars($_POST["vis_name"]));
	$vis_email = addslashes(htmlspecialchars($_POST["vis_email"]));
	$vis_url = addslashes(htmlspecialchars($_POST["vis_url"]));
	if (strpos($vis_url, "http://") !== 0 && strpos($vis_url, "https://") !== 0) {
		$vis_url = "http://" . $vis_url;
	}
	if ($vis_name != "" && $vis_email != "") {
		if (filter_var($vis_email, FILTER_VALIDATE_EMAIL)) {
		} else {
			$arr = [["code" => "201", "msg" => "邮箱格式不正确!"]];
			exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
		$yktxyx = substr($vis_email, 0, strpos($vis_email, "@"));
		if (is_numeric($yktxyx) && iconv_strlen($yktxyx, "UTF-8") >= 5) {
			$vis_naimg = "http://q1.qlogo.cn/g?b=qq&nk=" . $yktxyx . "&s=100";
		} else {
			$vis_naimg = "./assets/img/tx.png";
		}
		$user_zh = "vis#-[" . $vis_email . "]-#vis";
		$user_name = $vis_name;
		$user_img = $vis_naimg;
		$user_url = $vis_url;
		$user_passid = "vis_youke";
	} else {
		$arr = [["code" => "201", "msg" => "请先登录!"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
}
$tieid = addslashes(htmlspecialchars($_POST["tieid"]));
$tiehf = addslashes(htmlspecialchars($_POST["tiehf"]));
$tieea = addslashes(htmlspecialchars($_POST["tieea"]));
$pltexty = addslashes(htmlspecialchars($_POST["pltext"]));
if ($tieid == "") {
	$arr = [["code" => "201", "msg" => "未获取到文章id"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
if ($tiehf == "") {
	$arr = [["code" => "201", "msg" => "未获取到被评论人昵称"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
if ($tieea == "") {
	$arr = [["code" => "201", "msg" => "未获取到被评论人账号"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
if ($pltexty == "") {
	$arr = [["code" => "201", "msg" => "未获取到评论内容"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
if (iconv_strlen($pltexty, "UTF-8") > 500) {
	$arr = [["code" => "201", "msg" => "字数超过最大限制!"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
if ($filtertext != "") {
	$arrayData = json_decode($filtertext, true);
	$pltexty = str_replace($arrayData, "*", $pltexty);
}
$emtext = $pltexty;
$pltexty = parseUrls($pltexty);
$arr = [["title" => "::(呵呵)", "img" => "./assets/owo/paopao/E591B5E591B5_2x.png"], ["title" => "::(哈哈)", "img" => "./assets/owo/paopao/E59388E59388_2x.png"], ["title" => "::(吐舌)", "img" => "./assets/owo/paopao/E59090E8888C_2x.png"], ["title" => "::(太开心)", "img" => "./assets/owo/paopao/E5A4AAE5BC80E5BF83_2x.png"], ["title" => "::(笑眼)", "img" => "./assets/owo/paopao/E7AC91E79CBC_2x.png"], ["title" => "::(花心)", "img" => "./assets/owo/paopao/E88AB1E5BF83_2x.png"], ["title" => "::(小乖)", "img" => "./assets/owo/paopao/E5B08FE4B996_2x.png"], ["title" => "::(乖)", "img" => "./assets/owo/paopao/E4B996_2x.png"], ["title" => "::(捂嘴笑)", "img" => "./assets/owo/paopao/E68D82E598B4E7AC91_2x.png"], ["title" => "::(滑稽)", "img" => "./assets/owo/paopao/E6BB91E7A8BD_2x.png"], ["title" => "::(你懂的)", "img" => "./assets/owo/paopao/E4BDA0E68782E79A84_2x.png"], ["title" => "::(不高兴)", "img" => "./assets/owo/paopao/E4B88DE9AB98E585B4_2x.png"], ["title" => "::(怒)", "img" => "./assets/owo/paopao/E68092_2x.png"], ["title" => "::(汗)", "img" => "./assets/owo/paopao/E6B197_2x.png"], ["title" => "::(黑线)", "img" => "./assets/owo/paopao/E9BB91E7BABF_2x.png"], ["title" => "::(泪)", "img" => "./assets/owo/paopao/E6B3AA_2x.png"], ["title" => "::(真棒)", "img" => "./assets/owo/paopao/E79C9FE6A392_2x.png"], ["title" => "::(喷)", "img" => "./assets/owo/paopao/E596B7_2x.png"], ["title" => "::(惊哭)", "img" => "./assets/owo/paopao/E6838AE593AD_2x.png"], ["title" => "::(阴险)", "img" => "./assets/owo/paopao/E998B4E999A9_2x.png"], ["title" => "::(鄙视)", "img" => "./assets/owo/paopao/E98499E8A786_2x.png"], ["title" => "::(酷)", "img" => "./assets/owo/paopao/E985B7_2x.png"], ["title" => "::(啊)", "img" => "./assets/owo/paopao/E5958A_2x.png"], ["title" => "::(狂汗)", "img" => "./assets/owo/paopao/E78B82E6B197_2x.png"], ["title" => "::(what)", "img" => "./assets/owo/paopao/what_2x.png"], ["title" => "::(疑问)", "img" => "./assets/owo/paopao/E79691E997AE_2x.png"], ["title" => "::(酸爽)", "img" => "./assets/owo/paopao/E985B8E788BD_2x.png"], ["title" => "::(呀咩蹀)", "img" => "./assets/owo/paopao/E59180E592A9E788B9_2x.png"], ["title" => "::(委屈)", "img" => "./assets/owo/paopao/E5A794E5B188_2x.png"], ["title" => "::(惊讶)", "img" => "./assets/owo/paopao/E6838AE8AEB6_2x.png"], ["title" => "::(睡觉)", "img" => "./assets/owo/paopao/E79DA1E8A789_2x.png"], ["title" => "::(笑尿)", "img" => "./assets/owo/paopao/E7AC91E5B0BF_2x.png"], ["title" => "::(挖鼻)", "img" => "./assets/owo/paopao/E68C96E9BCBB_2x.png"], ["title" => "::(吐)", "img" => "./assets/owo/paopao/E59090_2x.png"], ["title" => "::(犀利)", "img" => "./assets/owo/paopao/E78A80E588A9_2x.png"], ["title" => "::(小红脸)", "img" => "./assets/owo/paopao/E5B08FE7BAA2E884B8_2x.png"], ["title" => "::(懒得理)", "img" => "./assets/owo/paopao/E68792E5BE97E79086_2x.png"], ["title" => "::(勉强)", "img" => "./assets/owo/paopao/E58B89E5BCBA_2x.png"], ["title" => "::(爱心)", "img" => "./assets/owo/paopao/E788B1E5BF83_2x.png"], ["title" => "::(心碎)", "img" => "./assets/owo/paopao/E5BF83E7A28E_2x.png"], ["title" => "::(玫瑰)", "img" => "./assets/owo/paopao/E78EABE791B0_2x.png"], ["title" => "::(礼物)", "img" => "./assets/owo/paopao/E7A4BCE789A9_2x.png"], ["title" => "::(彩虹)", "img" => "./assets/owo/paopao/E5BDA9E899B9_2x.png"], ["title" => "::(太阳)", "img" => "./assets/owo/paopao/E5A4AAE998B3_2x.png"], ["title" => "::(星星月亮)", "img" => "./assets/owo/paopao/E6989FE6989FE69C88E4BAAE_2x.png"], ["title" => "::(钱币)", "img" => "./assets/owo/paopao/E992B1E5B881_2x.png"], ["title" => "::(茶杯)", "img" => "./assets/owo/paopao/E88CB6E69DAF_2x.png"], ["title" => "::(蛋糕)", "img" => "./assets/owo/paopao/E89B8BE7B395_2x.png"], ["title" => "::(大拇指)", "img" => "./assets/owo/paopao/E5A4A7E68B87E68C87_2x.png"], ["title" => "::(胜利)", "img" => "./assets/owo/paopao/E8839CE588A9_2x.png"], ["title" => "::(haha)", "img" => "./assets/owo/paopao/haha_2x.png"], ["title" => "::(OK)", "img" => "./assets/owo/paopao/OK_2x.png"], ["title" => "::(沙发)", "img" => "./assets/owo/paopao/E6B299E58F91_2x.png"], ["title" => "::手纸", "img" => "./assets/owo/paopao/E6898BE7BAB8_2x.png"], ["title" => "::(香蕉)", "img" => "./assets/owo/paopao/E9A699E89589_2x.png"], ["title" => "::(便便)", "img" => "./assets/owo/paopao/E4BEBFE4BEBF_2x.png"], ["title" => "::(药丸)", "img" => "./assets/owo/paopao/E88DAFE4B8B8_2x.png"], ["title" => "::(红领巾)", "img" => "./assets/owo/paopao/E7BAA2E9A286E5B7BE_2x.png"], ["title" => "::(蜡烛)", "img" => "./assets/owo/paopao/E89CA1E7839B_2x.png"], ["title" => "::(音乐)", "img" => "./assets/owo/paopao/E99FB3E4B990_2x.png"], ["title" => "::(灯泡)", "img" => "./assets/owo/paopao/E781AFE6B3A1_2x.png"]];
for ($i = 0; $i < count($arr); $i++) {
	$danm = $arr[$i]["title"];
	$dang = $arr[$i]["img"];
	$aeimg = "<span class=\"sh-nr-bq-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $dang . "\" class=\"sh-nr-bq-img\" alt=\"" . $danm . "\"></span>";
	$pltexty = str_ireplace($danm, $aeimg, $pltexty);
}
$pltext = $pltexty;
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$wzname = $row["name"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
	$comaud = $row["comaud"];
	$glyusername = $row["username"];
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
$ban = $data2_row["ban"];
$bantime = $data2_row["bantime"];
if ($user_passid != "vis_youke") {
	if ($user_passid != $passid) {
		setcookie("username", "", time() + -1, "/");
		setcookie("passid", "", time() + -1, "/");
		$arr = [["code" => "201", "msg" => "账号信息异常,请重新登录!"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($ban != "0" && $bantime != "false") {
		if ($bantime === "true") {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			$arr = [["code" => "201", "msg" => "该账号涉嫌违规,已被永久封禁!"]];
			exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
		} else {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			$arr = [["code" => "201", "msg" => "你的账号已被封禁！解封时间为:" . $bantime]];
			exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
	}
}
$data_result = mysqli_query($conn, "select * from essay where cid='{$tieid}'");
$data_row = mysqli_fetch_array($data_result);
if ($data_row["commauth"] != 1) {
	$arr = [["code" => "201", "msg" => "该文章禁止评论!"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$Y = "Y";
$m = "m";
$d = "d";
$H = "H";
$i = "i";
$s = "s";
$sj = date($Y . "-" . $m . "-" . $d . " " . $H . ":" . $i . ":" . $s);
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
$microtime = str_replace(".", "", microtime(true));
$randomDigits = "";
do {
	for ($i = 0; $i < 5; $i++) {
		$randomDigits .= rand(0, 9);
	}
} while (strlen($randomDigits) < 5);
$str = $microtime . $randomDigits;
if (strpos($tieea, "vis#-[") !== false && strpos($tieea, "]-#vis") !== false) {
	if ($tiehf == $vis_name) {
		$tiehf = "false";
		$tieea = "false";
	}
} else {
	if ($tieea == $user_zh) {
		$tiehf = "false";
		$tieea = "false";
	}
}
$data_result = mysqli_query($conn, "select * from essay where cid='{$tieid}'");
$data_row = mysqli_fetch_array($data_result);
$wzfszh = $data_row["ptpuser"];
$wzfsptptext = $data_row["ptptext"];
$data_resultl = mysqli_query($conn, "select * from user where username='{$wzfszh}'");
$data_rowl = mysqli_fetch_array($data_resultl);
$wzfszh = $data_rowl["username"];
$wzfsnc = $data_rowl["name"];
$wzfstx = $data_rowl["img"];
$wzfseam = $data_rowl["esseam"];
$wzfsemail = $data_rowl["email"];
if ($tieea != "false") {
	$data_result = mysqli_query($conn, "select * from user where username='{$tieea}'");
	$data_row = mysqli_fetch_array($data_result);
	$wzfseam = $data_row["esseam"];
	$wzfsemail = $data_row["email"];
}
$data_result = mysqli_query($conn, "select * from user where username='{$user_zh}'");
$data_row = mysqli_fetch_array($data_result);
$huifzmail = $data_row["email"];
$huifzdurl = $data_row["url"];
if ($huifzmail == "") {
	$huifzmail = $vis_email;
}
if ($huifzdurl == "") {
	$huifzdurl = $vis_url;
}
if ($user_name == "") {
	$user_name = $vis_name;
}
if ($wzfsnc == "") {
	$wzfsnc = $tiehf;
}
if ($zjzhq == $glyusername) {
	$comaud = 0;
	$comaudzt = 1;
} else {
	if ($comaud == 0) {
		$comaudzt = 1;
	} else {
		$comaudzt = 0;
	}
}
$bdm = "comm";
$sql = "INSERT INTO {$bdm} (couser,coimg,coname,courl,cotext,bcouser,bconame,comaud,cotime,ip,wzcid,ecid)
VALUES ('{$user_zh}','{$user_img}','{$user_name}','{$user_url}','{$pltext}','{$tieea}','{$tiehf}','{$comaudzt}','{$sj}','{$ip}','{$tieid}','{$str}')";
if ($conn->query($sql) === true) {
	if ($comaud == 0) {
		$arr = [["code" => "200", "msg" => "评论成功!", "uuzh" => $user_zh, "uunc" => $user_name, "uutx" => $user_img, "uuwz" => $user_url, "buunc" => $tiehf, "buuzh" => $tieea, "pluunr" => $pltext, "plecid" => $str, "tieid" => $tieid]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	} else {
		$data_resultgl = mysqli_query($conn, "select * from user where username='{$glyusername}'");
		$data_rowgl = mysqli_fetch_array($data_resultgl);
		$glydem = $data_rowgl["email"];
		$btm = $user_name . "回复了:" . $wzfsnc;
		$mailapfs = "nopost";
		$mailtitle = "[" . $wzname . "]" . "您有新的待审核评论!";
		$title = "<div class=\"sh-wz-t\" style=\"color: #55798d;\">待审核的评论 - 来自文章：</div><div class=\"sh-wz-n\" style=\"background:#f7f7f7\"><p class=\"sh-wz-n-nr\" style=\"color: #000000;\"><a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/view.php?cid=" . $tieid . "\" style=\"text-decoration: none! important;\" target=\"_blank\" id=\"0\">" . $wzfsptptext . "</a></p></div><div class=\"sh-wz-b\" style=\"color: #55798d;\">评论内容：</div>";
		$text = $btm . "<br>" . $emtext;
		$mailbox = $glydem;
		include "./sendmail.php";
		$arr = [["code" => "201", "msg" => "评论成功,审核通过后即可显示!"]];
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	}
	if (strpos($tieea, "vis#-[") !== false && strpos($tieea, "]-#vis") !== false) {
	} else {
		if ($tieea == "false") {
			if ($wzfszh != $user_zh) {
				$bdm2 = "message";
				$btm = $user_name . "回复了:" . $wzfsnc;
				$sql2 = "INSERT INTO {$bdm2} (fuser,fimg,fname,suser,title,text,ftime,msg)
            VALUES ('{$user_zh}','{$user_img}','{$user_name}','{$wzfszh}','{$btm}','{$pltext}','{$sj}','0')";
				if ($conn->query($sql2) === true) {
				}
			}
		} else {
			$bdm2 = "message";
			$btm = $user_name . "回复了：" . $tiehf;
			$sql2 = "INSERT INTO {$bdm2} (fuser,fimg,fname,suser,title,text,ftime,msg)
        VALUES ('{$user_zh}','{$user_img}','{$user_name}','{$tieea}','{$btm}','{$pltext}','{$sj}','0')";
			if ($conn->query($sql2) === true) {
			}
		}
	}
	if (strpos($tieea, "vis#-[") !== false && strpos($tieea, "]-#vis") !== false) {
		$ykx = str_replace("vis#-[", "", $tieea);
		$ykx1 = str_replace("]-#vis", "", $ykx);
		$wzfsemail = $ykx1;
		$btm = $user_name . "回复了:" . $tiehf;
		if ($wzfsemail != $vis_email) {
			$mailapfs = "nopost";
			$mailtitle = "[" . $wzname . "]" . "新评论！";
			$title = "<div class=\"sh-wz-t\" style=\"color: #55798d;\">新评论 - 来自文章：</div><div class=\"sh-wz-n\" style=\"background:#f7f7f7\"><p class=\"sh-wz-n-nr\" style=\"color: #000000;\"><a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/view.php?cid=" . $tieid . "\" style=\"text-decoration: none! important;\" target=\"_blank\" id=\"0\">" . $wzfsptptext . "</a></p></div><div class=\"sh-wz-b\" style=\"color: #55798d;\">评论内容：</div>";
			$text = $btm . "<br>" . $emtext;
			$mailbox = $wzfsemail;
			include "./sendmail.php";
		}
	} else {
		if ($wzfsemail != $zjemail) {
			if ($wzfseam == 1) {
				$mailapfs = "nopost";
				$mailtitle = "[" . $wzname . "]" . "新评论！";
				$title = "<div class=\"sh-wz-t\" style=\"color: #55798d;\">新评论 - 来自文章：</div><div class=\"sh-wz-n\" style=\"background:#f7f7f7\"><p class=\"sh-wz-n-nr\" style=\"color: #000000;\"><a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/view.php?cid=" . $tieid . "\" style=\"text-decoration: none! important;\" target=\"_blank\" id=\"0\">" . $wzfsptptext . "</a></p></div><div class=\"sh-wz-b\" style=\"color: #55798d;\">评论内容：</div>";
				$text = $btm . "<br>" . $emtext;
				$mailbox = $wzfsemail;
				include "./sendmail.php";
			}
		}
	}
} else {
	$arr = [["code" => "201", "msg" => "评论失败!", "uuzh" => $user_zh, "uunc" => $user_name, "uutx" => $user_img, "uuwz" => $user_url, "buunc" => $tiehf, "buuzh" => $tieea, "pluunr" => $pltext, "plecid" => $str, "tieid" => $tieid]];
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}
$conn->close();
function parseUrls($string)
{
	$urls = [];
	$pattern = "/\\b(?:https?:\\/\\/|www\\.)([a-z0-9.-]+\\.[a-z]{2,4}(?:\\/[^\\s]*)?)/i";
	preg_match_all($pattern, $string, $matches);
	foreach ($matches[0] as $url) {
		if (strpos($url, "http://") !== 0 && strpos($url, "https://") !== 0) {
			$url = "http://" . $url;
		}
		$urls[] = "<a href=\"" . $url . "\" target=\"_blank\" class=\"sh-wz-nr-pl-url\" onclick=\"hfljurl()\">" . $url . "</a>";
	}
	$string = str_replace($matches[0], $urls, $string);
	return $string;
}
