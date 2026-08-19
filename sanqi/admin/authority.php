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
<title>权限设置 - <?php echo $name;?></title>
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
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-account-multiple"></i> <span>用户管理</span></a>
              <ul class="nav nav-subnav">
                <li> <a href="./userlist.php">用户列表</a> </li>
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



        <div class="row">
            <div class="col-lg-12">
            <div class="card">
              <div class="card-header"><div class="card-title">权限配置</div></div>
              <div class="card-body">
                <form action="./api/adminupdata.php" method="post" enctype="multipart/form-data" class="form-horizontal" autocomplete="off">

                    <div class="form-group">
                        <label><i class="mdi mdi-access-point-network" style="margin-right: 5px;"></i>站点开关</label>
                        <select class="form-control" name="develop_zt" <?php
if ($zt == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($zt == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($zt == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-account-plus" style="margin-right: 5px;"></i>用户注册</label>
                        <select class="form-control" name="develop_reg" <?php
if ($regqx == -1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($regqx == -1) {
	?><option value="-1">关闭</option>
                               <option value="0">开启</option><?php
} elseif ($regqx == 0) {
	?><option value="0">开启</option>
                                <option value="-1">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-account-arrow-left" style="margin-right: 5px;"></i>前台登录</label>
                        <select class="form-control" name="develop_qlogin" <?php
if ($loginkg == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($loginkg == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($loginkg == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-link-variant" style="margin-right: 5px;"></i>显示友链</label>
                        <select class="form-control" name="develop_lnk" <?php
if ($lnkzt == 1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($lnkzt == 0) {
	?><option value="0">开启</option>
                                <option value="1">关闭</option><?php
} elseif ($lnkzt == 1) {
	?><option value="1">关闭</option>
                               <option value="0">开启</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-telegram" style="margin-right: 5px;"></i>用户发布</label>
                        <select class="form-control" name="develop_fowz" <?php
if ($kqsy == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($kqsy == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($kqsy == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-feather" style="margin-right: 5px;"></i>前台发布</label>
                        <select class="form-control" name="develop_qfowz" <?php
if ($ptpfan == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($ptpfan == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($ptpfan == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-reddit" style="margin-right: 5px;"></i>匿名发布</label>
                        <select class="form-control" name="develop_nmfowz" <?php
if ($notname == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($notname == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($notname == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-rotate-orbit" style="margin-right: 5px;"></i>图片压缩</label>
                        <select class="form-control" name="develop_imgys" <?php
if ($imgpres == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($imgpres == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($imgpres == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-comment-processing" style="margin-right: 5px;"></i>游客评论</label>
                        <select class="form-control" name="develop_vispl" <?php
if ($viscomm == -1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($viscomm == -1) {
	?><option value="-1">关闭</option>
                               <option value="0">开启</option><?php
} elseif ($viscomm == 0) {
	?><option value="0">开启</option>
                                <option value="-1">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-comment-question" style="margin-right: 5px;"></i>评论审核</label>
                        <select class="form-control" name="develop_plsh" <?php
if ($comaud == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($comaud == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($comaud == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-script-text" style="margin-right: 5px;"></i>文章审核</label>
                        <select class="form-control" name="develop_wzsh" <?php
if ($ptpaud == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($ptpaud == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($ptpaud == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-dlna" style="margin-right: 5px;"></i>跨域资源</label>
                        <select class="form-control" name="develop_kyurl" <?php
if ($rosdomain == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($rosdomain == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($rosdomain == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-islam" style="margin-right: 5px;"></i>日夜模式</label>
                        <select class="form-control" name="develop_ryms" <?php
if ($daymode == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($daymode == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($daymode == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-arrow-up-bold-circle" style="margin-right: 5px;"></i>回到顶部</label>
                        <select class="form-control" name="develop_gotop" <?php
if ($gotop == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($gotop == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($gotop == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-magnify" style="margin-right: 5px;"></i>搜索功能</label>
                        <select class="form-control" name="develop_so" <?php
if ($search == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($search == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($search == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-adchoices" style="margin-right: 5px;"></i>视频自动播放</label>
                        <select class="form-control" name="develop_videoplay" <?php
if ($videoauplay == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($videoauplay == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($videoauplay == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="mdi mdi-image" style="margin-right: 5px;"></i>大图/视频圆角</label>
                        <select class="form-control" name="develop_piccir" <?php
if ($filterpiccir == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php
if ($filterpiccir == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php
} elseif ($filterpiccir == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php
}
?>                        </select>
                    </div>

                   <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode" style="color: #000000;">站点开关</label>
                      <div class="controls-box clearfix">
                          <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_1" name="develop_mode" class="custom-control-input" value="0" <--?php
                          if ($zt == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_1">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_2" name="develop_mode" class="custom-control-input" value="1" <-?php
                          if ($zt == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_2">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后网站将不能访问,后台管理不受影响</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode2" style="color: #000000;">用户注册</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_03" name="develop_mode2" class="custom-control-input" value="-1" <-?php
                          if ($regqx == -1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_03">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_04" name="develop_mode2" class="custom-control-input" value="0" <-?php
                          if ($regqx == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_04">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后新用户不可再注册账号</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode8" style="color: #000000;">前台登录</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_015" name="develop_mode8" class="custom-control-input" value="0" <-?php
                          if ($loginkg == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_015">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_016" name="develop_mode8" class="custom-control-input" value="1" <-?php
                          if ($loginkg == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_016">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后前台将不会显示登录按钮</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode1" style="color: #000000;">显示友链</label>
                      <div class="controls-box clearfix">
                          <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_01" name="develop_mode1" class="custom-control-input" value="1" <-?php
                          if ($lnkzt == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_01">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_02" name="develop_mode1" class="custom-control-input" value="0" <-?php
                          if ($lnkzt == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_02">开启</label>
      	              </div>

                      </div>
                      <code style="color: #838383">关闭后前台将不会显示友链列表</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode3" style="color: #000000;">用户发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_05" name="develop_mode3" class="custom-control-input" value="0" <-?php
                          if ($kqsy == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_05">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_06" name="develop_mode3" class="custom-control-input" value="1" <-?php
                          if ($kqsy == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_06">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后只有管理员才能发布文章</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode7" style="color: #000000;">前台发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_013" name="develop_mode7" class="custom-control-input" value="0" <-?php
                          if ($ptpfan == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_013">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_014" name="develop_mode7" class="custom-control-input" value="1" <-?php
                          if ($ptpfan == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_014">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后前台将不会显示发布文章按钮</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode9" style="color: #000000;">匿名发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_017" name="develop_mode9" class="custom-control-input" value="0" <-?php
                          if ($notname == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_017">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_018" name="develop_mode9" class="custom-control-input" value="1" <-?php
                          if ($notname == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_018">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后将不允许匿名发布文章</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode10" style="color: #000000;">图片压缩</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_021" name="develop_mode10" class="custom-control-input" value="0" <-?php
                          if ($imgpres == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_021">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_022" name="develop_mode10" class="custom-control-input" value="1" <-?php
                          if ($imgpres == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_022">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后发布文章时上传的图片将不会被压缩</code>
                    </div-->


                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode4" style="color: #000000;">游客评论</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_07" name="develop_mode4" class="custom-control-input" value="-1" <-?php
                          if ($viscomm == -1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_07">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_08" name="develop_mode4" class="custom-control-input" value="0" <-?php
                          if ($viscomm == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_08">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后只能登录账号进行发布评论</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode5" style="color: #000000;">评论审核</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_09" name="develop_mode5" class="custom-control-input" value="0" <-?php
                          if ($comaud == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_09">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_010" name="develop_mode5" class="custom-control-input" value="1" <-?php
                          if ($comaud == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_010">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后所有人都可随意发布评论</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 20px;">
                      <label for="develop_mode6" style="color: #000000;">文章审核</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_011" name="develop_mode6" class="custom-control-input" value="0" <-?php
                          if ($ptpaud == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_011">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_012" name="develop_mode6" class="custom-control-input" value="1" <-?php
                          if ($ptpaud == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_012">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后所有人都可随意发布文章</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode11" style="color: #000000;">跨域资源</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_023" name="develop_mode11" class="custom-control-input" value="0" <-?php
                          if ($rosdomain == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_023">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_024" name="develop_mode11" class="custom-control-input" value="1" <-?php
                          if ($rosdomain == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_024">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">用于加载一些有跨域限制、防盗链的远程资源链接,注意:当打开此功能时会出现 百度统计、cnzz等统计网站可能会导致失效，并且当七牛云设置了Referer防盗链且不为空时，将不可访问等,请按需求开启</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode12" style="color: #000000;">日夜模式</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_025" name="develop_mode12" class="custom-control-input" value="0" <-?php
                          if ($daymode == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_025">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_026" name="develop_mode12" class="custom-control-input" value="1" <-?php
                          if ($daymode == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_026">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后将不会在前台右下角悬浮显示日夜模式切换按钮</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode13" style="color: #000000;">回到顶部</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_027" name="develop_mode13" class="custom-control-input" value="0" <-?php
                          if ($gotop == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_027">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_028" name="develop_mode13" class="custom-control-input" value="1" <-?php
                          if ($gotop == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_028">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后将不会在前台右下角悬浮显示回到顶部按钮</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode14" style="color: #000000;">搜索功能</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_029" name="develop_mode14" class="custom-control-input" value="0" <-?php
                          if ($search == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_029">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_030" name="develop_mode14" class="custom-control-input" value="1" <-?php
                          if ($search == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_030">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后将不会显示搜索按钮</code>
                    </div-->

                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode15" style="color: #000000;">视频自动播放</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_031" name="develop_mode15" class="custom-control-input" value="0" <-?php
                          if ($videoauplay == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_031">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_032" name="develop_mode15" class="custom-control-input" value="1" <-?php
                          if ($videoauplay == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_032">开启</label>
      	              </div>
                      </div>
                      <code style="color: #838383">关闭后将不会自动播放文章中的视频</code>
                    </div-->

                    <input type="hidden" value="wzqxp" name="lx">

                    <button type="submit" class="btn btn-primary">保存</button>
                </form>
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
</body>
</html>
