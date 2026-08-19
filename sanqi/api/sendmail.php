<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

if (isset($mailapfs) && $mailapfs === "nopost") {
	$mailtitle = $mailtitle;
	$title = $title;
	$text = $text;
	$mailbox = $mailbox;
} else {
	$mailapfs = "post";
	$mailtitle = addslashes(htmlspecialchars($_POST["mailtitle"]));
	$title = addslashes(htmlspecialchars($_POST["title"]));
	$text = addslashes(htmlspecialchars($_POST["text"]));
	$mailbox = addslashes(htmlspecialchars($_POST["mailbox"]));
	if ($mailtitle == "") {
		$arr = [["code" => "201", "msg" => "请传入邮件标题"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($title == "") {
		$arr = [["code" => "201", "msg" => "请传入邮件内标题"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($text == "") {
		$arr = [["code" => "201", "msg" => "请传入内容"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	if ($mailbox == "") {
		$arr = [["code" => "201", "msg" => "请传入收件箱"]];
		exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
}
$title = preg_replace_callback("/<a(?!.*\\bid\\b)[^>]*>(.*?)<\\/a>/i", function ($match) {
	return $match[1];
}, $title);
$text = preg_replace_callback("/<a(?!.*\\bid\\b)[^>]*>(.*?)<\\/a>/i", function ($match) {
	return $match[1];
}, $text);
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$arr = [["code" => "201", "msg" => "连接数据库失败"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
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
$variables = [];
$variables["wz_name"] = $wzname;
$variables["title"] = $title;
$variables["text"] = $text;
$variables["url"] = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];
$template = file_get_contents("../site/mailtemplate.php");
foreach ($variables as $key => $value) {
	$template = str_replace("{{ " . $key . " }}", $value, $template);
}
$template = $template;
$post_data = ["emydz" => $emydz, "emssl" => $emssl, "emduk" => $emduk, "key" => $emkey, "username" => $emfs, "fromuser" => $emfs, "fromname" => $emfszm, "title" => $mailtitle, "nr" => $template, "reveuser" => $mailbox];
session_start();
$url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/site/email/email.php";
$data = $post_data;
$headers = ["Authorization: Basic YWRtaW46YWRtaW4xMjMuLi4=", "Cookie: PHPSESSID=" . session_id(), "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36"];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($curl);
if (curl_errno($curl)) {
}
curl_close($curl);
if ($mailapfs != "nopost") {
	echo $result;
}
if ($xiangym == "ok") {
	echo $result;
}
