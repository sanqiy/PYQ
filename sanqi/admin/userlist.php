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
if ($userdlzt == 0) {
	header("location: ./login.php");
	exit;
}
if ($user_zh == $glyadmin) {
	if ($user_passid != $passid) {
		exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"./login.php\";</script>;");
	}
} else {
	exit("<script language=\"JavaScript\">;alert(\"没有权限!\");location.href=\"../index.php\";</script>;");
}
?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . " ," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>用户列表 - <?php echo $name;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php
if (strpos($icon, "http") !== false) {
	echo $icon;
} else {
	echo "." . $icon;
}
?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="x-rim-auto-match" content="none">
<link rel="stylesheet" type="text/css" href="./assets/css/materialdesignicons.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/animate.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/style.min.css">
<link rel="stylesheet" type="text/css" href="../assets/mesg/dist/css/style.css">
</head>

<body>
<!--页面loading-->
<div id="lyear-preloader" class="loading">
  <div class="ctn-preloader">
    <div class="round_spinner">
      <div class="spinner"></div>
      <img src="<?php if(strpos($logo,"http")!==false){echo $logo;}else{echo ".".$logo;}?>" alt="">
    </div>
  </div>
</div>
<!--页面loading end-->
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--左侧导航-->
    <aside class="lyear-layout-sidebar">

      <!-- logo -->
      <div id="logo" class="sidebar-header">
        <a href="./index.php"><img src="<?php if(strpos($logo,"http")!==false){echo $logo;}else{echo ".".$logo;}?>" title="<?php echo $name;?>" alt="<?php echo $name;?>" /></a>
      </div>
      <div class="lyear-layout-sidebar-info lyear-scroll">

        <nav class="sidebar-main">
          <ul class="nav-drawer">
            <li class="nav-item"> <a href="index.php"><i class="mdi mdi-home"></i> <span>后台仪表盘</span></a> </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-wan"></i> <span>后台设置</span></a>
              <ul class="nav nav-subnav">
                <li> <a href="./basic.php">后台管理</a> </li>
                <li> <a href="./authority.php">权限管理</a> </li>
                <li> <a href="./imgset.php">图像管理</a> </li>
                <li> <a href="./emailset.php">邮箱管理</a> </li>
              </ul>
            </li>
            <li class="nav-item nav-item-has-subnav open active">
              <a href="javascript:void(0)"><i class="mdi mdi-account-multiple"></i> <span>用户管理</span></a>
              <ul class="nav nav-subnav">
                <li class="active"> <a href="./userlist.php">用户列表</a> </li>
              </ul>
            </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-stamper"></i> <span>审核中心</span>
              <?php
$Query = "Select count(*) as AllNum from essay WHERE ptpaud='0'";
$aes = mysqli_query($conn, $Query);
$escount = mysqli_fetch_assoc($aes);
$essl = $escount["AllNum"];
$Query = "Select count(*) as AllNum from comm WHERE comaud='0'";
$aco = mysqli_query($conn, $Query);
$cocount = mysqli_fetch_assoc($aco);
$cosl = $cocount["AllNum"];
$dshzl = $essl + $cosl;
echo "              ";
if ($dshzl != 0 && $dshzl != "") {
	echo "<span class=\"badge badge-danger\" style=\"margin-left: 10px;\">" . $dshzl . "</span>";
}
?>              </a>
              <ul class="nav nav-subnav">
                <li> <a href="./audites.php">审核文章<?php
if ($essl != 0 && $essl != "") {
	echo "<span class=\"badge badge-danger\" style=\"margin-left: 10px;\">" . $essl . "</span>";
}
?></a></li>
                <li> <a href="./auditco.php">审核评论<?php
