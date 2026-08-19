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
$servername = $_POST["servername"];
$username = $_POST["username"];
$password = $_POST["password"];
$dbname = $_POST["dbname"];
$adminame = $_POST["admin"];
if ($adminame == "") {
	exit("<script language=\"JavaScript\">;alert(\"管理员账号不能为空\");location.href=\"./index.php\";</script>");
}
if (strlen($adminame) < 5) {
	exit("<script language=\"JavaScript\">;alert(\"管理员账号不可低于5位\");location.href=\"./index.php\";</script>");
}
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$Y = "Y";
$m = "m";
$d = "d";
$H = "H";
$i = "i";
$s = "s";
$sj = date($Y . "-" . $m . "-" . $d . " " . $H . ":" . $i . ":" . $s);
$ip = $_SERVER["REMOTE_ADDR"];
$lx = 5;
$num = 32;
$gs = 1;
if ($lx == 1) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 2) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 3) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
} elseif ($lx == 4) {
	$strPol = "abcdefghijklmnopqrstuvwxyz";
} elseif ($lx == 5) {
	$strPol = "0123456789";
} elseif ($lx == 6) {
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
} elseif ($lx == 7) {
	$strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
} else {
	echo "{\"201\":\"错误的类型\"}";
	exit;
}
for ($x = 1; $x <= $gs; $x++) {
	$max = strlen($strPol) - 1;
	for ($i = 0; $i < $num; $i++) {
		$str .= $strPol[rand(0, $max)];
	}
	$str = $str;
}
$bdm = "admin";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name TEXT NOT NULL COMMENT '站点名称',
subtitle TEXT NOT NULL COMMENT '站点介绍',
icon TEXT NOT NULL COMMENT 'icon图标',
logo TEXT NOT NULL COMMENT 'logo图标',
zt TEXT NOT NULL COMMENT '站点状态 1开 0关',
username TEXT NOT NULL COMMENT '管理员账号',
homimg TEXT NOT NULL COMMENT '顶部背景图',
sign TEXT NOT NULL COMMENT '签名(不设置则为空)',
music TEXT NOT NULL COMMENT '网站音乐(-1为不设置)',
essgs TEXT NOT NULL COMMENT '文章输出数量',
commgs TEXT NOT NULL COMMENT '每篇文章的评论输出数量',
lnkzt TEXT NOT NULL COMMENT '是否开启友链显示 0=是 1=否',
regqx TEXT NOT NULL COMMENT '是否开启用户注册 0=开 -1=关',
kqsy TEXT NOT NULL COMMENT '是否开启所有用户发布权限(0=关 1=开)',
comaud TEXT NOT NULL COMMENT '评论是否需要审核(0=不需要 1=需要)',
ptpaud TEXT NOT NULL COMMENT '发布文章是否需要审核(0=不需要 1=需要)',
ptpfan TEXT NOT NULL COMMENT '是否显示前台发布文章按钮(0=不显示 1=显示)',
loginkg TEXT NOT NULL COMMENT '是否显示前台登录按钮(0=不显示 1=显示)',
notname TEXT NOT NULL COMMENT '是否开启匿名发布(0=关闭 1=开启)',
imgpres TEXT NOT NULL COMMENT '是否开启文章图片压缩(0=不压缩 1=压缩)',
rosdomain TEXT NOT NULL COMMENT '是否开启跨域资源(0=不开启 1=开启)',
daymode TEXT NOT NULL COMMENT '是否开启日夜模式(0=不开启 1=开启)',
gotop TEXT NOT NULL COMMENT '是否开启回到顶部(0=不开启 1=开启)',
search TEXT NOT NULL COMMENT '是否开启搜索功能(0=不开启 1=开启)',
videoauplay TEXT NOT NULL COMMENT '是否开启视频自动播放(0=不开启 1=开启)',
regverify TEXT NOT NULL COMMENT '是否开启注册验证邮箱(0=不开启 1=开启)',
pagepass TEXT NOT NULL COMMENT '网站访问密码 为空则无需密码',
emydz TEXT NOT NULL COMMENT '邮箱服务器地址',
emssl TEXT NOT NULL COMMENT '邮箱SSL加密方式',
emduk TEXT NOT NULL COMMENT 'SMTP端口',
emkey TEXT NOT NULL COMMENT '邮箱授权码',
emzh TEXT NOT NULL COMMENT 'SMTP账号',
emfs TEXT NOT NULL COMMENT '发送邮箱',
emfszm TEXT NOT NULL COMMENT '发送者名称',
date TEXT NOT NULL COMMENT '安装时间',
copyright TEXT NOT NULL COMMENT '版权所属',
beian TEXT NOT NULL COMMENT '备案号(没有则为空)',
topes TEXT NOT NULL COMMENT '置顶文文章',
scfont TEXT NOT NULL COMMENT '网站字体',
viscomm TEXT NOT NULL COMMENT '游客评论(-1=关 0=开)',
musplay TEXT NOT NULL COMMENT '悬浮音乐播放器样式'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$sql = "INSERT INTO {$bdm} (name, subtitle, icon, logo, zt,username,homimg,sign,music,essgs,commgs,lnkzt,regqx,kqsy,comaud,ptpaud,ptpfan,loginkg,notname,imgpres,rosdomain,daymode,gotop,search,videoauplay,regverify,pagepass,emydz,emssl,emduk,emkey,emzh,emfs,emfszm, date,copyright,beian,topes,scfont,viscomm,musplay)
VALUES ('sanqi', '分享生活，记录美好', './assets/img/favicon.png', './assets/img/logo.png', '1', '{$adminame}','./assets/img/homeimg.jpg','sanqi - 一个分享生活、与朋友互动的地方','-1','10','10','0','0','0','0','0','1','1','0','0','0','0','0','0','1','0','','smtp.qq.com','ssl','465', '','','','','{$sj}','sanqi','','','','-1','1')";
if ($conn->query($sql) === true) {
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
$bdm = "essay";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
ptpuser TEXT NOT NULL COMMENT '发布者账号',
ptpimg TEXT NOT NULL COMMENT '发布者头像',
ptpname TEXT NOT NULL COMMENT '发布者昵称',
ptptext TEXT NOT NULL COMMENT '文章内容',
ptpimag TEXT NOT NULL COMMENT '文章图片',
ptpvideo TEXT NOT NULL COMMENT '文章视频',
ptpmusic TEXT NOT NULL COMMENT '文章音乐',
ptplx TEXT NOT NULL COMMENT '文章类型(img=图文 video=视频 music=音乐 only=仅文字)',
ptpdw TEXT NOT NULL COMMENT '文章发布时定位',
ptptime TEXT NOT NULL COMMENT '文章发布时间',
ptpgg TEXT NOT NULL COMMENT '文章是否为广告(0=不是 1=是)',
ptpggurl TEXT NOT NULL COMMENT '广告跳转链接',
ptpys TEXT NOT NULL COMMENT '文章是否可见(0=不可见 1=可见)',
commauth TEXT NOT NULL COMMENT '是否允许评论(0=关 1=开)',
ptpaud TEXT NOT NULL COMMENT '审核状态(0=未审核 1=已审核)',
ip TEXT NOT NULL COMMENT '文章发布时的ip',
cid TEXT NOT NULL COMMENT '文章cid'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$sql = "INSERT INTO {$bdm} (ptpuser,ptpimg,ptpname,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,ip,cid)
VALUES ('{$adminame}','./assets/img/tx.png','管理员','欢迎使用sanqi！此文章由系统生成，您可以删除它，然后开始发布您的内容。','','','','only','','{$sj}','0','','1','1','1','{$ip}','{$str}')";
if ($conn->query($sql) === true) {
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
$bdm = "comm";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
couser TEXT NOT NULL COMMENT '评论者账号',
coimg TEXT NOT NULL COMMENT '评论者头像',
coname TEXT NOT NULL COMMENT '评论者昵称',
courl TEXT NOT NULL COMMENT '评论者网址',
cotext TEXT NOT NULL COMMENT '评论内容',
bcouser TEXT NOT NULL COMMENT '被评论者账号(没有被评论者则填false)',
bconame TEXT NOT NULL COMMENT '被评论者昵称(没有被评论者则填false)',
comaud TEXT NOT NULL COMMENT '评论审核状态(0=未审核 1=已审核)',
cotime TEXT NOT NULL COMMENT '评论时间',
ip TEXT NOT NULL COMMENT '评论ip',
wzcid TEXT NOT NULL COMMENT '评论所属文章',
ecid TEXT NOT NULL COMMENT '评论cid'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$sql = "INSERT INTO {$bdm} (couser,coimg,coname,courl,cotext,bcouser,bconame,comaud,cotime,ip,wzcid,ecid)
VALUES ('{$adminame}','./assets/img/tx.png','管理员','','嗨，这是一条由系统生成的评论，当您看到这句话代表您的程序已经安装成功，即刻开启您的分享之旅！','false','false','1','{$sj}','{$ip}','{$str}','{$str}')";
if ($conn->query($sql) === true) {
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
$lxx = 7;
$numx = 64;
$gsx = 1;
if ($lxx == 1) {
	$strPolx = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
} elseif ($lxx == 2) {
	$strPolx = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
} elseif ($lxx == 3) {
	$strPolx = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
} elseif ($lxx == 4) {
	$strPolx = "abcdefghijklmnopqrstuvwxyz";
} elseif ($lxx == 5) {
	$strPolx = "0123456789";
} elseif ($lxx == 6) {
	$strPolx = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
} elseif ($lxx == 7) {
	$strPolx = "0123456789abcdefghijklmnopqrstuvwxyz";
} else {
	echo "{\"201\":\"错误的类型\"}";
	exit;
}
for ($xx = 1; $xx <= $gsx; $xx++) {
	$maxx = strlen($strPolx) - 1;
	for ($i = 0; $i < $numx; $i++) {
		$strx .= $strPolx[rand(0, $maxx)];
	}
	$strx = $strx;
}
$bdm = "user";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
username TEXT NOT NULL COMMENT '账号',
password TEXT NOT NULL COMMENT '密码',
email TEXT NOT NULL COMMENT '邮箱',
name TEXT NOT NULL COMMENT '昵称',
img TEXT NOT NULL COMMENT '头像',
url TEXT NOT NULL COMMENT '网址',
homeimg TEXT NOT NULL COMMENT '主页背景图(-1则不设置)',
sign TEXT NOT NULL COMMENT '签名(不设置则为空)',
essqx TEXT NOT NULL COMMENT '是否拥有发布权限(0=否 1=是)',
esseam TEXT NOT NULL COMMENT '收到消息是否发送邮件通知(0=否 1=是)',
regtime TEXT NOT NULL COMMENT '注册时间',
regip TEXT NOT NULL COMMENT '注册ip',
logtime TEXT NOT NULL COMMENT '最后登录时间',
logip TEXT NOT NULL COMMENT '最后登录ip',
ban TEXT NOT NULL COMMENT '账号是否被封禁(0=正常 -1=封禁)',
bantime TEXT NOT NULL COMMENT '账号解封时间(true=永久封禁，否则填写解封日期)',
passid TEXT NOT NULL COMMENT '账号唯一id标识'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$yhmia = md5("123456");
$mrmzs = "用户" . $adminame;
$sql = "INSERT INTO {$bdm} (username,password,email,name,img,url,homeimg,sign,essqx,esseam,regtime,regip,logtime,logip,ban,bantime,passid)
VALUES ('{$adminame}','{$yhmia}','','{$mrmzs}','./assets/img/tx.png','','-1','sanqi - 一个分享生活、与朋友互动的地方','1','1','{$sj}','{$ip}','{$sj}','{$ip}','0','false','{$strx}')";
if ($conn->query($sql) === true) {
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
$bdm = "message";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
fuser TEXT NOT NULL COMMENT '发送者账号',
fimg TEXT NOT NULL COMMENT '发送者头像',
fname TEXT NOT NULL COMMENT '发送者昵称',
suser TEXT NOT NULL COMMENT '接收者账号',
title TEXT NOT NULL COMMENT '消息标题',
text TEXT NOT NULL COMMENT '消息内容',
ftime TEXT NOT NULL COMMENT '发送时间',
msg TEXT NOT NULL COMMENT '消息状态 0=未读 1=已读 -1=已删除'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$bdm = "lcke";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
luser TEXT NOT NULL COMMENT '点赞者账号',
lname TEXT NOT NULL COMMENT '点赞者昵称',
limg TEXT NOT NULL COMMENT '点赞者头像',
lwz TEXT NOT NULL COMMENT '点赞所属文章',
ltime TEXT NOT NULL COMMENT '点赞时间'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$bdm = "configx";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title TEXT NOT NULL COMMENT '配置名称',
text TEXT NOT NULL COMMENT '配置信息'
)";
if ($conn->query($sql) === true) {
} else {
	echo "创建数据表错误: " . $conn->error;
}
$bdm = "link";
$sql = "CREATE TABLE {$bdm} (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
url TEXT NOT NULL COMMENT '友链地址',
urls TEXT NOT NULL COMMENT '友链说明',
urlimg TEXT NOT NULL COMMENT '友链图标'
)";
if ($conn->query($sql) === true) {
	echo "安装成功,为了你的安全请手动删除install文件夹";
	$myfile = fopen("ins.bak", "w");
	$filename = "../config.php";
	$word = "<?php
\$servername = \"" . $servername . "\";//数据库地址
\$username = \"" . $username . "\";//数据库账号
\$password = \"" . $password . "\";//数据库密码
\$dbname = \"" . $dbname . "\";//数据库名
?>";
	$fh = fopen($filename, "w");
	fwrite($fh, $word);
	fclose($fh);
} else {
	echo "创建数据表错误: " . $conn->error;
}
$conn->close();
