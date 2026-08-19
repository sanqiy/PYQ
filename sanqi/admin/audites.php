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
<title>审核文章 - <?php echo $name;?></title>
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
<style>
    .card-footer{
        display: flex;
        justify-content: flex-start;
    }
</style>
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
            <li class="nav-item nav-item-has-subnav open active">
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
                <li class="active"> <a href="./audites.php">审核文章<?php
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
            <div class="card" style="background: #f4f5fa!important;box-shadow: 0 0px 0px rgb(0 0 0 / 0%);">
              <header class="card-header" style="background: #ffffff!important;margin-bottom: 20px;"><div class="card-title" onclick="window.location = './audites.php'" style="cursor:pointer;"><?php
if (addslashes(htmlspecialchars($_GET["type"])) == "" || addslashes(htmlspecialchars($_GET["type"])) == "0") {
	echo "待审核文章";
} else {
	if (addslashes(htmlspecialchars($_GET["type"])) == "all") {
		echo "已审核文章";
	}
}
?></div>
              <form class="search-bar ml-md-auto" method="get" action="./audites.php" role="form">
                  <!--input type="hidden" name="search_field" id="search-field" value="title"-->
                  <div class="input-group ml-md-auto">
                    <div class="input-group-prepend">
                        <?php
if (addslashes(htmlspecialchars($_GET["type"])) == "" || addslashes(htmlspecialchars($_GET["type"])) == "0") {
	?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">待审核文章</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=all" data-field="title">已审核文章</a>
                      </div><?php
} else {
	if (addslashes(htmlspecialchars($_GET["type"])) == "all") {
		?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">已审核文章</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=0" data-field="title">待审核文章</a>
                      </div><?php
	}
}
?>
                    </div>
                    <input type="text" class="form-control" name="keyword" placeholder="关键字搜索">
                  </div>
                </form>
              </header>


        <div class="card-columns">

            <?php