if ($cosl != 0 && $cosl != "") {
	echo "<span class=\"badge badge-danger\" style=\"margin-left: 10px;\">" . $cosl . "</span>";
}
?></a></li>
              </ul>
            </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-link"></i> <span>友链管理</span></a>
              <ul class="nav nav-subnav">
                    <li> <a href="./linkset.php">友链列表</a> </li>
              </ul>
            </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-folder-open-outline"></i> <span>资源管理</span></a>
              <ul class="nav nav-subnav">
                <li> <a href="./rm.php">资源列表</a> </li>
                <!--li> <a href="./rmnew.php">新增资源</a> </li-->
              </ul>
            </li>
          </ul>
        </nav>

        <div class="sidebar-footer">
          <p class="copyright">Copyright &copy; <?php echo date("Y");?>. <a target="_blank" href="<?php echo $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];?>"><?php echo $glyname;?></a> All rights reserved.</p>
        </div>
      </div>

    </aside>
    <!--End 左侧导航-->

    <!--头部信息-->
    <header class="lyear-layout-header">

      <nav class="navbar">

        <div class="navbar-left">
          <div class="lyear-aside-toggler">
            <span class="lyear-toggler-bar"></span>
            <span class="lyear-toggler-bar"></span>
            <span class="lyear-toggler-bar"></span>
          </div>
        </div>

        <ul class="navbar-right d-flex align-items-center">
            <li onclick='window.location.href = "../edit.php"'>
                <span class="icon-item"><i class="mdi mdi-pencil-box-outline"></i></span>
		    </li>
          <!--切换主题配色-->
		  <li class="dropdown dropdown-skin">
		    <span data-toggle="dropdown" class="icon-item"><i class="mdi mdi-palette"></i></span>
			<ul class="dropdown-menu dropdown-menu-right" data-stopPropagation="true">
              <li class="drop-title"><p>主题</p></li>
              <li class="drop-skin-li clearfix">
                <span class="inverse">
                  <input type="radio" name="site_theme" value="default" id="site_theme_1" checked>
                  <label for="site_theme_1"></label>
                </span>
                <span>
                  <input type="radio" name="site_theme" value="dark" id="site_theme_2">
                  <label for="site_theme_2"></label>
                </span>
                <span>
                  <input type="radio" name="site_theme" value="translucent" id="site_theme_3">
                  <label for="site_theme_3"></label>
                </span>
              </li>
			  <li class="drop-title"><p>LOGO</p></li>
			  <li class="drop-skin-li clearfix">
                <span class="inverse">
                  <input type="radio" name="logo_bg" value="default" id="logo_bg_1" checked>
                  <label for="logo_bg_1"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_2" id="logo_bg_2">
                  <label for="logo_bg_2"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_3" id="logo_bg_3">
                  <label for="logo_bg_3"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_4" id="logo_bg_4">
                  <label for="logo_bg_4"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_5" id="logo_bg_5">
                  <label for="logo_bg_5"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_6" id="logo_bg_6">
                  <label for="logo_bg_6"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_7" id="logo_bg_7">
                  <label for="logo_bg_7"></label>
                </span>
                <span>
                  <input type="radio" name="logo_bg" value="color_8" id="logo_bg_8">
                  <label for="logo_bg_8"></label>
                </span>
			  </li>
			  <li class="drop-title"><p>头部</p></li>
			  <li class="drop-skin-li clearfix">
                <span class="inverse">
                  <input type="radio" name="header_bg" value="default" id="header_bg_1" checked>
                  <label for="header_bg_1"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_2" id="header_bg_2">
                  <label for="header_bg_2"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_3" id="header_bg_3">
                  <label for="header_bg_3"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_4" id="header_bg_4">
                  <label for="header_bg_4"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_5" id="header_bg_5">
                  <label for="header_bg_5"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_6" id="header_bg_6">
                  <label for="header_bg_6"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_7" id="header_bg_7">
                  <label for="header_bg_7"></label>
                </span>
                <span>
                  <input type="radio" name="header_bg" value="color_8" id="header_bg_8">
                  <label for="header_bg_8"></label>
                </span>
				</li>
			  <li class="drop-title"><p>侧边栏</p></li>
			  <li class="drop-skin-li clearfix">
                <span class="inverse">
                  <input type="radio" name="sidebar_bg" value="default" id="sidebar_bg_1" checked>
                  <label for="sidebar_bg_1"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_2" id="sidebar_bg_2">
                  <label for="sidebar_bg_2"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_3" id="sidebar_bg_3">
                  <label for="sidebar_bg_3"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_4" id="sidebar_bg_4">
                  <label for="sidebar_bg_4"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_5" id="sidebar_bg_5">
                  <label for="sidebar_bg_5"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_6" id="sidebar_bg_6">
                  <label for="sidebar_bg_6"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_7" id="sidebar_bg_7">
                  <label for="sidebar_bg_7"></label>
                </span>
                <span>
                  <input type="radio" name="sidebar_bg" value="color_8" id="sidebar_bg_8">
                  <label for="sidebar_bg_8"></label>
                </span>
			  </li>
		    </ul>
		  </li>
          <!--切换主题配色-->
          <li class="dropdown dropdown-profile">
            <a href="javascript:void(0)" data-toggle="dropdown" class="dropdown-toggle">
              <img class="img-avatar img-avatar-48 m-r-10" src="<?php
