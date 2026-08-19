<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$iteace = "1";
if (is_file("../config.php")) {
	include "../config.php";
}
include "../api/wz.php";
if ($userdlzt == 1) {
	header("location: ./index.php");
	exit;
}
if ($user_zh == "" || $user_name == "" || $user_img == "" || $user_passid == "") {
	$userdlzt = "0";
} else {
	$userdlzt = "1";
	if ($user_zh == $glyadmin) {
		if ($user_passid != $passid) {
			exit("<script language=\"JavaScript\">alert(\"您账号登陆令牌已经失效请重新登陆！\");location.href=\"../index.php\";</script>");
		} else {
			header("Location: ./index.php");
			exit;
		}
	} else {
		exit("<script language=\"JavaScript\">alert(\"您的账号未获取后台权限哦！\");location.href=\"../index.php\";</script>");
	}
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . "," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>管理员登录 - <?php echo $name;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo strpos($icon, "http") !== false ? $icon : "." . $icon; ?>">
<link rel="stylesheet" type="text/css" href="./assets/css/materialdesignicons.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/animate.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/style.min.css">
<style>
body {background: linear-gradient(135deg, #0C0E10 0%, #446182 50%, #1a2a3a 100%);background-size: cover;background-position: center;height: 100vh;margin: 0;}
.login-form .has-feedback {position: relative;}
.login-form .has-feedback .form-control {padding-left: 36px;}
.login-form .has-feedback .mdi {position: absolute;top: 0;left: 0;width: 36px;height: 36px;line-height: 36px;z-index: 4;color: #dcdcdc;display: block;text-align: center;pointer-events: none;}
.login-form .has-feedback.row .mdi {left: 15px;}
.text-center-huge {font-size: 36px; font-weight: bold; text-align: center; margin-bottom: 20px; color: #ffffff;}
.card {background-color: rgba(255, 255, 255, 0.9); border-radius: 8px; padding: 50px; width: 420px; margin: auto; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);}
.centered-content {text-align: center; margin: 10px 0;color: #ffffff;}
.centered-content a {color: #ffffff;}
</style>
</head>
<body class="center-vh">
<div id="lyear-preloader" class="loading"><div class="ctn-preloader"><div class="round_spinner"><div class="spinner"></div><img src="<?php if(strpos($logo,"http")!==false){echo $logo;}else{echo ".".$logo;}?>" alt=""></div></div></div>
<div class="card card-shadowed">
  <div class="text-center-huge"><?php echo $name;?></div>
  <form action="./api/login.php" method="post" class="login-form" autocomplete="off">
    <div class="form-group has-feedback"><span class="mdi mdi-account" aria-hidden="true"></span><input type="text" class="form-control" id="username" placeholder="管理员账号" name="username" required="required" minlength="5" maxlength="32"></div>
    <div class="form-group has-feedback"><span class="mdi mdi-lock" aria-hidden="true"></span><input type="password" class="form-control" id="password" placeholder="密码" name="password" required="required" minlength="3" maxlength="16"></div>
    <div class="form-group"><button class="btn btn-block btn-primary" type="submit">登录后台</button></div>
  </form>
</div>
<div class="centered-content">
    &copy; <?php echo date("Y"); ?> <a target="_blank" href="<?php echo $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];?>"><?php echo $glyname;?></a> All rights reserved.
</div>
<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<script type="text/javascript" src="./assets/js/main.min.js"></script>
</body>
</html>
