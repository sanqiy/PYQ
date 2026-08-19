<?php

$iteace = "adminapi";
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];

if ($user_zh == "" || $user_passid == "") {
    exit("<script language=\"JavaScript\">;alert(\"请先登录!\");location.href=\"../login.php\";</script>");
}

include "../../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    exit("连接失败: " . $conn->connect_error);
}

$user_zhsg = addslashes(htmlspecialchars($user_zh));
$sqls = "select * from user where username='{$user_zhsg}'";
$data_results = mysqli_query($conn, $sqls);
$data2s_row = mysqli_fetch_array($data_results);
$user_zh = $data2s_row["username"];
$user_name = $data2s_row["name"];
$user_img = $data2s_row["img"];
$user_url = $data2s_row["url"];

$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $glyzhuser = $row["username"];
}

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
$zid = $data2_row["id"];
$zjpassword = $data2_row["password"];

if ($user_passid != $passid) {
    setcookie("username", "", time() + -1, "/");
    setcookie("passid", "", time() + -1, "/");
    exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"../login.php\";</script>");
}

if ($zjzhq != $glyzhuser) {
    exit("<script language=\"JavaScript\">;alert(\"没有权限!\");history.go(-1);</script>");
}

$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    exit("连接失败: " . $conn->connect_error);
}

$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $name = $row["name"];
    $subtitle = $row["subtitle"];
    $icon = $row["icon"];
    $logo = $row["logo"];
    $zt = $row["zt"];
    $username = $row["username"];
    $glyadmin = $row["username"];
    $homimg = $row["homimg"];
    $sign = $row["sign"];
    $wzmusic = $row["music"];
    $essgs = $row["essgs"];
    $commgs = $row["commgs"];
    $lnkzt = $row["lnkzt"];
    $regqx = $row["regqx"];
    $kqsy = $row["kqsy"];
    $comaud = $row["comaud"];
    $ptpaud = $row["ptpaud"];
    $emydz = $row["emydz"];
    $emssl = $row["emssl"];
    $emduk = $row["emduk"];
    $emkey = $row["emkey"];
    $emzh = $row["emzh"];
    $emfs = $row["emfs"];
    $emfszm = $row["emfszm"];
    $copyright = $row["copyright"];
    $beian = $row["beian"];
    $topes = $row["topes"];
    $scfont = $row["scfont"];
    $viscomm = $row["viscomm"];
    $musplay = $row["musplay"];
    $date = $row["date"];
}