if (strpos($user_img, "http") !== false) {
	echo $user_img;
} else {
	echo "." . $user_img;
}
?>" alt="头像" />
              <span><?php echo $user_name;?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
              <li>
                <a class="dropdown-item" href="../index.php"><i class="mdi mdi-home-export-outline"></i> 回到首页</a>
              </li>
              <li class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="JavaScript:;" onclick="logut()"><i class="mdi mdi-logout-variant"></i> 退出登录</a>
              </li>
            </ul>
          </li>
        </ul>

      </nav>

    </header>
    <!--End 头部信息-->

    <!--页面主要内容-->
    <main class="lyear-layout-content">

      <div class="container-fluid p-t-15">

<?php
$useracc = addslashes(htmlspecialchars($_GET["useracc"]));
if ($useracc != "") {
	$sql = "select * from user where username= '{$useracc}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$user1_id = $row["id"];
			$user1_username = $row["username"];
			$user1_email = $row["email"];
			$user1_name = $row["name"];
			$user1_img = $row["img"];
			$user1_url = $row["url"];
			$user1_homeimg = $row["homeimg"];
			$user1_sign = $row["sign"];
			$user1_essqx = $row["essqx"];
			$user1_esseam = $row["esseam"];
			$user1_regtime = $row["regtime"];
			$user1_regip = $row["regip"];
			$user1_logtime = $row["logtime"];
			$user1_logip = $row["logip"];
			$user1_ban = $row["ban"];
			$user1_bantime = $row["bantime"];
			if ($user1_essqx == 1) {
				$fbwzqx = "<option value=\"1\">允许</option>
                         <option value=\"2\">特权</option>
                            <option value=\"0\">不允许</option>";
			} elseif ($user1_essqx == 2) {
				$fbwzqx = "<option value=\"2\">特权</option>
                         <option value=\"1\">允许</option>
                            <option value=\"0\">不允许</option>";
			} else {
				$fbwzqx = "<option value=\"0\">不允许</option>
                            <option value=\"1\">允许</option>
                            <option value=\"2\">特权</option>";
			}
			if ($user1_esseam == 1) {
				$esseamqx = "<option value=\"1\">接收</option>
                            <option value=\"0\">不接收</option>";
			} else {
				$esseamqx = "<option value=\"0\">不接收</option>
                            <option value=\"1\">接收</option>";
			}
			if ($user1_ban == -1) {
				if ($user1_bantime != "false") {
					if ($user1_bantime == "true") {
						$usfjts = "<code class=\"form-group\">该账号解封时间为：永久</code>";
					} else {
						$usfjts = "<code class=\"form-group\">该账号解封时间为：" . $user1_bantime . "</code>";
					}
				} else {
					$usfjts = "<code class=\"form-group\">该账号解封时间为：" . $user1_bantime . "</code>";
				}
				$uszhfjzt = "<option value=\"-1\">封禁</option>
                            <option value=\"0\">正常</option>";
			} else {
				$uszhfjzt = "<option value=\"0\">正常</option>
                            <option value=\"-1\">封禁</option>";
				$usfjts = "";
			}
			echo "<div class=\"row\">
          <div class=\"col-lg-12\">
            <div class=\"card\">
              <div class=\"card-header\"><div class=\"card-title\">编辑用户</div></div>
              <div class=\"card-body\">
                <form class=\"form-group\" action=\"./api/userinfo.php\" method=\"post\" enctype=\"multipart/form-data\">
                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputEmail4\">账号</label>
                      <input type=\"text\" class=\"form-control\" id=\"inputEmail40\" placeholder=\"请输入账号\" minlength=\"5\" value=\"" . $user1_username . "\" name=\"uuserzh\">
                    </div>
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputPassword4\">密码</label>
                      <input type=\"password\" class=\"form-control\" id=\"inputPassword41\" placeholder=\"请输入密码\" minlength=\"3\" value=\"\" name=\"upwd\">
                    </div>
                  </div>

                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputEmail4\">邮箱</label>
                      <input type=\"email\" class=\"form-control\" id=\"inputEmail42\" placeholder=\"请输入邮箱\" value=\"" . $user1_email . "\" name=\"uuserem\">
                    </div>
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputPassword4\">昵称</label>
                      <input type=\"text\" class=\"form-control\" id=\"inputPassword43\" placeholder=\"请输入昵称\" value=\"" . $user1_name . "\" name=\"uusername\">
                    </div>
                  </div>

                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputEmail4\">头像</label>
                      <input type=\"text\" class=\"form-control\" id=\"inputEmail44\" placeholder=\"请输入头像链接\" value=\"" . $user1_img . "\" name=\"uuserimg\">
                    </div>
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputPassword4\">网址</label>
                      <input type=\"url\" class=\"form-control\" id=\"inputPassword45\" placeholder=\"请输入网址\" value=\"" . $user1_url . "\" name=\"uuserwz\">
                    </div>
                  </div>

                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputEmail4\">封面</label>
                      <input type=\"text\" class=\"form-control\" id=\"inputEmail46\" placeholder=\"请输入封面链接(不设置请填-1)\" value=\"" . $user1_homeimg . "\" name=\"uuserbj\">
                    </div>
                    <div class=\"form-group col-md-6\">
                      <label for=\"inputPassword4\">签名</label>
                      <input type=\"text\" class=\"form-control\" id=\"inputPassword47\" placeholder=\"请输入签名\" value=\"" . $user1_sign . "\" name=\"uuserqm\">
                    </div>
                  </div>

                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                    <label for=\"exampleFormControlSelect1\">发布文章</label>
                    <select class=\"form-control\" id=\"exampleFormControlSelect1\" name=\"uuserqx\">
                      " . $fbwzqx . "
                    </select>
                  </div>
                    <div class=\"form-group col-md-6\">
                    <label for=\"exampleFormControlSelect12\">邮件通知</label>
                    <select class=\"form-control\" id=\"exampleFormControlSelect12\" name=\"uuseres\">
                      " . $esseamqx . "
                    </select>
                  </div>
                  </div>

                  <div class=\"input-group mb-2 mr-sm-2\" style=\"cursor: not-allowed;\">
                    <div class=\"input-group-prepend\">
                      <div class=\"input-group-text\">注册时间</div>
                    </div>
                    <input type=\"text\" class=\"form-control\" id=\"inlineFormInputGroupUsername2\" disabled placeholder=\"注册时间\" value=\"" . $user1_regtime . "\" style=\"cursor: not-allowed;\">
                  </div>
                  <div class=\"input-group mb-2 mr-sm-2\" style=\"cursor: not-allowed;\">
                    <div class=\"input-group-prepend\">
                      <div class=\"input-group-text\">注册时IP</div>
                    </div>
                    <input type=\"text\" class=\"form-control\" id=\"inlineFormInputGroupUsername3\" disabled placeholder=\"注册时IP\" value=\"" . $user1_regip . "\" style=\"cursor: not-allowed;\">
                  </div>
                  <div class=\"input-group mb-2 mr-sm-2\" style=\"cursor: not-allowed;\">
                    <div class=\"input-group-prepend\">
                      <div class=\"input-group-text\">最后登录</div>
                    </div>
                    <input type=\"text\" class=\"form-control\" id=\"inlineFormInputGroupUsername4\" disabled placeholder=\"最后登录\" value=\"" . $user1_logtime . "\" style=\"cursor: not-allowed;\">
                  </div>
                  <div class=\"input-group mb-2 mr-sm-2\" style=\"cursor: not-allowed;\">
                    <div class=\"input-group-prepend\">
                      <div class=\"input-group-text\">最后的IP</div>
                    </div>
                    <input type=\"text\" class=\"form-control\" id=\"inlineFormInputGroupUsername5\" disabled placeholder=\"最后的IP\" value=\"" . $user1_logip . "\" style=\"cursor: not-allowed;\">
                  </div>


                  <div class=\"form-row\">
                    <div class=\"form-group col-md-6\">
                    <label for=\"exampleFormControlSelect13\">账号封禁</label>
                    <select class=\"form-control\" id=\"exampleFormControlSelect13\" name=\"uuserban\">
                      " . $uszhfjzt . "
                    </select>
                  </div>
                    <div class=\"form-group col-md-6\">
                    <label for=\"exampleFormControlSelect14\">封禁时间</label>
                    <select class=\"form-control\" id=\"exampleFormControlSelect14\" name=\"uuserbantime\">
                      <option value=\"false\">正常</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+1 day")) . "\">1天</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+7 day")) . ">\">7天</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+15 day")) . "\">15天</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+30 day")) . "\">30天</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+180 day")) . "\">180天</option>
                            <option value=\"" . date("Y-m-d H:i", strtotime("+365 day")) . "\">1年</option>
                        <option value=\"true\">永久</option>
                    </select>
                  </div>
                  " . $usfjts . "
                  </div>

                  <input type=\"hidden\" value=\"userbj\" name=\"lx\" id=\"lx\">
                  <input type=\"hidden\" value=\"" . $user1_id . "\" name=\"userid\" id=\"userid\">

                  <button type=\"submit\" class=\"btn btn-primary\">更新</button>
                  <button type=\"submit\" class=\"btn btn-danger btn-primary\" id=\"deluser\" lang=\"" . $user1_id . "\">删除用户</button>
                </form>
              </div>
            </div>






          </div>





        </div>";
		}
	}
}
?>



        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-toolbar d-flex flex-column flex-md-row">
                <div class="toolbar-btn-action">
                  <a class="btn btn-success m-r-5 ajax-post confirm" href="JavaScript:;" onclick="idsx()" target-form="ids"><i class="mdi mdi-check"></i> 解封</a>
                  <a class="btn btn-warning m-r-5" href="JavaScript:;" onclick="idsxf()"><i class="mdi mdi-block-helper"></i> 封禁</a>
                </div>
              <form class="search-bar ml-md-auto" method="get" action="./userlist.php" role="form">
                  <!--input type="hidden" name="search_field" id="search-field" value="title"-->
                  <div class="input-group ml-md-auto">
                    <div class="input-group-prepend">
                        <?php