if (addslashes(htmlspecialchars($_GET["page"])) != "") {
	$page = $_GET["page"];
} else {
	$page = 1;
}
$perPage = 20;
$offset = ($page - 1) * $perPage;
if (addslashes(htmlspecialchars($_GET["type"])) == "" || addslashes(htmlspecialchars($_GET["type"])) == "0") {
	$sql = "SELECT * FROM essay where ptpaud= '0' order by id desc LIMIT {$perPage} OFFSET {$offset}";
	$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM essay where ptpaud= '0'")->fetch_assoc();
} else {
	if (addslashes(htmlspecialchars($_GET["type"])) == "all") {
		$sql = "SELECT * FROM essay where ptpaud= '1' order by id desc LIMIT {$perPage} OFFSET {$offset}";
		$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM essay where ptpaud= '1'")->fetch_assoc();
	}
}
$keyword = addslashes(htmlspecialchars($_GET["keyword"]));
if ($keyword != "") {
	$sql = "SELECT * FROM essay where ptpaud= '1' and ptptext LIKE '%{$keyword}%' order by id desc";
}
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$ptpuser = $row["ptpuser"];
		$ptpimg = $row["ptpimg"];
		$ptpname = $row["ptpname"];
		$ptptext = $row["ptptext"];
		$ptpimag = $row["ptpimag"];
		$ptpvideo = $row["ptpvideo"];
		$ptpmusic = $row["ptpmusic"];
		$ptplx = $row["ptplx"];
		$ptpdw = $row["ptpdw"];
		$ptptime = $row["ptptime"];
		$ptpgg = $row["ptpgg"];
		$ptpggurl = $row["ptpggurl"];
		$ptpys = $row["ptpys"];
		$commauth = $row["commauth"];
		$ptpaud = $row["ptpaud"];
		$ptpip = $row["ip"];
		$cid = $row["cid"];
		$wid = $row["id"];
		$partssp = explode("|", $ptpvideo);
		$ptpvideo = $partssp[0];
		$ptpvideofm = $partssp[1];
		if ($ptpimg == "./assets/img/tx.png") {
			$ptpimg = "../../assets/img/tx.png";
		} else {
			if (strpos($ptpimg, "./user/headimg/") !== false) {
				$ptpimg = "../." . $ptpimg;
			}
		}
		$ptptext = str_replace("./assets/owo/paopao/", "../../assets/owo/paopao/", $ptptext, $count);
		$ptptext = $ptptext . PHP_EOL;
		$ptptext = str_replace("src=\"./assets/img/thumbnail.svg\"", "", $ptptext, $count);
		$ptptext = $ptptext . PHP_EOL;
		$ptptext = str_replace("data-src=\"", "src=\"", $ptptext, $count);
		$ptptext = $ptptext . PHP_EOL;
		$ptptext = str_replace("<img ", "<img style=\"width: 24px;height: 24px;\" ", $ptptext, $count);
		$ptptext = $ptptext . PHP_EOL;
		if ($ptpgg == 1) {
			$ggdiv = "<code style=\"margin-left: 10px;font-size: 12px;\">广告</code>";
		} else {
			$ggdiv = "";
		}
		$time = strtotime($ptptime);
		$wzfbsj = ReckonTime($time);
		echo "
                    <div class=\"col-sm-12\" style=\"padding: 0px;\" id=\"sh-content-" . $cid . "\">
            <div class=\"card\">
              <header class=\"card-header\">
                <div class=\"card-title\"><img class=\"img-avatar img-avatar-48 m-r-10 rounded\" alt=\"48x48\" src=\"" . $ptpimg . "\" style=\"object-fit: cover;\" data-holder-rendered=\"true\">" . $ptpname . $ggdiv . "</div>
                <ul class=\"card-actions\">
                  <li class=\"dropdown show\">" . $wzfbsj . "</li>
                </ul>
              </header>
              <div class=\"card-body\">
                <p>" . $ptptext . "</p>
                <div class=\"border-example-border-utils\">
                  ";
		if ($ptplx == "img") {
			$imgar = explode("(+@+)", $ptpimag);
			$coun = count($imgar);
			for ($i = 0; $i < $coun; $i++) {
				if (strpos($imgar[$i], "./upload/") !== false) {
					$tuimg = "../." . $imgar[$i];
					if (strpos($imgar[$i], "../upload/") !== false) {
						$tuimg = "../" . $imgar[$i];
					}
				} else {
					$tuimg = $imgar[$i];
				}
				echo "<img src=\"" . $tuimg . "\" width=\"70\" alt=\"图片\" class=\"rounded-sm\" style=\"width:70px;height: 100vw;max-height: 100px;object-fit: cover;margin-bottom: 10px;margin-right: 5px;\">";
			}
		} elseif ($ptplx == "video") {
			echo "<div class=\"sh-video\">
                            <iframe src=\"../site/library/player.php?url=" . $ptpvideo . "\" allowfullscreen=\"allowfullscreen\" mozallowfullscreen=\"mozallowfullscreen\" msallowfullscreen=\"msallowfullscreen\" oallowfullscreen=\"oallowfullscreen\" webkitallowfullscreen=\"webkitallowfullscreen\" frameBorder=\"\" style=\"width: fit-content;max-width: 100%;height:100vw;min-height:200px;max-height:400px;border-radius: 4px;\"></iframe>
                        </div>";
		} elseif ($ptplx == "music") {
			if (is_numeric($ptpmusic)) {
				?><span>网易云音乐播放器已取消</span><?php
			} else {
				$mus = explode("|", $ptpmusic);
				echo "<audio controls style=\"width: 100%;\">
                              <source src=\"" . $mus[0] . "\" type=\"audio/mp3\">
                              </audio>";
			}
		}
		echo "
                </div>
              </div>
              <footer class=\"card-footer text-right\">
                <button class=\"btn btn-label btn-danger\" style=\"margin-top: 5px;\" lang=\"" . $cid . "\" onclick=\"scwzsg()\"><label><i class=\"mdi mdi-close\"></i></label> 驳回</button>";
		if (addslashes(htmlspecialchars($_GET["type"])) != "all") {
			if (addslashes(htmlspecialchars($_GET["keyword"])) == "") {
				echo "<button class=\"btn btn-label btn-primary\" style=\"margin-top: 5px;margin-left: 5px;\" lang=\"" . $cid . "\" onclick=\"tgwzsg()\"><label><i class=\"mdi mdi-checkbox-marked-circle-outline\"></i></label> 通过</button>";
			}
		}
		?>

              </footer>
            </div>
          </div><?php
	}
}
$totalRecords = $totalRecords["total"];
$maxPages = ceil($totalRecords / $perPage);
echo "<div class=\"fixed-table-pagination callout\" style=\"background-color: rgb(255 255 255 / 0%);\">
                  <div class=\"float-left pagination-detail\" style=\"margin-bottom: 10px;\">
                      <span class=\"pagination-info\">
                          每页显示" . $perPage . "条数据，总共" . $totalRecords . "条数据
                      </span>
                  </div>
                                    <div class=\"float-right pagination\">
                      <ul class=\"pagination\">";
