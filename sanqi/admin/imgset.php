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
<title>图像设置 - <?php echo $name;?></title>
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


        <?php
if ($filterupy != "") {
	$array = json_decode($filterupy, true);
	$bucketName = $array["bucketName"];
	$operatorName = $array["operatorName"];
	$operatorPassword = $array["operatorPassword"];
	$operatorurl = $array["operatorurl"];
}
?>        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">又拍云图像储存</div></header>
              <div class="card-body">
                 <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
	               <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">服务器名称</span>
                  </div>
                  <input type="text" class="form-control" name="upfm" placeholder="" value="<?php echo $bucketName;?>">
                </div>

                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">操作员账号</span>
                  </div>
                  <input type="text" class="form-control" name="upcz" placeholder="" value="<?php echo $operatorName;?>">
                </div>

                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">操作员密码</span>
                  </div>
                  <input type="text" class="form-control" name="upcm" placeholder="" value="<?php echo $operatorPassword;?>">
                </div>

                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">绑定的域名</span>
                  </div>
                  <input type="url" class="form-control" name="upby" placeholder="" value="<?php echo $operatorurl;?>">
                </div>
                <div class="input-group mb-3"><code>请填写又拍云绑定域名,注意http(s)://开头,最后不要加/</code></div>


                <input type="hidden" value="upyun" name="lx">

	               <button type="submit" class="btn btn-primary">保存</button>
                 </form>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">Favicon图标</div></header>
              <div class="card-body">
                 <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
	               <label for="exampleFormControlFile1" id="filem">请选择文件</label>
	               <div class="cupload-upload-box" style="margin-bottom: 10px;position: relative;display: flex;text-align: center;border: 1px dashed #e2e2e2;border-radius: 2px;box-sizing: border-box;flex: 1;height: 120px">
                            <span class="cupload-upload-btn" style="flex: 1;font-size: 28px;color: rgb(140, 147, 157);">
                                <i class="mdi mdi-cloud-upload" style="font-size: 50px;color: #009688;margin-bottom: -50px;"></i>
                                <p style="font-size: 14px;margin-top: -10px;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;word-wrap: break-word;word-break: break-all;">点击上传，或将文件拖拽到此处</p>
                            </span>
                            <input class="cupload-upload-input" type="file" multiple="" accept="image/*" title="" style="position: absolute; top: 0px; right: 0px; width: 100%; height: 100%; opacity: 0; cursor: pointer;" id="myFileInput" name="file">
                        </div>

	               <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">链接</span>
                  </div>
                  <input type="text" class="form-control" id="iconurl" name="iconurl" placeholder="输入icon图标链接" value="<?php echo $icon;?>">
                </div>
                <div class="input-group mb-3"><code>自定义浏览器favicon图标(优先级：链接 > 上传图片)</code></div>
                <input type="hidden" value="icon" name="lx">

	               <button type="submit" class="btn btn-primary">上传</button>
                 </form>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">Logo图标</div></header>
              <div class="card-body">
                 <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
	               <label for="exampleFormControlFile1" id="filem1">请选择文件</label>
	               <div class="cupload-upload-box" style="margin-bottom: 10px;position: relative;display: flex;text-align: center;border: 1px dashed #e2e2e2;border-radius: 2px;box-sizing: border-box;flex: 1;height: 120px">
                            <span class="cupload-upload-btn" style="flex: 1;font-size: 28px;color: rgb(140, 147, 157);">
                                <i class="mdi mdi-cloud-upload" style="font-size: 50px;color: #009688;margin-bottom: -50px;"></i>
                                <p style="font-size: 14px;margin-top: -10px;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;word-wrap: break-word;word-break: break-all;">点击上传，或将文件拖拽到此处</p>
                            </span>
                            <input class="cupload-upload-input" type="file" multiple="" accept="image/*" title="" style="position: absolute; top: 0px; right: 0px; width: 100%; height: 100%; opacity: 0; cursor: pointer;" id="myFileInput1" name="file">
                        </div>

	               <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">链接</span>
                  </div>
                  <input type="text" class="form-control" id="logourl" name="logourl" placeholder="输入Logo图标链接" value="<?php echo $logo;?>">
                </div>
                <div class="input-group mb-3"><code>自定义Logo图标(优先级：链接 > 上传图片)</code></div>
                <input type="hidden" value="logo" name="lx">

	               <button type="submit" class="btn btn-primary">上传</button>
                 </form>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">相册封面</div></header>
              <div class="card-body">
                 <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
	               <label for="exampleFormControlFile1" id="filem2">请选择文件</label>
	               <div class="cupload-upload-box" style="margin-bottom: 10px;position: relative;display: flex;text-align: center;border: 1px dashed #e2e2e2;border-radius: 2px;box-sizing: border-box;flex: 1;height: 120px">
                            <span class="cupload-upload-btn" style="flex: 1;font-size: 28px;color: rgb(140, 147, 157);">
                                <i class="mdi mdi-cloud-upload" style="font-size: 50px;color: #009688;margin-bottom: -50px;"></i>
                                <p style="font-size: 14px;margin-top: -10px;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;word-wrap: break-word;word-break: break-all;">点击上传，或将文件拖拽到此处</p>
                            </span>
                            <input class="cupload-upload-input" type="file" multiple="" accept="image/*" title="" style="position: absolute; top: 0px; right: 0px; width: 100%; height: 100%; opacity: 0; cursor: pointer;" id="myFileInput2" name="file">
                        </div>

	               <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">链接</span>
                  </div>
                  <input type="text" class="form-control" id="coverimgurl" name="coverimgurl" placeholder="输入相册封面链接" value="<?php echo $homimg;?>">
                </div>
                <div class="input-group mb-3"><code>自定义网站相册封面(优先级：链接 > 上传图片)</code></div>
                <input type="hidden" value="coverimg" name="lx">

	               <button type="submit" class="btn btn-primary">上传</button>
                 </form>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">网页背景图</div></header>
              <div class="card-body">
                 <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
	               <label for="exampleFormControlFile1" id="filem3">请选择文件</label>
	               <div class="cupload-upload-box" style="margin-bottom: 10px;position: relative;display: flex;text-align: center;border: 1px dashed #e2e2e2;border-radius: 2px;box-sizing: border-box;flex: 1;height: 120px">
                            <span class="cupload-upload-btn" style="flex: 1;font-size: 28px;color: rgb(140, 147, 157);">
                                <i class="mdi mdi-cloud-upload" style="font-size: 50px;color: #009688;margin-bottom: -50px;"></i>
                                <p style="font-size: 14px;margin-top: -10px;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;word-wrap: break-word;word-break: break-all;">点击上传，或将文件拖拽到此处</p>
                            </span>
                            <input class="cupload-upload-input" type="file" multiple="" accept="image/*" title="" style="position: absolute; top: 0px; right: 0px; width: 100%; height: 100%; opacity: 0; cursor: pointer;" id="myFileInput3" name="file">
                        </div>

                <input id="biaos" type="hidden" value="bobgsc" name="lx">

	               <button type="submit" class="btn btn-primary">上传</button>
	               <button type="submit" class="btn btn-outline-danger" onclick="bgde()">删除已有背景</button>
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
<script>
    function bgde(){
        document.getElementById("biaos").value="bobgde";
    }


    // 获取文件输入字段
    var fileInput = document.getElementById('myFileInput');
    // 监听 'change' 事件
    fileInput.addEventListener('change', function(event) {
        // 获取选中的文件
        var selectedFile = event.target.files[0];
        // 获取文件名
        var fileName = selectedFile ? selectedFile.name : '';
        document.getElementById("filem").innerText=fileName;
        document.getElementById("iconurl").value="";
    });
    // 获取文件输入字段
    var fileInput1 = document.getElementById('myFileInput1');
    // 监听 'change' 事件
    fileInput1.addEventListener('change', function(event) {
        // 获取选中的文件
        var selectedFile1 = event.target.files[0];
        // 获取文件名
        var fileName1 = selectedFile1 ? selectedFile1.name : '';
        document.getElementById("filem1").innerText=fileName1;
        document.getElementById("logourl").value="";
    });
    // 获取文件输入字段
    var fileInput2 = document.getElementById('myFileInput2');
    // 监听 'change' 事件
    fileInput2.addEventListener('change', function(event) {
        // 获取选中的文件
        var selectedFile2 = event.target.files[0];
        // 获取文件名
        var fileName2 = selectedFile2 ? selectedFile2.name : '';
        document.getElementById("filem2").innerText=fileName2;
        document.getElementById("coverimgurl").value="";
    });
    // 获取文件输入字段
    var fileInput3 = document.getElementById('myFileInput3');
    // 监听 'change' 事件
    fileInput3.addEventListener('change', function(event) {
        // 获取选中的文件
        var selectedFile3 = event.target.files[0];
        // 获取文件名
        var fileName3 = selectedFile3 ? selectedFile3.name : '';
        document.getElementById("filem3").innerText=fileName3;
    });
</script>
</body>
</html>