if (addslashes(htmlspecialchars($_GET["type"])) == "") {
	?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">所有用户</button>
                      <div class="dropdown-menu" style="">
                      <a class="dropdown-item" href="?type=0" data-field="title">正常用户</a>
                        <a class="dropdown-item" href="?type=-1" data-field="title">封禁用户</a>
                      </div><?php
} elseif (addslashes(htmlspecialchars($_GET["type"])) == "0") {
	?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">正常用户</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=-1" data-field="title">封禁用户</a>
                        <a class="dropdown-item" href="./userlist.php" data-field="title">所有用户</a>
                      </div><?php
} elseif (addslashes(htmlspecialchars($_GET["type"])) == "-1") {
	?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">封禁用户</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=0" data-field="title">正常用户</a>
                        <a class="dropdown-item" href="./userlist.php" data-field="title">所有用户</a>
                      </div><?php
}
?>
                    </div>
                    <input type="text" class="form-control" name="keyword" placeholder="账号/邮箱搜索">
                  </div>
                </form>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check-all">
                            <label class="custom-control-label" for="check-all"></label>
                          </div>
                        </th>
                        <th>ID</th>
                        <th>头像</th>
                        <th>账号</th>
                        <th>邮箱</th>
                        <th>昵称</th>
                        <th>账号状态</th>
                        <th>注册时间</th>
                        <th>注册IP</th>
                        <th>最后时间</th>
                        <th>最后IP</th>
                        <th>操作</th>
                      </tr>
                    </thead>
                    <tbody>

                        <?php
