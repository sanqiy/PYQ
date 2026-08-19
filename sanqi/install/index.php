<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

$file = "ins.bak";
if (file_exists($file)) {
	?>你已经安装过了!若您是首次使用请先进行安装,删除文件夹 <span style="color: red;">[install/]</span> 下的 <span style="color: red;">[ins.bak]</span> 文件后进行安装。<?php
	exit;
}
$iteace = 2;
if (is_file("../config.php")) {
	include "../config.php";
}
?><!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>安装程序 - sanqi</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/ico">
<meta name="keywords" content="安装程序 - sanqi">
<meta name="description" content="安装程序 - sanqi">
<meta name="author" content="yinqi">
<link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../admin/assets/css/materialdesignicons.min.css" rel="stylesheet">
<link href="../admin/assets/css/style.min.css" rel="stylesheet">
</head>
<body>
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--页面主要内容-->
    <main class="lyear-layout-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-10">
            <div class="card">
              <div class="card-header"><h4>sanqi 安装程序</h4></div>
              <div class="card-body">

                <form class="form-horizontal" action="install.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label class="col-xs-12" for="example-text-input">数据库地址</label>
                    <div class="col-xs-12">
                      <input class="form-control" type="text" id="example-text-input" name="servername" placeholder="" value="localhost" required="required">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-xs-12" for="example-text-input">数据库名</label>
                    <div class="col-xs-12">
                      <input class="form-control" type="text" id="example-text-input" name="dbname" placeholder="" required="required">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-xs-12" for="example-text-input">数据库账号</label>
                    <div class="col-xs-12">
                      <input class="form-control" type="text" id="example-text-input" name="username" placeholder="" required="required">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-xs-12" for="example-text-input">数据库密码</label>
                    <div class="col-xs-12">
                      <input class="form-control" type="text" id="example-text-input" name="password" placeholder="" required="required">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-xs-12" for="example-disabled-input">设置管理员账号</label>
                    <div class="col-xs-12">
                      <input class="form-control" type="text" id="example-disabled-input" name="admin" placeholder="默认密码:123456" required="required" minlength="5" maxlength="32" onKeyUp="value=value.replace(/[\W]/g,'')">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12">
                      <button class="btn btn-primary" type="submit">立即安装</button>
                    </div>
                  </div>
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
</body>
</html>
