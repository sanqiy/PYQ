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
$page = addslashes(htmlspecialchars($_POST["page"]));
if ($page == "") {
	exit("参数为空!");
}
include "../config.php";
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
} else {
	include "../config.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		exit("连接数据库失败");
	}
	$user_zhsg = addslashes(htmlspecialchars($user_zh));
	$sqls = "select * from user where username='{$user_zhsg}'";
	$data_results = mysqli_query($conn, $sqls);
	$data2s_row = mysqli_fetch_array($data_results);
	$user_zh = $data2s_row["username"];
	$user_name = $data2s_row["name"];
	$user_img = $data2s_row["img"];
	$user_url = $data2s_row["url"];
}
session_start();
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$name = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$username = $row["username"];
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$kqsy = $row["kqsy"];
	$videoauplay = $row["videoauplay"];
}
$filterpiccir = "";
$sql = "SELECT text FROM configx WHERE title = 'piccir'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filterpiccir = $row["text"];
}
$sqly = "SELECT * FROM admin";
$result = $conn->query($sqly);
while ($row = $result->fetch_assoc()) {
	if ($row["topes"] == "") {
		$snk = "-201-201-1";
		break;
	}
	$ars = $row["topes"];
}
$ars = explode(PHP_EOL, $ars);
$dqgs = 0;
$dqs = 0;
$so = addslashes(htmlspecialchars($_POST["so"]));
if ($so != "" && $so != "null") {
	$sql = "SELECT * FROM essay where ptpaud<>'0' and ptpaud<>'-1' and ptpys<>'0' and ptptext LIKE '%{$so}%' order by id desc";
} else {
	$sql = "SELECT * FROM essay where ptpaud<>'0' and ptpaud<>'-1' and ptpys<>'0' order by id desc";
}
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$dqgs = $dqgs + 1;
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
	if ($ptpys != 1) {
	} else {
		if ($snk == "-201-201-1") {
		} else {
			if (in_array($wid, $ars)) {
				continue;
			}
		}
		if ($page < $dqgs) {
			$dqs = $dqs + 1;
			if ($dqs > 10) {
				break;
			}
			$imgar = explode("(+@+)", $ptpimag);
			$coun = count($imgar);
			if ($coun == 1) {
				$tusty = "grid-template-columns:1fr;width: 55%;";
			} else {
				if ($coun == 4 || $coun == 2) {
					$tusty = "grid-template-columns:1fr 1fr;width: 55%;";
				} else {
					$tusty = "grid-template-columns:1fr 1fr 1fr;";
				}
			}
			if ($ptpgg == 1) {
				$ggdiv = "display: flex;";
				$ggurl = "<div class=\"sh-content-right-ggurl\"><i class=\"iconfont icon-lianjie1 ri-sxwzgg\"></i><a href=\"" . $ptpggurl . "\" target=\"_blank\">进一步了解</a></div>";
				$gps = "";
			} else {
				$ggdiv = "display: none;";
				$ggurl = "";
				$gps = "<div class=\"sh-content-right-gps\"><a href=\"javascript:;\">" . $ptpdw . "</a></div>";
			}
			$time = strtotime($ptptime);
			$wzfbsj = ReckonTime($time);
			if ($ptpys == 1 && $ptpaud == 1) {
				$contenttext0 = preg_replace("/<img[^>]+>/i", "", $ptptext);
				$contenttext1 = preg_replace("/<span[^>]+>/i", "", $contenttext0);
				$contenttext = preg_replace("/<a href=[^>]+>/i", "", $contenttext1);
				if (iconv_strlen($contenttext, "UTF-8") > 100) {
					$ptptext = "<span class=\"wzndhycyc\" id=\"sh-content-qwdid-" . $cid . "\">" . $ptptext . "</span><a href=\"JavaScript:;\" class=\"sh-content-quanwenan\" id=\"sh-content-quanwenan-" . $cid . "\" lang=\"0\" onclick=\"quanwenan()\">全文</a>";
				} else {
					$ptptext = "<span>" . $ptptext . "</span>";
				}
				if ($ptpname == "匿名用户") {
					$arcuserurl = "";
				} else {
					$arcuserurl = "onclick=\"location.href='./archives.php?user=" . md5(md5($ptpuser)) . "'\"";
				}
				echo "
                    <div class=\"sh-content\"  id=\"sh-content-" . $cid . "\">
                <!-- 左边 -->
                <div class=\"sh-content-left\">
                    <!-- 头像 -->
                    <img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $ptpimg . "\" alt=\"头像\" " . $arcuserurl . ">
                </div>
                <!-- 右边 -->
                <div class=\"sh-content-right\">
                    <!-- 昵称与内容 -->
                    <div class=\"sh-content-right-head\">
                        <!-- 昵称 -->
                        <div class=\"sh-content-right-head-title\">
                            <p>" . $ptpname . "</p>
                            <div class=\"sh-content-right-head-title-ad\" style=\"" . $ggdiv . "\">
                                <p>广告</p>
                            </div>
                        </div>
                        <!-- 内容 -->
                        " . $ptptext;
				if ($ptplx == "only") {
					echo "<!-- 图片 -->";
				} elseif ($ptplx == "img") {
					if ($coun > "1") {
						$picimg = "style=\"width: 100%;\"";
					} else {
						$picimg = "";
					}
					echo "<!-- 图片 -->
                        <div class=\"sh-content-right-img\" id=\"imglib-" . $cid . "\" style=\"" . $tusty . "\">";
					for ($i = 0; $i < $coun; $i++) {
						$tuimg = $imgar[$i];
						if ($i > 7) {
							$duoimg = $coun - $i - 1;
							if ($coun > 9) {
								if ($i == 8) {
									echo "<a href=\"" . $tuimg . "\" class=\"sh-content-right-img-pic\" data-fancybox=\"gallery" . $cid . "\" data-caption=\"\">
                                             <span class=\"sh-content-right-img-pic-mask\">+" . $duoimg . "</span>
                                             <img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $tuimg . "\" alt=\"\" " . $picimg . ">
                                             </a>";
								} else {
									echo "<a href=\"" . $tuimg . "\" class=\"sh-content-right-img-pic\" data-fancybox=\"gallery" . $cid . "\" data-caption=\"\"  style=\"display:none;\">
                                             <img src=\"" . $tuimg . "\" data-src=\"" . $tuimg . "\" alt=\"\" " . $picimg . ">
                                             </a>";
								}
							} else {
								echo "<a href=\"" . $tuimg . "\" class=\"sh-content-right-img-pic\" data-fancybox=\"gallery" . $cid . "\" data-caption=\"\">
                                         <img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $tuimg . "\" alt=\"\" " . $picimg . ">
                                         </a>";
							}
						} else {
							echo "<a href=\"" . $tuimg . "\" class=\"sh-content-right-img-pic\" data-fancybox=\"gallery" . $cid . "\" data-caption=\"\">
                                     <img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $tuimg . "\" alt=\"\" " . $picimg . ">
                                     </a>";
						}
					}
					?></div><?php
				} elseif ($ptplx == "video") {
					if ($videoauplay == 1) {
						$videobf = "autoplay";
						$videobfplas = "";
					} else {
						$videobf = "";
						$videobfplas = "<i class=\"iconfont icon-sa4f56\" id=\"sh-content-video-videobfb-" . $cid . "\" style=\"width: fit-content;height: fit-content;grid-column: 1;grid-row: 1;z-index: 5;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-size: 40px;color: #ffffff;display: flex;cursor: pointer;padding: 15px;pointer-events: none;\"></i>";
					}
					if ($filterpiccir == 1) {
						$vidoyuan = "1";
					} else {
						$vidoyuan = "0";
					}
					echo "
                            <!-- 视频 -->
                        <div class=\"sh-video\" id=\"sh-content-video-" . $cid . "\" onclick=\"videofdgb()\" lang=\"0\">
                            <video name=\"sh-videokid\" class=\"sh-content-video\" data-ybs=\"" . $vidoyuan . "\" poster=\"" . $ptpvideofm . "\" id=\"sh-content-videok-" . $cid . "\" src=\"" . $ptpvideo . "\" playsinline webkit-playsinline preload=\"metadata\" " . $videobf . " muted loop onclick=\"videofd()\" lang=\"0\" disablePictureInPicture=\"true\" controlslist=\"nodownload nofullscreen noremoteplayback\"></video>
                            " . $videobfplas . "
                            <i class=\"iconfont icon-quxiao\" id=\"sh-content-videog-" . $cid . "\" lang=\"0\"></i>
                            <span class=\"sh-video-span\" id=\"sh-video-span-" . $cid . "\">MP4</span>
                        </div>
                            ";
				} elseif ($ptplx == "music") {
					echo "<!-- 音乐 -->";
					if (is_numeric($ptpmusic)) {
					} else {
						$mus = explode("|", $ptpmusic);
						include "../site/musicplay.php";
					}
				}
				?>

                    </div>
                    <!-- 地址 --><?php
				if ($ptpdw != "") {
					echo $gps;
				}
				echo "<!--广告-->";
				echo $ggurl;
				$sql2b = "select * from lcke where lwz= '{$cid}'";
				$result2b = mysqli_query($conn, $sql2b);
				if (mysqli_num_rows($result2b) > 0) {
					$sql2a = "SELECT * FROM lcke";
					$result2a = $conn->query($sql2a);
					while ($row2a = $result2a->fetch_assoc()) {
						if ($row2a["lwz"] == $cid) {
							$dianzmlnr = $row2a["luser"];
							if ($user_zh == "" || $user_passid == "") {
								if ($dianzmlnr == $_SESSION["visykmz_userip"]) {
									$dianzmlnr = "取消";
									$dianzmlimg = "iconfont icon-aixin2 ri-sxdzlikehs";
									break;
								} else {
									$dianzmlnr = "赞";
									$dianzmlimg = "iconfont icon-aixin ri-sxdzlike";
								}
							} else {
								if ($dianzmlnr == $user_zh) {
									$dianzmlnr = "取消";
									$dianzmlimg = "iconfont icon-aixin2 ri-sxdzlikehs";
									break;
								} else {
									$dianzmlnr = "赞";
									$dianzmlimg = "iconfont icon-aixin ri-sxdzlike";
								}
							}
						}
					}
				} else {
					$dianzmlnr = "赞";
					$dianzmlimg = "iconfont icon-aixin ri-sxdzlike";
				}
				if ($commauth == 1) {
					$gwzkplzt = "
    <div class=\"sh-content-right-time-right-left-y\" id=\"" . $cid . "\" onclick=\"plkkg()\">
                                    <i class=\"iconfont icon-pinglun2 ri-sxdzcomm\"></i>
                                    <span>评论</span>
                                </div>
    ";
				} else {
					$gwzkplzt = "
    <div class=\"sh-content-right-time-right-left-y\" id=\"" . $cid . "\">
                                    <i class=\"iconfont icon-pinglun2 ri-sxdzcomm\"></i>
                                    <span>评论关闭</span>
                                </div>
    ";
				}
				echo "
                    <!-- 时间与点赞 -->
                    <div class=\"sh-content-right-time\">
                        <!-- 时间 -->
                        <div class=\"sh-content-right-time-left\"><span>" . $wzfbsj . "</span></div>
                        <!-- 点赞 -->
                        <div class=\"sh-content-right-time-right\">
                            <!-- 左边合集 -->
                            <div class=\"sh-content-right-time-right-left\" id=\"pl-" . $cid . "\" name=\"pl\">
                                <div class=\"sh-content-right-time-right-left-z\" onclick=\"dinazan()\">
                                    <i class=\"" . $dianzmlimg . "\" id=\"tiezimg-" . $cid . "\"></i>
                                    <span id=\"tiezdz-" . $cid . "\">" . $dianzmlnr . "</span>
                                </div>
                                <p></p>
                                " . $gwzkplzt . "
                            </div>
                            <!-- 右边点赞控制按钮 -->
                            <div class=\"sh-content-right-time-right-right\" id=\"" . $cid . "\" onclick=\"plk()\">
                                <p class=\"zp1\"></p>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <!-- 点赞列表与评论 -->
                    <div class=\"sh-zanp\" id=\"zanss-" . $cid . "\">
                    ";
				$sql2 = "select * from lcke where lwz= '{$cid}'";
				$result2 = mysqli_query($conn, $sql2);
				if (mysqli_num_rows($result2) > 0) {
					echo "
    <!-- 点赞列表 -->
                        <div class=\"sh-zanp-zan\" id=\"zans-" . $cid . "\">
                            <!-- 左侧点赞图标 -->
                            <div class=\"sh-zanp-zan-left\"><!--img src=\"./assets/img/likel.svg\" alt=\"\"--><i class=\"iconfont icon-aixin ri-sxwzlike\"></i></div>
                            <!-- 右边点赞名列表 -->
                            <ul class=\"sh-zanp-zan-right\" id=\"zlbeh-" . $cid . "\">
                                ";
					$iw = 0;
					$dwys = 0;
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$dianzmys = $row2["luser"];
						if (strpos($dianzmys, "vis#-[") !== false || strpos($dianzmys, "]-#vis") !== false) {
							$dwys++;
						} else {
							$iw++;
							$dianzms = $row2["lname"];
							echo "<li id=\"zan-" . $cid . "\" lang=\"" . $dianzms . "\">" . $dianzms . "</li>";
						}
					}
					if ($dwys != 0) {
						echo "<li id=\"fkzan-" . $cid . "\">" . $dwys . "位访客</li>";
					}
					?>

     </ul>
    </div>
    <?php
				} else {
					echo "

    <!-- 点赞列表 -->
                        <div class=\"sh-zanp-zan\" id=\"zans-" . $cid . "\" style=\"display:none;\">
                            <!-- 左侧点赞图标 -->
                            <div class=\"sh-zanp-zan-left\"><i class=\"iconfont icon-aixin ri-sxwzlike\"></i></div>
                            <!-- 右边点赞名列表 -->
                            <ul class=\"sh-zanp-zan-right\" id=\"zlbeh-" . $cid . "\">


     </ul>
    </div>
    ";
				}
				echo "
                        <!-- 评论列表 -->";
				$sql3 = "select * from comm where wzcid= '{$cid}' and comaud<>'0' and comaud<>'-1'";
				$result3 = mysqli_query($conn, $sql3);
				$pls = 0;
				if (mysqli_num_rows($result3) > 0) {
					echo "<ul class=\"sh-zanp-pl\" id=\"sh-zanp-pl-" . $cid . "\">";
					while ($row3 = mysqli_fetch_assoc($result3)) {
						$pls = $pls + 1;
						if ($commgs < $pls) {
							$pls = $pls - 1;
							$plgd = "display:flex";
							break;
						} else {
							$plgd = "display:none";
						}
						$couser = $row3["couser"];
						$coname = $row3["coname"];
						$courl = $row3["courl"];
						$cotext = $row3["cotext"];
						$bcouser = $row3["bcouser"];
						$bconame = $row3["bconame"];
						$comaud = $row3["comaud"];
						if ($comaud != 1) {
							$cotext = "该条评论未通过审核!";
						}
						if ($courl == "") {
							$plzwze = "";
						} else {
							$plzwze = "href=\"" . $courl . "\" style=\"pointer-events: all;\"";
						}
						if ($commauth == 1) {
							$pldjhf = "onclick=\"plhuifu()\"";
						} else {
							$pldjhf = "";
						}
						if ($bcouser == "false" || $bconame == "false") {
							echo "
            <li lang=\"" . $coname . "\" " . $pldjhf . " id=\"" . $cid . "\" value=\"" . $couser . "\" data-comkzt=\"0\">
                                <div class=\"sh-zanp-pl-n\">
                                    <a " . $plzwze . " class=\"sh-zanp-pl-n-nc\" onclick=\"hfljurl()\" target=\"_blank\">" . $coname . "</a>：
                                    <span class=\"sh-zanp-pl-n-nr\">" . $cotext . "</span>
                                </div>
                            </li>
            ";
						} else {
							echo "
            <li lang=\"" . $coname . "\" " . $pldjhf . " id=\"" . $cid . "\" value=\"" . $couser . "\" data-comkzt=\"0\">
                                <div class=\"sh-zanp-pl-n\">
                                    <a " . $plzwze . " class=\"sh-zanp-pl-n-nc\" onclick=\"hfljurl()\" target=\"_blank\">" . $coname . "</a>
                                    <span>回复</span>
                                    <span class=\"sh-zanp-pl-n-nc\">" . $bconame . "</span>：
                                    <span class=\"sh-zanp-pl-n-nr\">" . $cotext . "</span>
                                </div>
                            </li>
            ";
						}
					}
					?></ul><?php
				} else {
					$plgd = "display:none";
					echo "<ul class=\"sh-zanp-pl\" id=\"sh-zanp-pl-" . $cid . "\" style=\"display:none;\"></ul>";
				}
				echo "

                        <!-- 显示更多评论 -->
                        <div class=\"sh-zanp-pl-ku\">
                        <a href=\"./view.php?cid=" . $cid . "\" target=\"_blank\" class=\"sh-zanp-pl-gd\" style=\"" . $plgd . "\">
                            <p class=\"zp1\"></p>
                            <p class=\"zp1\"></p>
                            <p></p>
                        </a>
                        </div>

                    </div>
                </div>
            </div>
                    ";
			}
		}
	}
}
$conn->close();
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$Query = "Select count(*) as AllNum from essay where ptpaud<>'0' and ptpaud<>'-1' and ptpys<>'0'";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$shujukts = $b["AllNum"];
$xyts = $dqgs;
if ($dqgs == $shujukts) {
	$dqgqsa = $shujukts;
} else {
	$dqgqsa = $dqgs - 1;
}
$conn->close();
echo "8965896582315263skfjskfjsgdfgdgddjskdjgdsdsjhd" . $dqgqsa . "fedfrtgg6h5j8u5j5d45hgd5s4fh5sd5gdf8w5dss48sd1fds56ds156";
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