if (addslashes(htmlspecialchars($_GET["page"])) != "") {
	$page = $_GET["page"];
} else {
	$page = 1;
}
$perPage = 10;
$offset = ($page - 1) * $perPage;
$type = addslashes(htmlspecialchars($_GET["type"]));
$keyword = addslashes(htmlspecialchars($_GET["keyword"]));
if ($type == "") {
	$sql = "SELECT * FROM user order by id desc LIMIT {$perPage} OFFSET {$offset}";
} elseif ($type == "0") {
	$sql = "SELECT * FROM user WHERE ban = '0' order by id desc LIMIT {$perPage} OFFSET {$offset}";
} elseif ($type == "-1") {
	$sql = "SELECT * FROM user WHERE ban != '0' order by id desc LIMIT {$perPage} OFFSET {$offset}";
}
if ($keyword != "") {
	$sql = "SELECT * FROM user WHERE username LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%' ORDER BY id DESC";
}
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$user2_id = $row["id"];
		$user2_username = $row["username"];
		$user2_email = $row["email"];
		$user2_name = $row["name"];
		$user2_img = $row["img"];
		$user2_url = $row["url"];
		$user2_homeimg = $row["homeimg"];
		$user2_sign = $row["sign"];
		$user2_essqx = $row["essqx"];
		$user2_esseam = $row["esseam"];
		$user2_regtime = $row["regtime"];
		$user2_regip = $row["regip"];
		$user2_logtime = $row["logtime"];
		$user2_logip = $row["logip"];
		$user2_ban = $row["ban"];
		$user2_bantime = $row["bantime"];
		if ($user2_img == "./assets/img/tx.png") {
			$user2_img = "../../assets/img/tx.png";
		} else {
			if (strpos($user2_img, "./user/headimg/") !== false) {
				$user2_img = "../." . $user2_img;
			}
		}
		if ($user2_ban == -1) {
			$zhfjzt = "<span class=\"badge badge-danger\" style=\"margin-left: 5px;font-size: 12px;vertical-align: middle;\">封禁</span>";
		} else {
			$zhfjzt = "<span class=\"badge badge-success\" style=\"margin-left: 5px;font-size: 12px;vertical-align: middle;\">正常</span>";
		}
		echo "<tr>
                        <td>
                          <div class=\"custom-control custom-checkbox\">
                            <input type=\"checkbox\" class=\"custom-control-input ids\" name=\"ids[]\" value=\"" . $user2_id . "\" id=\"ids-" . $user2_id . "\">
                            <label class=\"custom-control-label\" for=\"ids-" . $user2_id . "\"></label>
                          </div>
                        </td>
                        <td>" . $user2_id . "</td>
                        <td><img class=\"img-avatar img-avatar-48 m-r-10\" src=\"" . $user2_img . "\"></td>
                        <td>" . $user2_username . "</td>
                        <td>" . $user2_email . "</td>
                        <td>" . $user2_name . "</td>
                        <td>" . $zhfjzt . "</td>
                        <td>" . $user2_regtime . "</td>
                        <td>" . $user2_regip . "</td>
                        <td>" . $user2_logtime . "</td>
                        <td>" . $user2_logip . "</td>
                        <td>
                          <div class=\"btn-group\">
                            <a class=\"btn btn-success m-r-5 ajax-post confirm\" href=\"?useracc=" . $user2_username . "\" target-form=\"ids\"><i class=\"mdi mdi-account-edit-outline\"></i> 编辑</a>
                          </div>
                        </td>
                      </tr>";
	}
}
?>
                    </tbody>
                  </table>
                </div>
                <ul class="pagination" style="margin-top:10px;">
                  <?php
