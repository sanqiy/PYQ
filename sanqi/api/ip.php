<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$ip = $_GET["ip"];
if ($ip != "ok") {
	exit("错误的指令");
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
get_ip_city($ip);
function get_ip_city($ip)
{
	$ch = curl_init();
	$url = "https://whois.pconline.com.cn/ipJson.jsp?id=6&ip=" . $ip;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$location = curl_exec($ch);
	curl_close($ch);
	$location = mb_convert_encoding($location, "utf-8", "GB2312");
	$location = substr($location, 2 + strpos($location, "({"), (strlen($location) - strpos($location, "})")) * -1);
	$location = str_replace("\"", "", str_replace(":", "=", str_replace(",", "&", $location)));
	parse_str($location, $ip_location);
	$region = $ip_location["pro"];
	$city = $ip_location["city"];
	$addr = $ip_location["addr"];
	if ($region == "" && $city == "" && $addr == "") {
		$arr = [["code" => "201", "ipcd" => $ip, "region" => "", "city" => "", "addr" => ""]];
	} else {
		if ($region == null && $city == null && $addr == null) {
			$arr = [["code" => "201", "ipcd" => $ip, "region" => "", "city" => "", "addr" => ""]];
		} else {
			$arr = [["code" => "200", "ipcd" => $ip, "region" => $region, "city" => $city, "addr" => $addr]];
		}
	}
	echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
