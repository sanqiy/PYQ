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
				if ($iars == 1) {
					$topxs = "置顶";
				} else {
					$topxs = "";
				}
				if ($ptpys == 1 && $ptpaud == 1) {
					if ($ptplx == "img") {
						$imgar = explode("(+@+)", $ptpimag);
						$coun = count($imgar);
						if ($coun == 1) {
							$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">

                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 图片 -->
                    <div class=\"homecontent-right-tw\">
                        <!-- 图片 -->
                        <div class=\"homecontent-right-tw-img\" style=\"grid-template-columns: 1fr;grid-template-rows: 1fr;\">
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[0] . "\" alt=\"\"></div>
                            " . $wzsdbs . "
                        </div>
                    </div>
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\">
                        <div class=\"homecontent-right-nr-text\">" . $ptptext . "</div>
                        <p class=\"homecontent-right-nr-tus\">共" . $coun . "张</p>
                    </div>
                </div>
            </div>
        </div>";
						} elseif ($coun == 2) {
							$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">

                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 图片 -->
                    <div class=\"homecontent-right-tw\">
                        <!-- 图片 -->
                        <div class=\"homecontent-right-tw-img\">
                            <div class=\"homecontent-right-tw-img-wk\" style=\"grid-row: 1 / 3;\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[0] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\" style=\"grid-row: 1 / 3;\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[1] . "\" alt=\"\"></div>
                            " . $wzsdbs . "
                        </div>
                    </div>
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\">
                        <div class=\"homecontent-right-nr-text\">" . $ptptext . "</div>
                        <p class=\"homecontent-right-nr-tus\">共" . $coun . "张</p>
                    </div>
                </div>
            </div>
        </div>";
						} elseif ($coun == 3) {
							$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">

                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 图片 -->
                    <div class=\"homecontent-right-tw\">
                        <!-- 图片 -->
                        <div class=\"homecontent-right-tw-img\">
                            <div class=\"homecontent-right-tw-img-wk\" style=\"grid-row: 1 / 3;\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[0] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[1] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[2] . "\" alt=\"\"></div>
                            " . $wzsdbs . "
                        </div>
                    </div>
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\">
                        <div class=\"homecontent-right-nr-text\">" . $ptptext . "</div>
                        <p class=\"homecontent-right-nr-tus\">共" . $coun . "张</p>
                    </div>
                </div>
            </div>
        </div>";
						} elseif ($coun == 4 || $coun >= 4) {
							$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">

                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 图片 -->
                    <div class=\"homecontent-right-tw\">
                        <!-- 图片 -->
                        <div class=\"homecontent-right-tw-img\">
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[0] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[1] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[2] . "\" alt=\"\"></div>
                            <div class=\"homecontent-right-tw-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $imgar[3] . "\" alt=\"\"></div>
                            " . $wzsdbs . "
                        </div>
                    </div>
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\">
                        <div class=\"homecontent-right-nr-text\">" . $ptptext . "</div>
                        <p class=\"homecontent-right-nr-tus\">共" . $coun . "张</p>
                    </div>
                </div>
            </div>
        </div>";
						}
					} elseif ($ptplx == "video") {
						if ($videoauplay == 1) {
							$videobf = "autoplay";
							$videobfplas = "";
						} else {
							$videobf = "";
							$videobfplas = "<i class=\"iconfont icon-sa4f56\" id=\"sh-content-video-videobfb-" . $cid . "\" style=\"width: fit-content;height: fit-content;grid-column: 1;grid-row: 1;z-index: 5;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-size: 30px;color: #ffffff;display: flex;cursor: pointer;padding: 15px;pointer-events: none;\"></i>";
						}
						$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">
                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 视频 -->
                    <div class=\"homecontent-right-tw\">
                        <!-- 视频 -->
                        <div class=\"homecontent-right-tw-video\">
                            <video class=\"homecontent-right-tw-videoau\" poster=\"" . $ptpvideofm . "\" src=\"" . $ptpvideo . "\" playsinline=\"\" webkit-playsinline=\"\" preload=\"metadata\" " . $videobf . " muted=\"\" loop=\"\"></video>
                            " . $videobfplas . "
                            <span class=\"sh-video-span\" style=\"left: 4px;bottom: 4px;\">MP4</span>
                                " . $wzsdbs . "
                        </div>
                    </div>
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\">
                        <div class=\"homecontent-right-nr-text\">" . $ptptext . "</div>
                    </div>
                </div>
            </div>
        </div>";
					} elseif ($ptplx == "music") {
						$mus = explode("|", $ptpmusic);
						if ($mus[3] == "") {
							$musimg = "./assets/img/musicba.jpg";
						} else {
							$musimg = $mus[3];
						}
						if ($ptptext == "") {
							$ptpnr = "";
						} else {
							$ptpnr = "<div class=\"sh-homecontent-right-lie-music-title\">" . $ptptext . "</div>";
						}
						$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <!-- 右边内容框架 -->
            <div class=\"sh-homecontent-right-wk\">
                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie-musicwk\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">" . $wzsdbs . "
                    " . $ptpnr . "
                    <div class=\"sh-homecontent-right-lie sh-homecontent-right-lie-music\">
                        <!-- 音乐 -->
                        <div class=\"homecontent-right-tw homecontent-right-tw-music\">
                            <!-- 图片 -->
                            <div class=\"homecontent-right-tw-img\" style=\"grid-template-columns: 1fr;grid-template-rows: 1fr;\">
                                <div class=\"homecontent-right-tw-img-wk homecontent-right-tw-img-wk-music\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $musimg . "\" alt=\"\"><i class=\"iconfont icon-sa4f56\"></i></div>
                            </div>
                        </div>
                        <!-- 文字内容 -->
                        <div class=\"homecontent-right-nr homecontent-right-nr-music\">
                            <div class=\"homecontent-right-nr-text homecontent-right-nr-text-music\" style=\"color: var(--thetitle);\">" . $mus[1] . "</div>
                            <p class=\"homecontent-right-nr-text-music-p homecontent-right-nr-text-music\">" . $mus[2] . "</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
					} elseif ($ptplx == "only") {
						$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $year . "-" . $month . "-" . $day . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $topxs . "</span>

                </div>
                <!-- 地址 -->
                <div class=\"sh-homecontent-left-time-dw\">" . $ptpdw . "</div>
            </div>
            <div class=\"sh-homecontent-right-wk\">
                <!-- 右边内容主体 -->
                <div class=\"sh-homecontent-right-lie\" onclick=\"window.location.href = './view.php?cid=" . $cid . "';\">
                    <!-- 仅文字 -->
                    <!-- 文字内容 -->
                    <div class=\"homecontent-right-nr\" style=\"min-height: 10px;background: var(--fgxys);position:relative;\">
                        <div class=\"homecontent-right-nr-text homecontent-right-nr-textjw\" style=\"margin: 10px;\">" . $ptptext . "</div>
                        " . $wzsdbs . "
                    </div>
                </div>
            </div>
        </div>";
					}
					echo $wzbank;
				}
			}
		}
	}
	?><div class="sh-homecontent-lie" style="margin-top: 25px;display:flex;justify-content: center;">
      <div style="width: 90%;border-bottom: 1px solid var(--fgxys);margin: 15px 0;"></div>
      </div><?php
}