$Query = "Select count(*) as AllNum from user";
$aus = mysqli_query($conn, $Query);
$bus = mysqli_fetch_assoc($aus);
echo "<div class=\"fixed-table-pagination callout\" style=\"width: 100%;display: flex;justify-content: space-between;\">
                  <div class=\"float-left pagination-detail\" style=\"margin-bottom: 10px;\">
                      <span class=\"pagination-info\">
                          每页显示" . $perPage . "位用户，共" . $bus["AllNum"] . "位用户
                      </span>
                  </div>
                                    <div class=\"float-right pagination\">";
$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc();
$totalRecords = $totalRecords["total"];
$maxPages = ceil($totalRecords / $perPage);
if ($maxPages > 1) {
	if ($page > 1) {
		?><li class="page-item"><a class="page-link" aria-label="首页" href="userlist.php?page=1">首页</a></li><?php
		echo "<li><a class=\"page-link\" aria-label=\"上一页\" href=\"userlist.php?page=" . ($page - 1) . "\">‹</a></li>";
	}
	$start = max(1, $page - 1);
	$end = min($page + 1, $maxPages);
	if ($start > 1) {
		?><li class="page-item disabled"><span class="page-link">...</span></li><?php
	}
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $page) {
			echo "<li class=\"page-item active\"><a class=\"page-link\" aria-label=\"" . $i . "\" href=\"userlist.php?page=" . $i . "\">" . $i . "</a></li>";
		} else {
			echo "<li class=\"page-item \"><a class=\"page-link\" aria-label=\"" . $i . "\" href=\"userlist.php?page=" . $i . "\">" . $i . "</a></li>";
		}
	}
	if ($end < $maxPages) {
		?><li class="page-item disabled"><span class="page-link">...</span></li><?php
	}
	if ($page < $maxPages) {
		echo "<li><a class=\"page-link\" aria-label=\"下一页\" href=\"userlist.php?page=" . ($page + 1) . "\">›</a></li>";
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"尾页\" href=\"userlist.php?page=" . $maxPages . "\">尾页</a></li>";
	}
}
?></div></div>                </ul>




              </div>
            </div>
          </div>



        </div>




      </div>

    </main>
    <!--End 页面主要内容-->
  </div>
