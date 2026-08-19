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
<title>基础设置 - <?php echo $name;?></title>
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
              <header class="card-header"><div class="card-title">网站配置</div></header>
              <div class="card-body">

                <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站标题</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $name;?>" minlength="1" placeholder="" name="wzbt">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站描述</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $subtitle;?>" minlength="1" placeholder="可定义多个描述词以逗号分开" name="wzms">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">管理员账号</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $glyadmin;?>" minlength="5" maxlength="32" placeholder="" name="wzglyzh" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站首页签名</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $sign;?>" minlength="1" placeholder="" name="wzqm">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">默认文章显示数量</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" value="<?php echo $essgs;?>" minlength="1" maxlength="2"placeholder="" name="wzmrxw">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">默认评论显示数量</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" value="<?php echo $commgs;?>" minlength="1" maxlength="2"placeholder="" name="wzmrxp">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">备案号</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $beian;?>" maxlength="500" placeholder='示例:<a href="http://beian.miit.gov.cn/">蜀ICP备20220202号</a>' name="wzba">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站字体(TTF字体链接)</label>
                    <input type="url" class="form-control" id="exampleFormControlInput1" value="<?php echo $scfont;?>" maxlength="500" placeholder="" name="wzzit">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站版权</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $copyright;?>" maxlength="50" placeholder="" name="wzcopy">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站访问密码(不设置请留空)</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $pagepass;?>" maxlength="100" placeholder="" name="pagepass">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">敏感词过滤(用 || 符号分开)</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php
if ($filtertext != "") {
	$data = json_decode($filtertext, true);
	$result = implode("||", $data);
	echo $result;
}
?>" placeholder="" name="mask">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlSelect1">悬浮音乐播放器样式</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="wzmusplay">
                        <?php
if ($musplay == 0) {
	?><option value="0">经典</option>
                            <option value="1">新颖</option><?php
} elseif ($musplay == 1) {
	?><option value="1">新颖</option>
                            <option value="0">经典</option><?php
} else {
	?><option value="">设置错误,请重新设置</option><option value="0">经典</option>
                            <option value="1">新颖</option><?php
}
?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlSelect1">注册验证邮箱(需先配置邮箱)</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="regverify">
                        <?php
if ($regverify == 0) {
	?><option value="0">不验证</option>
                            <option value="1">验证</option><?php
} elseif ($regverify == 1) {
	?><option value="1">验证</option>
                            <option value="0">不验证</option><?php
} else {
	?><option value="">设置错误,请重新设置</option><option value="0">不验证</option>
                            <option value="1">验证</option><?php
}
?>                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">网站音乐(一行一个,不设置则留空)</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="" name="wzmusic" spellcheck="false"><?php echo $wzmusic;?></textarea>
                    <small class="help-block">直链音乐格式：<code>歌名|链接</code></small><br>
                    <small class="help-block">网易云音乐格式(请填写前缀:网易音乐+音乐id)：<code>网易音乐|25906124</code></small>
                  </div>

                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">自定义CSS</label>
                    <textarea class="form-control"  style="min-height:150px" id="exampleFormControlTextarea1" placeholder="" name="cucss" spellcheck="false"><?php echo $filtercucss;?></textarea>
                    <small class="help-block">请填写自定义CSS内容，不要填写style标签，请直接写代码</small>
                  </div>

                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">自定义JS</label>
                    <textarea class="form-control"  style="min-height:150px" id="exampleFormControlTextarea1" placeholder="" name="cujs" spellcheck="false"><?php echo $filtercujs;?></textarea>
                    <small class="help-block">请填写自定义JS内容，例如网站统计等，不要填写script标签，请直接写代码</small>
                  </div>

                  <input type="hidden" value="wzpz" name="lx">

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
<script type="text/javascript">
    function kgkz(){
        var element = event.target;
        var isChecked = element.checked;
        if(isChecked){
            console.log("选择")
            element.checked = true;
            var ele = window.event.srcElement.lang;
            var ele2 = window.event.srcElement.value;
            element.value = ele;
            element.lang = ele2;
        }else{
            console.log("未选择")
            element.checked = false;
            var ele = window.event.srcElement.lang;
            var ele2 = window.event.srcElement.value;
            element.value = ele;
            element.lang = ele2;
        }

    }
</script>
</body>
</html>
