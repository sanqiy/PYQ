<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

if (file_exists("./config.php")) {
	include "./config.php";
} else {
	header("Location:../index.php");
}
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
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
$filterpiccir = "";
$sql = "SELECT text FROM configx WHERE title = 'piccir'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filterpiccir = $row["text"];
}
if ($snk == "-201-201-1") {
} else {
	$ars = explode(PHP_EOL, $ars);
	for ($iars = 0; $iars < count($ars); $iars++) {
		$cdid = $ars[$iars];
		$sqly = "select * from essay where id= '{$cdid}'";
		$result = mysqli_query($conn, $sqly);
		if (mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
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
					$zdes = "margin-right: 5px;";
				} else {
					$ggdiv = "display: none;";
					$ggurl = "";
					$gps = "<div class=\"sh-content-right-gps\"><a href=\"javascript:;\">" . $ptpdw . "</a></div>";
					$zdes = "";
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
                            <div style=\"display: flex;align-content: center;\">
                            <div class=\"sh-content-right-head-title-ad\" style=\"background: var(--themetm);" . $zdes . "\">
                                <p style=\"color: var(--theme);\">置顶</p>
                            </div>
                            <div class=\"sh-content-right-head-title-ad\" style=\"" . $ggdiv . "\">
                                <p>广告</p>
                            </div></div>
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
                            <video class=\"sh-content-video\" data-ybs=\"" . $vidoyuan . "\" poster=\"" . $ptpvideofm . "\" id=\"sh-content-videok-" . $cid . "\" src=\"" . $ptpvideo . "\" playsinline webkit-playsinline preload=\"metadata\" " . $videobf . " muted loop onclick=\"videofd()\" lang=\"0\"></video>

                            <!--iframe src=\"../site/library/player.php?url=" . $ptpvideo . "\" allowfullscreen=\"allowfullscreen\" mozallowfullscreen=\"mozallowfullscreen\" msallowfullscreen=\"msallowfullscreen\" oallowfullscreen=\"oallowfullscreen\" webkitallowfullscreen=\"webkitallowfullscreen\" frameborder=\"\"></iframe-->

                            " . $videobfplas . "
                            <i class=\"iconfont icon-quxiao\" id=\"sh-content-videog-" . $cid . "\" onclick=\"videofdgb()\" lang=\"0\"></i>
                            <span class=\"sh-video-span\" id=\"sh-video-span-" . $cid . "\">MP4</span>
                        </div>
                            ";
					} elseif ($ptplx == "music") {
						echo "<!-- 音乐 -->";
						if (is_numeric($ptpmusic)) {
						} else {
							$mus = explode("|", $ptpmusic);
							include "./site/musicplay.php";
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
}
