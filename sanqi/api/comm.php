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
	$userdlzt = "0";
} else {
	$userdlzt = "1";
}
$page = addslashes(htmlspecialchars($_POST["page"]));
$wzcidd = addslashes(htmlspecialchars($_POST["wzcidd"]));
if ($page == "" || $wzcidd == "") {
	exit("参数为空!");
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
if (strstr($_SERVER["HTTP_REFERER"], "home.php")) {
	$scplan = 1;
} else {
	$scplan = "";
}
$dqgs = 0;
$dqs = 0;
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$name = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$username = $row["username"];
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$kqsy = $row["kqsy"];
	$topes = $row["topes"];
}
$ars = explode(PHP_EOL, $topes);
$sql = "select * from essay where cid= '{$wzcidd}'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	$roww = mysqli_fetch_assoc($result);
	$wzyonyzh = $roww["ptpuser"];
	$wzyonyid = $roww["id"];
}
if ($userdlzt == 1) {
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
		exit("账号信息异常,请重新登录");
	}
}
$sql = "select * from comm where wzcid= '{$wzcidd}' and comaud<>'0' and comaud<>'-1'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_assoc($result)) {
		$dqgs = $dqgs + 1;
		$couser = $row["couser"];
		$coimg = $row["coimg"];
		$coname = $row["coname"];
		$courl = $row["courl"];
		$coecid = $row["ecid"];
		$cotext = $row["cotext"];
		$bcouser = $row["bcouser"];
		$bconame = $row["bconame"];
		$cotime = $row["cotime"];
		$ip = $row["ip"];
		$wzcid = $row["wzcid"];
		$eid = $row["eid"];
		$comaud = $row["comaud"];
		if ($comaud != 1) {
			$cotext = "该条评论未通过审核!";
		}
		if ($courl == "") {
			$plzwze = "";
		} else {
			$plzwze = "href=\"" . $courl . "\" style=\"pointer-events: all;\"";
		}
		if ($page < $dqgs) {
			$dqs = $dqs + 1;
			if ($dqs > 10) {
				break;
			}
			if ($scplan == 1) {
				if ($userdlzt == 1) {
					if ($user_zh == $glyadmin) {
						$scplanp = "<a href=\"JavaScript:;\" class=\"sh-zanp-pl-del\" onclick=\"pldels(this)\" id=\"" . $coecid . "\" lang=\"yswid-" . $wzcidd . "\">删除</a>";
						$glyszd = "terewsq";
					} else {
						if ($wzyonyzh == $zjzhq) {
							if (in_array($wzyonyid, $ars)) {
								$scplanp = "";
							} else {
								$scplanp = "<a href=\"JavaScript:;\" class=\"sh-zanp-pl-del\" onclick=\"pldels(this)\" id=\"" . $coecid . "\" lang=\"yswid-" . $wzcidd . "\">删除</a>";
							}
						} else {
							$scplanp = "";
							$glyszd = "";
						}
					}
				} else {
					$scplanp = "";
				}
			}
			if ($bcouser == "false" || $bconame == "false") {
				echo "
            <li lang=\"" . $coname . "\" onclick=\"plhuifu()\" id=\"" . $wzcid . "\" value=\"" . $couser . "\" data-comkzt=\"0\">
                                <div class=\"sh-zanp-pl-n\">
                                    <a " . $plzwze . " class=\"sh-zanp-pl-n-nc\" onclick=\"hfljurl()\" target=\"_blank\">" . $coname . "</a>：
                                    <span class=\"sh-zanp-pl-n-nr\">" . $cotext . "</span>
                                </div>
                                " . $scplanp . "
                            </li>
            ";
			} else {
				echo "
            <li lang=\"" . $coname . "\" onclick=\"plhuifu()\" id=\"" . $wzcid . "\" value=\"" . $couser . "\" data-comkzt=\"0\">
                                <div class=\"sh-zanp-pl-n\">
                                    <a " . $plzwze . " class=\"sh-zanp-pl-n-nc\" onclick=\"hfljurl()\" target=\"_blank\">" . $coname . "</a>
                                    <span>回复</span>
                                    <span class=\"sh-zanp-pl-n-nc\">" . $bconame . "</span>：
                                    <span class=\"sh-zanp-pl-n-nr\">" . $cotext . "</span>
                                </div>
                                " . $scplanp . "
                            </li>
            ";
			}
		}
	}
}
$conn->close();