</div>

<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<script type="text/javascript" src="./assets/js/popper.min.js"></script>
<script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="./assets/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="./assets/js/jquery.cookie.min.js"></script>
<script type="text/javascript" src="./assets/js/main.min.js"></script>
<script type="text/javascript" src="./assets/js/Chart.min.js"></script>
<script type="text/javascript" src="../assets/mesg/dist/js/sh-noytf.js"></script>
<script type="text/javascript">
if (document.getElementById('deluser')) {
    // 如果元素存在，则给它绑定事件
    document.getElementById('deluser').addEventListener('click', function() {
        if (confirm("这将删除该账号的所有数据且不可撤销,是否继续?")) {
            // 用户点击了确认按钮
            document.getElementById("lx").value="deluser";
        } else {
            // 用户点击了取消按钮或关闭了弹窗
            event.preventDefault(); // 阻止表单的默认提交行为
            return;
        }
    });
}

//解封
function idsx(){
    // 获取所有具有特定类名的checkbox元素
var checkboxes = document.querySelectorAll('.custom-control-input.ids');
// 创建一个空数组来存储被勾选元素的值
var selectedValues = [];
// 遍历所有checkbox元素
for (var i = 0; i < checkboxes.length; i++) {
    // 检查每个checkbox元素是否被勾选
    if (checkboxes[i].checked) {
        // 如果被勾选，则将该元素的值添加到selectedValues数组中
        selectedValues.push(checkboxes[i].value);
    }
}
// 将被勾选元素的值拼接为字符串，每个值用逗号分隔
var data = selectedValues.join(',');
if (data == "") {
    return;
}

loadpop("正在执行,请稍后",'ok');
// 创建一个XMLHttpRequest对象
var xhr = new XMLHttpRequest();
// 设置属性
xhr.open('post', './api/userinfo.php', true); // 注意这里设置为异步请求
// 设置请求头为application/x-www-form-urlencoded，以便能够通过POST请求发送数据
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
// 将数据通过send方法传递，注意这里将数据转换为键值对形式
xhr.send('ids=' + data+'&lx=0');
// 发送并处理返回值
xhr.onreadystatechange = function () {
    // 这步为判断服务器是否正确响应
    if (xhr.readyState == 4 && xhr.status == 200) {
        if (xhr.responseText == "") {
            warnpop("未获取到数据");
            return;
        }
        var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
        //console.log(obj);
        var code =obj[0].code;//获取第一个的值 状态码
        var msg=obj[0].msg;//获取返回内容昵称
        if (code == 200) {
            successpop(msg);
            location.reload();
        }else{
            warnpop(msg);
        }
    }
};
}

