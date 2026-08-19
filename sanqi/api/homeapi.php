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
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
$page = addslashes(htmlspecialchars($_POST["page"]));
$getuser = addslashes(htmlspecialchars($_POST["getuser"]));
if ($user_zh == "" || $user_passid == "") {
	if ($getuser == "") {
		exit("请先登录!");
	}
}
if ($page == "") {
	exit("参数为空!");
}
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
$data_result = mysqli_query($conn, "select * from user where username='{$user_zh}'");
$data2_row = mysqli_fetch_array($data_result);
$zjzhq = $data2_row["username"];
$zjnc = $data2_row["name"];
$zjimg = $data2_row["img"];
$zjhomeimg = $data2_row["homeimg"];
$zjsign = $data2_row["sign"];
$zjemail = $data2_row["email"];
$zjurl = $data2_row["url"];
$zjfbqx = $data2_row["essqx"];
$zjemtz = $data2_row["esseam"];
$passid = $data2_row["passid"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("账号信息异常,请重新登录!");
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
	$daymoed = $row["daymoed"];
	$gotop = $row["gotop"];
	$search = $row["search"];
	$videoauplay = $row["videoauplay"];
}
if ($getuser != "") {
	$sql = "SELECT * FROM user";
	$result = $conn->query($sql);
	$data = [];
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$encrypted_username = md5(md5($row["username"]));
			$data[$encrypted_username] = $row["username"];
		}
	} else {
		exit("用户不存在");
	}
	if (array_key_exists($getuser, $data)) {
		$getuser = $data[$getuser];
	} else {
		exit("此用户不存在");
	}
	$sql = "select * from user where username= '{$getuser}'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		do {
		} while ($row = mysqli_fetch_assoc($result));
	} else {
		exit("此用户不存在");
	}
}
$dqgs = 0;
$dqs = 0;
$prevYear = $prevMonth = $prevDay = "";
$timebss = "";
if ($getuser != "" && $getuser != null) {
	$query = "SELECT * FROM essay WHERE ptpys='1' and ptpaud='1' and ptpuser = '{$getuser}'";
} else {
	if ($user_zh == $glyadmin) {
		$query = "SELECT * FROM essay WHERE ptpaud='1'";
	} else {
		$query = "SELECT * FROM essay WHERE ptpaud='1' and ptpuser = '{$user_zh}'";
	}
}
$result = mysqli_query($conn, $query);
$ptptime_values = [];
while ($row = mysqli_fetch_assoc($result)) {
	$ptptime = date("Y", strtotime($row["ptptime"])) . date("m", strtotime($row["ptptime"])) . date("d", strtotime($row["ptptime"])) . date("H", strtotime($row["ptptime"])) . date("i", strtotime($row["ptptime"])) . date("s", strtotime($row["ptptime"]));
	if (strpos($ptptime, "-") !== false) {
		$timestamp = strtotime($ptptime);
	} else {
		$timestamp = DateTime::createFromFormat("YmdHis", $ptptime)->getTimestamp();
	}
	$ptptime_values[$timestamp] = $row;
}
krsort($ptptime_values);
foreach ($ptptime_values as $row) {
	$dqgs = $dqgs + 1;
	if ($page < $dqgs) {
		$dqs = $dqs + 1;
		if ($essgs < $dqs) {
			$dqgs = $dqgs - 1;
			break;
		}
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
		$year = date("Y", strtotime($ptptime));
		$month = date("m", strtotime($ptptime));
		$day = date("d", strtotime($ptptime));
		$givenDates = $year . "-" . $month . "-" . $day;
		if ($year != $prevYear) {
			if (date("Y") != $year) {
				echo "<h2 class=\"sh-homecontent-timed\">" . $year . "<span class=\"sh-homecontent-timed-n\">年</span></h2>";
			}
			$prevYear = $year;
		}
		if ($ptpys == 0) {
			$wzsdbs = "<i class=\"iconfont icon-suoding sh-homecontent-wzsbs\"></i>";
		} else {
			$wzsdbs = "";
		}
		if ($ptplx == "img") {
			$imgar = explode("(+@+)", $ptpimag);
			$coun = count($imgar);
			if ($coun == 1) {
				$wzbank = "<div class=\"sh-homecontent-lie\">
            <!-- 左边 -->
            <div class=\"sh-homecontent-left\">
                <!-- 日期 -->
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
			$musm = $mus[1];
			$muss = $mus[2];
			$musimg = $mus[3];
			if ($musm == "") {
				$musm = "-";
			}
			if ($muss == "") {
				$muss = "-";
			}
			if ($musimg == "") {
				$musimg = "./assets/img/musicba.jpg";
			} else {
				$musimg = $mus[3];
			}
			if (is_numeric($ptpmusic)) {
				$musm = "网易云音乐播放器已移除";
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
                            <div class=\"homecontent-right-nr-text homecontent-right-nr-text-music\" style=\"color: var(--thetitle);\">" . $musm . "</div>
                            <p class=\"homecontent-right-nr-text-music-p homecontent-right-nr-text-music\">" . $muss . "</p>
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
                <div class=\"sh-homecontent-left-time\" lang=\"year-" . $givenDates . "\" style=\"" . $shougbsx . "\">
                    <span class=\"homecontent-left-time-h\">" . $day . "</span>
                    <span class=\"homecontent-left-time-y\">" . $month . "月</span>
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
echo "8965896582315263skfjskfjsgdfgdgddjskdjgdsdsjhd" . $dqgs . "fedfrtgg6h5j8u5j5d45hgd5s4fh5sd5gdf8w5dss48sd1fds56ds156";
$conn->close();
