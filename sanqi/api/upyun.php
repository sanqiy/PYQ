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
$filterupy = "";
$sql = "SELECT text FROM configx WHERE title = 'upyun'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filterupy = $row["text"];
}
if ($filterupy != "") {
	$array = json_decode($filterupy, true);
	$bucketName = $array["bucketName"];
	$operatorName = $array["operatorName"];
	$operatorPassword = $array["operatorPassword"];
	$operatorurl = $array["operatorurl"];
}
if ($bucketName == "" || $operatorName == "" || $operatorPassword == "" || $operatorurl == "") {
	$upy = 0;
} else {
	$bucketName = $bucketName;
	$operatorName = $operatorName;
	$operatorPassword = $operatorPassword;
	$uploadUrl = "http://v0.api.upyun.com/" . $bucketName;
	$cow = "/lan/";
	$imagePath = $newFile;
	$imageName = $upylu;
	$headers = ["Authorization: Basic " . base64_encode($operatorName . ":" . $operatorPassword), "Content-Type: application/octet-stream", "Expect:"];
	$handle = fopen($imagePath, "r");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uploadUrl . $cow . $imageName);
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_INFILE, $handle);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($imagePath));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	fclose($handle);
	if ($httpCode == 200) {
		array_push($arr, $operatorurl . $cow . $upylu);
		$upy = 1;
		if (file_exists($newFile)) {
			unlink($newFile);
		}
	} else {
		$upy = 0;
	}
}