//封禁
function idsxf(){
    // 获取所有具有特定类名的checkbox元素
var checkboxes = document.querySelectorAll('.custom-control-input.ids');
// 创建一个空数组来存储被勾选元素的值
var selectedValues = [];
// 遍历所有checkbox元素
for (var i = 0; i < checkboxes.length; i++) {
    // 检查每个checkbox元素是否被勾选
    if (checkboxes[i].checked) {
        // 如果被勾选，则将该元素的值添加到selectedValues数组中
        selectedValues.push(checkboxes[i].value);
    }
}
// 将被勾选元素的值拼接为字符串，每个值用逗号分隔
var data = selectedValues.join(',');
if (data == "") {
    return;
}

loadpop("正在执行,请稍后",'ok');
// 创建一个XMLHttpRequest对象
var xhr = new XMLHttpRequest();
// 设置属性
xhr.open('post', './api/userinfo.php', true); // 注意这里设置为异步请求
// 设置请求头为application/x-www-form-urlencoded，以便能够通过POST请求发送数据
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
// 将数据通过send方法传递，注意这里将数据转换为键值对形式
xhr.send('ids=' + data+'&lx=-1');
// 发送并处理返回值
xhr.onreadystatechange = function () {
    // 这步为判断服务器是否正确响应
    if (xhr.readyState == 4 && xhr.status == 200) {
        if (xhr.responseText == "") {
            warnpop("未获取到数据");
            return;
        }
        var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
        //console.log(obj);
        var code =obj[0].code;//获取第一个的值 状态码
        var msg=obj[0].msg;//获取返回内容昵称
        if (code == 200) {
            successpop(msg);
            location.reload();
        }else{
            warnpop(msg);
        }
    }
};
}
</script>
</body>
</html>