$shemail_dshwznr = "<div class=\"open_email\" style=\"margin-left: 8px; margin-top: 8px; margin-bottom: 8px; margin-right: 8px;\">
        <div>
            <span class=\"genEmailNicker\">
            </span>
            <br>
            <span class=\"genEmailContent\">
                <div id=\"cTMail-Wrap\"
                    style=\"word-break: break-all;box-sizing:border-box;text-align:center;min-width:320px; max-width:660px; border:1px solid #f6f6f6; background-color:#f7f8fa; margin:auto; padding:20px 0 30px; font-family:'helvetica neue',PingFangSC-Light,arial,'hiragino sans gb','microsoft yahei ui','microsoft yahei',simsun,sans-serif\">
                    <div class=\"main-content\" style=\"\">
                        <table style=\"width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse\">
                            <tbody>
                                <tr style=\"font-weight:300\">
                                    <td style=\"width:3%;max-width:30px;\"></td>
                                    <td style=\"max-width:600px;\">
                                        <div id=\"cTMail-logo\" style=\"width:92px; height:25px;\">
                                            <a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] ."\" rel=\"noopener\" target=\"_blank\">
                                            <img border=\"0\" src=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/assets/img/logo.png\"
                                            style=\"width:92px; height:25px;display:block\">
                                            </a>
                                        </div>
                                        <p
                                            style=\"height:2px;background-color: #00a4ff;border: 0;font-size:0;padding:0;width:100%;margin-top:20px;\">
                                        </p>
                                        <div id=\"cTMail-inner\"
                                            style=\"background-color:#fff; padding:23px 0 20px;box-shadow: 0px 1px 1px 0px rgba(122, 55, 55, 0.2);text-align:left;\">
                                            <table
                                                style=\"width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse;text-align:left;\">

                                                <tbody>
                                                    <tr style=\"font-weight:300\">
                                                        <td style=\"width:3.2%;max-width:30px;\"></td>
                                                        <td style=\"max-width:480px;text-align:left;\">
                                                            <h1 id=\"cTMail-title\"
                                                                style=\"font-weight:bold;font-size:20px; line-height:36px; margin:0 0 16px;\">
                                                                邮件测试通知</h1>
                                                            <p id=\"cTMail-userName\"
                                                                style=\"font-size:14px;color:#333; line-height:24px; margin:0;\">
                                                                尊敬的用户，您好！</p>
                                                            <p class=\"cTMail-content\"
                                                                style=\"font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;\">
                                                                该邮件为系统测试邮件,若您收到此邮件代表您的网站邮箱配置正确,可以正常收发邮件啦!</p>

                                                            <p class=\"cTMail-content\"
                                                                style=\"font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 6px 0px 0px; word-wrap: break-word; word-break: break-all;\">
                                                           <span style=\"font-size: 16px; line-height: 45px; display: block; background-color: rgb(0, 164, 255); color: rgb(255, 255, 255); text-align: center; text-decoration: none; margin-top: 20px; border-radius: 3px;\">配置成功</span>
                                                            </p>

                                                            <p
                                                                style=\"border-top: 1px solid rgb(234, 237, 240); margin: 20px 0px;\">
                                                            </p>

                                                            <dl
                                                                style=\"font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;\">

                                                                <dd
                                                                    style=\"margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;\">
                                                                    <p id=\"cTMail-sender\"
                                                                        style=\"font-size: 14px; line-height: 26px; word-wrap: break-word; word-break: break-all; margin-top: 32px;\">
                                                                        此致
                                                                        <br>
                                                                        <a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "\" rel=\"noopener\" target=\"_blank\"><strong> $name </strong></a>

                                                                    </p>
                                                                </dd>
                                                            </dl>
                                                        </td>
                                                        <td style=\"width:3.2%;max-width:30px;\"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id=\"cTMail-copy\"
                                            style=\"text-align:center; font-size:12px; line-height:18px; color:#999\">
                                            <table
                                                style=\"width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse\">
                                                <tbody>
                                                    <tr style=\"font-weight:300\">
                                                        <td style=\"width:3.2%;max-width:30px;\"></td>  

                                                        <td style=\"max-width:540px;\">
                                                            <p
                                                                style=\"text-align:center; margin:20px auto 14px auto;font-size:12px;color:#999;\">
                                                                此为系统邮件，请勿回复。 
                                                            </p>
                                                            <p style=\"text-align:center;margin:0 auto 4px;\">
                                                            Copyright © 2025 $_SERVER["REQUEST_SCHEME"]. All rights reserved .
                                                            </p>
                                                            </p>
                                                        </td>
                                                        <td style=\"width:3.2%;max-width:30px;\"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                    <td style=\"width:3%;max-width:30px;\"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </span>
        </div>
    </div>
</div>";

$post_data = ["emydz" => $emydz, "emssl" => $emssl, "emduk" => $emduk, "key" => $emkey, "username" => $emfs, "fromuser" => $emfs, "fromname" => $emfszm, "title" => "[" . $name . "] 测试邮件!", "nr" => $shemail_dshwznr, "reveuser" => $emfs];
$url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/site/email/email.php";
$data = $post_data;
$headers = ["Authorization: Basic YWRtaW46YWRtaW4xMjMuLi4=", "Cookie: PHPSESSID=" . session_id(), "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36"];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($curl);

if (curl_errno($curl)) {
}

curl_close($curl);

if ($result == "") {
    exit("<script language=\"JavaScript\">;alert(\"未获取到数据\");history.go(-1);</script>");
} else {
    exit("<script language=\"JavaScript\">;alert(\"" . $result . "\");history.go(-1);</script>");
}

$conn->close();

?>