if (addslashes(htmlspecialchars($_GET["type"])) != "") {
	$typez = "type=" . addslashes(htmlspecialchars($_GET["type"])) . "&";
} else {
	$typez = "";
}
if ($maxPages > 1) {
	if ($page > 1) {
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"首页\" href=\"./audites.php?" . $typez . "page=1\">首页</a></li>";
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"上一页\" href=\"./audites.php?" . $typez . "page=" . ($page - 1) . "\">‹</a></li>";
	}
	$start = max(1, $page - 1);
	$end = min($page + 1, $maxPages);
	if ($start > 1) {
	}
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $page) {
			echo "<li class=\"page-item active\"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./audites.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		} else {
			echo "<li class=\"page-item \"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./audites.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		}
	}
	if ($end < $maxPages) {
		?><li class="page-item disabled"><span class="page-link">...</span></li><?php
	}
	if ($page < $maxPages) {
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"下一页\" href=\"./audites.php?" . $typez . "page=" . ($page + 1) . "\">›</a></li>";
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"尾页\" href=\"./audites.php?" . $typez . "page=" . $maxPages . "\">尾页</a></li>";
	}
}
?>

                      </ul>
                  </div>
            </div>



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
//审核文章事件
function tgwzsg(){
    var ele = window.event.srcElement.lang;
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/escoaud.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('ztm=shwz&cs='+ele);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                alert("未获取到数据!");
                return;
            }
            //alert(xhr.responseText);
            if (xhr.responseText == "200") {
                var x = document.getElementById("sh-content-"+ele);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                }
            }else{
                alert(xhr.responseText);
            }
        }
    };
}


//驳回文章事件
function scwzsg(){
    var ele = window.event.srcElement.lang;
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/escoaud.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('ztm=bhwz&cs='+ele);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                alert("未获取到数据!");
                return;
            }
            //alert(xhr.responseText);
            if (xhr.responseText == "200") {
                var x = document.getElementById("sh-content-"+ele);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                }
            }else{
                alert(xhr.responseText);
            }
        }
    };
}

</script>
</body>
</html><?php
function ReckonTime($time)
{
	$NowTime = time();
	if ($NowTime < $time) {
		return false;
	}
	$TimePoor = $NowTime - $time;
	if ($TimePoor == 0) {
		$str = "一眨眼之间";
	} elseif ($TimePoor < 60 && $TimePoor > 0) {
		$str = $TimePoor . "秒之前";
	} elseif ($TimePoor >= 60 && $TimePoor <= 3600) {
		$str = floor($TimePoor / 60) . "分钟前";
	} elseif ($TimePoor > 3600 && $TimePoor <= 86400) {
		$str = floor($TimePoor / 3600) . "小时前";
	} elseif ($TimePoor > 86400 && $TimePoor <= 604800) {
		if (floor($TimePoor / 86400) == 1) {
			$str = "昨天";
		} elseif (floor($TimePoor / 86400) == 2) {
			$str = "前天";
		} else {
			$str = floor($TimePoor / 86400) . "天前";
		}
	} elseif ($TimePoor > 604800) {
		$str = date("Y-m-d", $time);
	}
	return $str;
}
