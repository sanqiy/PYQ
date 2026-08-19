<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$condition = addslashes(htmlspecialchars($_GET["condition"]));
if ($condition != "error") {
	exit("<script language=\"JavaScript\">;alert(\"非法请求!\");location.href=\"../index.php\";;</script>");
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
<title>授权验证失败 - <?php echo $name;?></title>
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
<style>
.lyear-layout-content2{
    padding: 50px 200px;
}

@media screen and (max-width:1210px) {
    .lyear-layout-content2{
        padding: 50px 60px;
}
@media screen and (max-width:960px) {
    .lyear-layout-content2{
        padding: 50px 10px;
}


}
</style>
</head>

<body class="lyear-layout-sidebar-close">
<!--页面loading-->
<div id="lyear-preloader" class="loading">
  <div class="ctn-preloader">
    <div class="round_spinner">
      <div class="spinner"></div>
      <img src="<?php
if (strpos($logo, "http") !== false) {
	echo $logo;
} else {
	echo "." . $logo;
}
?>" alt="">
    </div>
  </div>
</div>
<!--页面loading end-->
<div class="lyear-layout-web">
  <div class="lyear-layout-container">


    <!--页面主要内容-->
    <main class="lyear-layout-content2">

      <div class="container-fluid p-t-15">

        <div class="row">

          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title" style="color: #ff0000;">授权验证失败</div></header>
              <div class="card-body">

                <p>系统未获取到您的域名授权信息!</p>
                <div class="jumbotron">
                  <h1 class="display-4" style="color: #ff0000;">授权错误!</h1>
                  <p class="lead">您的域名：<code><?php echo $_SERVER["SERVER_NAME"];?></code>&nbsp;&nbsp;授权信息验证失败,系统未获取到您的授权信息,请稍后再试!</p>
                  <hr class="my-4">
                  <p>系统未获取到您的授权信息,可能有以下原因:<br>1.网络问题,未成功连接到授权服务器。<br>2.授权服务器故障,请过段时间再尝试。<br>3.您的授权信息被封禁,请联系作者咨询原因。<br>4.您的授权信息被删除,请联系作者咨询原因。<br>5.您尚未购买授权,请在购买后使用。<br></p>
                  <a class="btn btn-primary btn-lg" href="http://www.qemao.com" target="_blank" role="button" style="margin-top: 5px;">前往授权</a>

                </div>


                <p class="copyright">时间：<?php echo date("Y-m-d H:i:s");?></p>
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
    function aujh(){
        document.getElementById("my-4-fg").style.display="block";
        document.getElementById("my-4-fg-nr").style.display="block";
    }
</script>
</body>
</html>
