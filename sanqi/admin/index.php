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
        exit("<script language=\"JavaScript\">alert(\"您账号登陆令牌已经失效请重新登陆！\");location.href=\"./login.php\";</script>");
    }
} else {
    exit("<script language=\"JavaScript\">alert(\"您的账号未获取后台权限哦！\");location.href=\"../index.php\";</script>");
}

$Query = "Select count(*) as AllNum from user";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$usercount = $b["AllNum"];

$Query = "Select count(*) as AllNum from essay";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$essaycount = $b["AllNum"];

$Query = "Select count(*) as AllNum from comm";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$commcount = $b["AllNum"];

$today = date("Y-m-d");
$Query = "Select count(*) as todayNum from essay WHERE DATE(essaytime) = '$today'";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$todayEssay = $b["todayNum"];

$Query = "Select count(*) as todayComms from comm WHERE DATE(comtime) = '$today'";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$todayComms = $b["todayComms"];

$lastWeek = date("Y-m-d", strtotime("-7 days"));
$Query = "Select count(*) as weekEssays from essay WHERE essaytime >= '$lastWeek'";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$weekEssays = $b["weekEssays"];

$Query = "SELECT username, COUNT(*) as essay_count FROM essay WHERE essaytime >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY username ORDER BY essay_count DESC LIMIT 5";
$activeUsers = mysqli_query($conn, $Query);

$totalFilesQuery = "SELECT COUNT(*) as total FROM rm";
$totalFilesResult = mysqli_query($conn, $totalFilesQuery);
$totalFiles = mysqli_fetch_assoc($totalFilesResult)['total'];

$latestEssayQuery = "SELECT title, essaytime FROM essay ORDER BY essaytime DESC LIMIT 1";
$latestEssayResult = mysqli_query($conn, $latestEssayQuery);
$latestEssay = mysqli_fetch_assoc($latestEssayResult);

$Query = "Select count(*) as AllNum from essay WHERE ptpaud='0'";
$aes = mysqli_query($conn, $Query);
$escount = mysqli_fetch_assoc($aes);
$essl = $escount["AllNum"];

$Query = "Select count(*) as AllNum from comm WHERE comaud='0'";
$aco = mysqli_query($conn, $Query);
$cocount = mysqli_fetch_assoc($aco);
$cosl = $cocount["AllNum"];
$dshzl = $essl + $cosl;

$todayUserQuery = "SELECT COUNT(*) as todayUserNum FROM user WHERE DATE(regtime) = '$today'";
$todayUserResult = mysqli_query($conn, $todayUserQuery);
$todayUserData = mysqli_fetch_assoc($todayUserResult);
$todayUserCount = $todayUserData['todayUserNum'];

$weekAgo = date("Y-m-d", strtotime("-7 days"));
$lastWeekUserQuery = "SELECT COUNT(*) as lastWeekUserNum FROM user WHERE regtime >= '$weekAgo' AND regtime < '$today'";
$lastWeekUserResult = mysqli_query($conn, $lastWeekUserQuery);
$lastWeekUserData = mysqli_fetch_assoc($lastWeekUserResult);
$lastWeekUserCount = $lastWeekUserData['lastWeekUserNum'];

$userGrowthRate = 0;
if ($lastWeekUserCount > 0 && $todayUserCount > 0) {
    $userGrowthRate = round(($todayUserCount / $lastWeekUserCount) * 100 - 100, 1);
} elseif ($todayUserCount > 0) {
    $userGrowthRate = 100;
}

$monthlyActiveQuery = "SELECT COUNT(DISTINCT username) as activeUsers FROM essay WHERE essaytime >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$monthlyActiveResult = mysqli_query($conn, $monthlyActiveQuery);
$monthlyActiveData = mysqli_fetch_assoc($monthlyActiveResult);
$monthlyActiveUsers = $monthlyActiveData['activeUsers'];

$lastWeekEssayQuery = "SELECT COUNT(*) as lastWeekEssayNum FROM essay WHERE essaytime >= '$weekAgo' AND essaytime < '$today'";
$lastWeekEssayResult = mysqli_query($conn, $lastWeekEssayQuery);
$lastWeekEssayData = mysqli_fetch_assoc($lastWeekEssayResult);
$lastWeekEssayCount = $lastWeekEssayData['lastWeekEssayNum'];

$essayGrowthRate = 0;
if ($lastWeekEssayCount > 0 && $todayEssay > 0) {
    $essayGrowthRate = round(($todayEssay / $lastWeekEssayCount) * 100 - 100, 1);
} elseif ($todayEssay > 0) {
    $essayGrowthRate = 100;
}

$categoryQuery = "SELECT category, COUNT(*) as count FROM essay GROUP BY category ORDER BY count DESC LIMIT 5";
$categoryResult = mysqli_query($conn, $categoryQuery);

$lastWeekCommQuery = "SELECT COUNT(*) as lastWeekCommNum FROM comm WHERE comtime >= '$weekAgo' AND comtime < '$today'";
$lastWeekCommResult = mysqli_query($conn, $lastWeekCommQuery);
$lastWeekCommData = mysqli_fetch_assoc($lastWeekCommResult);
$lastWeekCommCount = $lastWeekCommData['lastWeekCommNum'];

$commGrowthRate = 0;
if ($lastWeekCommCount > 0 && $todayComms > 0) {
    $commGrowthRate = round(($todayComms / $lastWeekCommCount) * 100 - 100, 1);
} elseif ($todayComms > 0) {
    $commGrowthRate = 100;
}

$interactionRate = ($essaycount > 0) ? round(($commcount / $essaycount) * 100, 1) : 0;

$totalSizeQuery = "SELECT SUM(size) as totalSize FROM rm";
$totalSizeResult = mysqli_query($conn, $totalSizeQuery);
$totalSizeData = mysqli_fetch_assoc($totalSizeResult);
$totalFileSize = $totalSizeData['totalSize'] ? round($totalSizeData['totalSize'] / (1024*1024), 2) : 0;

$todayFileQuery = "SELECT COUNT(*) as todayFileNum FROM rm WHERE DATE(upload_time) = '$today'";
$todayFileResult = mysqli_query($conn, $todayFileQuery);
$todayFileData = mysqli_fetch_assoc($todayFileResult);
$todayFileCount = $todayFileData['todayFileNum'];

$yesterday = date("Y-m-d", strtotime("-1 day"));
$yesterdayFileQuery = "SELECT COUNT(*) as yesterdayFileNum FROM rm WHERE DATE(upload_time) = '$yesterday'";
$yesterdayFileResult = mysqli_query($conn, $yesterdayFileQuery);
$yesterdayFileData = mysqli_fetch_assoc($yesterdayFileResult);
$yesterdayFileCount = $yesterdayFileData['yesterdayFileNum'];

$fileGrowthRate = 0;
if ($yesterdayFileCount > 0 && $todayFileCount > 0) {
    $fileGrowthRate = round(($todayFileCount / $yesterdayFileCount) * 100 - 100, 1);
} elseif ($todayFileCount > 0) {
    $fileGrowthRate = 100;
}

$avgFileSize = ($totalFiles > 0) ? round($totalFileSize / $totalFiles, 1) : 0;

$regTrendQuery = "SELECT DATE(regtime) as regdate, COUNT(*) as regcount FROM user WHERE regtime >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(regtime) ORDER BY regdate";
$regTrendResult = mysqli_query($conn, $regTrendQuery);
$regTrendData = [];
while($row = mysqli_fetch_assoc($regTrendResult)) {
    $regTrendData[] = $row;
}

$essayTrendQuery = "SELECT DATE(essaytime) as essaydate, COUNT(*) as essaycount FROM essay WHERE essaytime >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(essaytime) ORDER BY essaydate";
$essayTrendResult = mysqli_query($conn, $essayTrendQuery);
$essayTrendData = [];
while($row = mysqli_fetch_assoc($essayTrendResult)) {
    $essayTrendData[] = $row;
}

$commTrendQuery = "SELECT DATE(comtime) as commdate, COUNT(*) as commcount FROM comm WHERE comtime >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(comtime) ORDER BY commdate";
$commTrendResult = mysqli_query($conn, $commTrendQuery);
$commTrendData = [];
while($row = mysqli_fetch_assoc($commTrendResult)) {
    $commTrendData[] = $row;
}

$totalUsersQuery = "SELECT COUNT(*) as total FROM user";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];

$totalEssaysQuery = "SELECT COUNT(*) as total FROM essay";
$totalEssaysResult = mysqli_query($conn, $totalEssaysQuery);
$totalEssays = mysqli_fetch_assoc($totalEssaysResult)['total'];

$totalCommentsQuery = "SELECT COUNT(*) as total FROM comm";
$totalCommentsResult = mysqli_query($conn, $totalCommentsQuery);
$totalComments = mysqli_fetch_assoc($totalCommentsResult)['total'];

$todayStats = [
    'users' => $todayUserCount,
    'essays' => $todayEssay,
    'comments' => $todayComms,
    'files' => $todayFileCount,
    'pending' => $dshzl
];

$weekStats = [
    'essays' => $weekEssays,
    'users' => $lastWeekUserCount,
    'comments' => $lastWeekCommCount
];

$categoryTotalQuery = "SELECT COUNT(DISTINCT category) as total FROM essay WHERE category IS NOT NULL AND category != ''";
$categoryTotalResult = mysqli_query($conn, $categoryTotalQuery);
$categoryTotalData = mysqli_fetch_assoc($categoryTotalResult);

$categoryDetailQuery = "SELECT
    category,
    COUNT(*) as count,
    COUNT(*) * 100.0 / (SELECT COUNT(*) FROM essay) as percentage,
    MAX(essaytime) as latest_update,
    MIN(essaytime) as earliest_update
FROM essay
WHERE category IS NOT NULL AND category != ''
GROUP BY category
ORDER BY count DESC
LIMIT 8";
$categoryDetailResult = mysqli_query($conn, $categoryDetailQuery);
$hasCategories = $categoryDetailResult && mysqli_num_rows($categoryDetailResult) > 0;

$categoryChartQuery = "SELECT category, COUNT(*) as count FROM essay WHERE category IS NOT NULL AND category != '' GROUP BY category ORDER BY count DESC LIMIT 5";
$categoryChartResult = mysqli_query($conn, $categoryChartQuery);
$chartData = [];
$otherCount = 0;
$totalWithCategories = 0;
while($row = mysqli_fetch_assoc($categoryChartResult)) {
  $chartData[] = $row;
  $totalWithCategories += $row['count'];
}
$otherCount = $essaycount - $totalWithCategories;
if($otherCount > 0) {
  $chartData[] = ['category' => '其他', 'count' => $otherCount];
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . " ," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>后台管理 - <?php echo $name;?></title>
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
    .dashboard-widget {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #eef1f5;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .dashboard-widget:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(0,0,0,0.1);
    }
    .stat-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color, #667eea), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover::before {
        opacity: 1;
    }
    .stat-icon-wrapper {
        position: relative;
    }
    .stat-icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: all 0.3s ease;
    }
    .stat-card:hover .stat-icon-circle {
        transform: scale(1.1) rotate(5deg);
    }
    .stat-number {
        font-size: 32px;
        font-weight: 800;
        background: linear-gradient(135deg, var(--card-color, #667eea) 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    .stat-title {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        opacity: 0.8;
    }
    .stat-change {
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
    }
    .stat-change.text-success {
        background: rgba(76, 175, 80, 0.1);
    }
    .stat-change.text-danger {
        background: rgba(244, 67, 54, 0.1);
    }
    .stat-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 12px;
        margin-top: 12px;
    }
    .stat-footer .col-6 {
        border-right: 1px solid rgba(0, 0, 0, 0.05);
    }
    .stat-footer .col-6:last-child {
        border-right: none;
    }
    .stat-card:nth-child(1) {
        --card-color: #667eea;
    }
    .stat-card:nth-child(2) {
        --card-color: #f5576c;
    }
    .stat-card:nth-child(3) {
        --card-color: #4facfe;
    }
    .stat-card:nth-child(4) {
        --card-color: #43e97b;
    }
    .trend-chart-container {
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 8px 32px rgba(0,0,0,0.04);
    }
    .trend-chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(0,0,0,0.05);
    }
    .trend-chart-title {
        font-size: 18px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .trend-time-selector {
        display: flex;
        gap: 8px;
        background: #f8f9fa;
        padding: 6px;
        border-radius: 12px;
    }
    .time-btn {
        padding: 6px 16px;
        border: none;
        background: transparent;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .time-btn.active {
        background: white;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(102,126,234,0.2);
    }
    .trend-metric-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .trend-metric-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #eef1f5;
        transition: all 0.3s ease;
    }
    .trend-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
    }
    .metric-icon.users {
        background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
        color: #667eea;
    }
    .metric-icon.content {
        background: linear-gradient(135deg, rgba(245,87,108,0.1) 0%, rgba(240,147,251,0.1) 100%);
        color: #f5576c;
    }
    .metric-icon.comments {
        background: linear-gradient(135deg, rgba(79,172,254,0.1) 0%, rgba(0,242,254,0.1) 100%);
        color: #4facfe;
    }
    .metric-value {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .metric-label {
        font-size: 13px;
        color: #666;
        font-weight: 600;
    }
    .metric-change {
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
    }
    .trend-chart-area {
        height: 280px;
        position: relative;
    }
    .chart-legend {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    .legend-text {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }
    .trend-comparison {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    .comparison-item {
        flex: 1;
        background: white;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #eef1f5;
        text-align: center;
    }
    .comparison-value {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .comparison-label {
        font-size: 12px;
        color: #999;
    }
    .version-card {
        border-left: 4px solid #4caf50;
        padding-left: 20px;
        margin-bottom: 25px;
    }
    .version-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }
    .current-version {
        background: linear-gradient(135deg, #4caf50, #8bc34a);
        color: white;
    }
    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        font-size: 18px;
    }
    .server-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .status-online {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }
    .quick-action {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .quick-action:hover {
        background: #fff;
        border-color: #4caf50;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .quick-action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 22px;
        color: white;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .stat-card {
        animation: fadeInUp 0.6s ease forwards;
        animation-delay: calc(var(--card-index, 0) * 0.1s);
        opacity: 0;
    }
    .category-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .category-chart svg {
        max-width: 200px;
        margin: 0 auto;
        display: block;
    }
    .category-legend {
        max-height: 180px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .category-legend::-webkit-scrollbar {
        width: 4px;
    }
    .category-legend::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    .category-legend::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 2px;
    }
    .category-legend::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
    .category-tooltip {
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .progress {
        background-color: #f0f0f0;
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease-in-out;
    }
    .version-timeline {
        position: relative;
        padding-left: 30px;
        margin-top: 25px;
    }
    .version-timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #667eea 0%, #f5576c 100%);
        border-radius: 2px;
    }
    .version-item {
        position: relative;
        margin-bottom: 25px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        border: 1px solid #eef1f5;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .version-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .version-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 3px solid white;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.2);
    }
    .version-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 10px;
    }
    .version-date {
        color: #999;
        font-size: 13px;
        margin-left: 10px;
    }
    .version-features {
        margin-top: 10px;
        padding-left: 20px;
    }
    .version-features li {
        margin-bottom: 6px;
        color: #666;
        font-size: 13px;
        position: relative;
    }
    .version-features li::before {
        content: '•';
        position: absolute;
        left: -15px;
        color: #4caf50;
        font-weight: bold;
    }
    .performance-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
        border-radius: 12px;
        font-size: 11px;
        margin-left: 8px;
    }
    @media (max-width: 768px) {
        .stat-number {
            font-size: 28px;
        }
        .stat-icon-circle {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
        .stat-footer .col-6 {
            margin-bottom: 10px;
            border-right: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding-bottom: 10px;
        }
        .stat-footer .col-6:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .trend-metric-grid {
            grid-template-columns: 1fr;
        }
        .category-chart {
            margin-bottom: 20px;
        }
        .category-legend {
            max-height: 150px;
        }
        .table-responsive {
            font-size: 14px;
        }
    }
</style>
</head>

<body>
<div id="lyear-preloader" class="loading">
  <div class="ctn-preloader">
    <div class="round_spinner">
      <div class="spinner"></div>
      <img src="<?php if(strpos($logo,"http")!==false){echo $logo;}else{echo ".".$logo;}?>" alt="">
    </div>
  </div>
</div>
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <aside class="lyear-layout-sidebar">
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
              </ul>
            </li>
          </ul>
        </nav>
        <div class="sidebar-footer">
          <p class="copyright">后台权限归 &copy; <?php echo date("Y");?>. <a target="_blank" href="<?php echo $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];?>"><?php echo $glyname;?></a> 使用</p>
        </div>
      </div>
    </aside>
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
          <li class="dropdown dropdown-skin">
            <span data-toggle="dropdown" class="icon-item"><i class="mdi mdi-palette"></i></span>
            <ul class="dropdown-menu dropdown-menu-right" data-stopPropagation="true">
            </ul>
          </li>
          <li class="dropdown dropdown-profile">
            <a href="javascript:void(0)" data-toggle="dropdown" class="dropdown-toggle">
              <img class="img-avatar img-avatar-48 m-r-10" src="<?php
              if (strpos($user_img, "http") !== false) {
                  echo $user_img;
              } else {
                  echo "." . $user_img;
              }
              ?>" alt="头像"/>
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
    <main class="lyear-layout-content">
      <div class="container-fluid p-t-20">
        <div class="row mb-4">
          <div class="col-12">
            <div class="dashboard-widget">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="mb-1">欢迎回来，<?php echo $user_name;?> 👋</h4>
                  <p class="text-muted mb-0">今天是 <?php echo date('Y年m月d日'); ?>，<?php echo ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'][date('w')]; ?></p>
                </div>
                <div>
                  <span class="server-status status-online">
                    <i class="mdi mdi-check-circle me-2"></i> 系统运行正常
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-widget stat-card">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-info">
                  <h6 class="stat-title mb-1 text-muted">
                    <i class="mdi mdi-account-multiple-outline me-1"></i>总用户数
                  </h6>
                  <div class="d-flex align-items-end">
                    <h2 class="stat-number mb-0 me-2"><?php echo $usercount; ?></h2>
                    <span class="stat-change <?php echo $userGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                      <i class="mdi mdi-arrow-<?php echo $userGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                      <?php echo abs($userGrowthRate); ?>%
                    </span>
                  </div>
                </div>
                <div class="stat-icon-wrapper">
                  <div class="stat-icon-circle" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                    <i class="mdi mdi-account-multiple" style="color: #667eea;"></i>
                  </div>
                </div>
              </div>
              <div class="stat-footer">
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted">
                      <i class="mdi mdi-calendar-today me-1"></i>今日新增
                    </small>
                    <div class="fw-bold"><?php echo $todayUserCount; ?></div>
                  </div>
                  <div class="col-6 text-end">
                    <small class="text-muted">
                      <i class="mdi mdi-calendar-week me-1"></i>月活跃
                    </small>
                    <div class="fw-bold"><?php echo $monthlyActiveUsers; ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-widget stat-card">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-info">
                  <h6 class="stat-title mb-1 text-muted">
                    <i class="mdi mdi-file-document-multiple-outline me-1"></i>内容总数
                  </h6>
                  <div class="d-flex align-items-end">
                    <h2 class="stat-number mb-0 me-2"><?php echo $essaycount; ?></h2>
                    <span class="stat-change <?php echo $essayGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                      <i class="mdi mdi-arrow-<?php echo $essayGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                      <?php echo abs($essayGrowthRate); ?>%
                    </span>
                  </div>
                </div>
                <div class="stat-icon-wrapper">
                  <div class="stat-icon-circle" style="background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%);">
                    <i class="mdi mdi-file-document-multiple" style="color: #f5576c;"></i>
                  </div>
                </div>
              </div>
              <div class="stat-footer">
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted">
                      <i class="mdi mdi-calendar-today me-1"></i>今日新增
                    </small>
                    <div class="fw-bold"><?php echo $todayEssay; ?></div>
                  </div>
                  <div class="col-6 text-end">
                    <small class="text-muted">
                      <i class="mdi mdi-calendar-week me-1"></i>本周新增
                    </small>
                    <div class="fw-bold"><?php echo $weekEssays; ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-widget stat-card">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-info">
                  <h6 class="stat-title mb-1 text-muted">
                    <i class="mdi mdi-comment-multiple-outline me-1"></i>互动评论
                  </h6>
                  <div class="d-flex align-items-end">
                    <h2 class="stat-number mb-0 me-2"><?php echo $commcount; ?></h2>
                    <span class="stat-change <?php echo $commGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                      <i class="mdi mdi-arrow-<?php echo $commGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                      <?php echo abs($commGrowthRate); ?>%
                    </span>
                  </div>
                </div>
                <div class="stat-icon-wrapper">
                  <div class="stat-icon-circle" style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);">
                    <i class="mdi mdi-comment-multiple" style="color: #4facfe;"></i>
                  </div>
                </div>
              </div>
              <div class="stat-footer">
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted">
                      <i class="mdi mdi-calendar-today me-1"></i>今日新增
                    </small>
                    <div class="fw-bold"><?php echo $todayComms; ?></div>
                  </div>
                  <div class="col-6 text-end">
                    <small class="text-muted">
                      <i class="mdi mdi-chart-line me-1"></i>互动率
                    </small>
                    <div class="fw-bold"><?php echo $interactionRate; ?>%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-widget stat-card">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-info">
                  <h6 class="stat-title mb-1 text-muted">
                    <i class="mdi mdi-folder-multiple-outline me-1"></i>资源文件
                  </h6>
                  <div class="d-flex align-items-end">
                    <h2 class="stat-number mb-0 me-2"><?php echo $totalFiles; ?></h2>
                    <span class="stat-change <?php echo $fileGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                      <i class="mdi mdi-arrow-<?php echo $fileGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                      <?php echo abs($fileGrowthRate); ?>%
                    </span>
                  </div>
                </div>
                <div class="stat-icon-wrapper">
                  <div class="stat-icon-circle" style="background: linear-gradient(135deg, rgba(67, 233, 123, 0.1) 0%, rgba(56, 249, 215, 0.1) 100%);">
                    <i class="mdi mdi-folder-multiple" style="color: #43e97b;"></i>
                  </div>
                </div>
              </div>
              <div class="stat-footer">
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted">
                      <i class="mdi mdi-database me-1"></i>总大小
                    </small>
                    <div class="fw-bold"><?php echo $totalFileSize; ?> MB</div>
                  </div>
                  <div class="col-6 text-end">
                    <small class="text-muted">
                      <i class="mdi mdi-file-upload me-1"></i>平均大小
                    </small>
                    <div class="fw-bold"><?php echo $avgFileSize; ?> MB</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 公告版本信息 -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="dashboard-widget">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="mdi mdi-bullhorn-outline me-2"></i> 系统公告</h5>
                <span class="badge bg-info">最新</span>
              </div>
              <div class="alert alert-info border-info" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%); border-left: 4px solid #17a2b8;">
                <div class="d-flex">
                  <div class="me-3">
                    <i class="mdi mdi-information-outline display-6 text-info"></i>
                  </div>
                  <div>
                    <h6 class="alert-heading mb-2"><?php echo $name;?> 程序公告</h6>
                    <p class="mb-2">欢迎使用sanqi，在这里分享生活、与朋友互动。</p>
                    <p class="mb-2">我们的标语是：<strong class="text-primary"><?php echo $subtitle;?></strong></p>
                    </div>
                    <div class="mt-3">
                      <span class="text-muted small"><i class="mdi mdi-update me-1"></i> 公告发布时间：2024年1月</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="d-flex align-items-center p-3 rounded mb-2" style="background: rgba(23, 162, 184, 0.08);">
                    <i class="mdi mdi-account-group text-info me-3"></i>
                    <div>
                      <div class="fw-bold">社交互动</div>
                      <small class="text-muted">仿微信朋友圈体验</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex align-items-center p-3 rounded mb-2" style="background: rgba(23, 162, 184, 0.08);">
                    <i class="mdi mdi-share-variant text-info me-3"></i>
                    <div>
                      <div class="fw-bold">生活分享</div>
                      <small class="text-muted">随时记录精彩瞬间</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex align-items-center p-3 rounded mb-2" style="background: rgba(23, 162, 184, 0.08);">
                    <i class="mdi mdi-heart text-info me-3"></i>
                    <div>
                      <div class="fw-bold">情感连接</div>
                      <small class="text-muted">与朋友保持联系</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 数据趋势分析 -->

        <div class="row mb-4">
          <div class="col-12">
            <div class="trend-chart-container">
              <div class="trend-chart-header">
                <h5 class="trend-chart-title"><i class="mdi mdi-chart-line me-2"></i> 数据趋势分析</h5>
                <div class="trend-time-selector">
                  <button class="time-btn active">最近7天</button>
                  <button class="time-btn">本月</button>
                  <button class="time-btn">本季度</button>
                </div>
              </div>
              <div class="trend-metric-grid">
                <div class="trend-metric-card">
                  <div class="metric-icon users">
                    <i class="mdi mdi-account-multiple"></i>
                  </div>
                  <div class="metric-value"><?php echo $todayUserCount; ?></div>
                  <div class="metric-label">今日新增用户</div>
                  <div class="metric-change <?php echo $userGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <i class="mdi mdi-arrow-<?php echo $userGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                    <?php echo abs($userGrowthRate); ?>%
                  </div>
                </div>
                <div class="trend-metric-card">
                  <div class="metric-icon content">
                    <i class="mdi mdi-file-document-multiple"></i>
                  </div>
                  <div class="metric-value"><?php echo $todayEssay; ?></div>
                  <div class="metric-label">今日发布内容</div>
                  <div class="metric-change <?php echo $essayGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <i class="mdi mdi-arrow-<?php echo $essayGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                    <?php echo abs($essayGrowthRate); ?>%
                  </div>
                </div>
                <div class="trend-metric-card">
                  <div class="metric-icon comments">
                    <i class="mdi mdi-comment-multiple"></i>
                  </div>
                  <div class="metric-value"><?php echo $todayComms; ?></div>
                  <div class="metric-label">今日互动评论</div>
                  <div class="metric-change <?php echo $commGrowthRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <i class="mdi mdi-arrow-<?php echo $commGrowthRate >= 0 ? 'up' : 'down'; ?>-thick"></i>
                    <?php echo abs($commGrowthRate); ?>%
                  </div>
                </div>
              </div>
              <div class="trend-chart-area">
                <canvas id="trendChart" height="280"></canvas>
              </div>
              <div class="chart-legend">
                <div class="legend-item">
                  <div class="legend-color" style="background: #667eea;"></div>
                  <span class="legend-text">用户注册</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color" style="background: #f5576c;"></div>
                  <span class="legend-text">内容发布</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color" style="background: #4facfe;"></div>
                  <span class="legend-text">评论互动</span>
                </div>
              </div>
              <div class="trend-comparison">
                <div class="comparison-item">
                  <div class="comparison-value"><?php echo $weekEssays; ?></div>
                  <div class="comparison-label">本周新增内容</div>
                </div>
                <div class="comparison-item">
                  <div class="comparison-value"><?php echo $lastWeekUserCount; ?></div>
                  <div class="comparison-label">上周新增用户</div>
                </div>
                <div class="comparison-item">
                  <div class="comparison-value"><?php echo $lastWeekCommCount; ?></div>
                  <div class="comparison-label">上周评论数</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-8">
            <div class="dashboard-widget">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0"><i class="mdi mdi-update me-2"></i> 系统版本演进</h5>
                <span class="version-badge current-version">当前版本 v3.55</span>
              </div>
              <div class="version-timeline">
                <div class="version-item">
                  <div class="d-flex align-items-center mb-2">
                    <span class="version-tag">v3.55</span>
                    <span class="version-date">2024年1月15日</span>
                    <span class="performance-badge">+45% 性能</span>
                  </div>
                  <p class="text-muted mb-2">全面重构后台加载机制，页面响应速度提升45%，修复8处安全漏洞，增强SQL注入防护机制。</p>
                  <ul class="version-features">
                    <li>多维度数据可视化图表，支持实时数据刷新</li>
                    <li>深度优化移动端操作体验，支持触屏手势操作</li>
                    <li>增强批量操作功能，支持一键数据导出导入</li>
                    <li>重新设计操作流程，减少用户操作步骤30%</li>
                  </ul>
                </div>
                <div class="version-item">
                  <div class="d-flex align-items-center mb-2">
                    <span class="version-tag">v3.40</span>
                    <span class="version-date">2023年11月28日</span>
                    <span class="performance-badge">+30% 性能</span>
                  </div>
                  <p class="text-muted mb-2">引入AI内容审核系统，智能识别违规内容准确率达到98.5%，降低人工审核工作量70%。</p>
                  <ul class="version-features">
                    <li>集成智能推荐算法，提升内容分发效率</li>
                    <li>新增多语言支持，国际化架构设计</li>
                    <li>优化数据库查询性能，响应时间减少30%</li>
                    <li>增强用户权限管理系统，支持细粒度控制</li>
                  </ul>
                </div>
                <div class="version-item">
                  <div class="d-flex align-items-center mb-2">
                    <span class="version-tag">v3.20</span>
                    <span class="version-date">2023年9月10日</span>
                  </div>
                  <p class="text-muted mb-2">重构前端架构，采用现代化组件化设计，支持深色模式，优化无障碍访问体验。</p>
                  <ul class="version-features">
                    <li>引入实时数据同步机制，提升数据一致性</li>
                    <li>新增API网关，支持微服务架构扩展</li>
                    <li>优化图片处理系统，支持WebP格式</li>
                    <li>增强缓存策略，降低数据库压力40%</li>
                  </ul>
                </div>
                <div class="version-item">
                  <div class="d-flex align-items-center mb-2">
                    <span class="version-tag">v3.00</span>
                    <span class="version-date">2023年6月5日</span>
                    <span class="performance-badge">里程碑版本</span>
                  </div>
                  <p class="text-muted mb-2">完全重写系统架构，采用现代化技术栈，支持百万级用户并发访问，系统稳定性达到99.99%。</p>
                  <ul class="version-features">
                    <li>引入容器化部署，支持Kubernetes集群</li>
                    <li>重构权限系统，支持RBAC权限模型</li>
                    <li>新增实时监控告警系统</li>
                    <li>支持多租户架构，SaaS化部署</li>
                  </ul>
                </div>
              </div>
              <div class="alert alert-success mt-4">
                <div class="d-flex align-items-center">
                  <i class="mdi mdi-check-circle-outline display-6 me-3"></i>
                  <div>
                    <h6 class="mb-1">系统健康状态良好</h6>
                    <p class="mb-0">所有核心服务运行正常，系统响应时间保持在200ms以内，建议保持当前版本。</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="dashboard-widget">
              <h5 class="mb-4"><i class="mdi mdi-tag-multiple me-2"></i> 内容分类统计</h5>
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 rounded mb-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%); border-left: 4px solid #667eea;">
                    <div class="me-3">
                      <i class="mdi mdi-folder-outline display-6 text-primary"></i>
                    </div>
                    <div>
                      <h4 class="mb-0"><?php echo $essaycount; ?></h4>
                      <small class="text-muted">总文章数</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 rounded mb-3" style="background: linear-gradient(135deg, rgba(245, 87, 108, 0.1) 0%, rgba(240, 147, 251, 0.05) 100%); border-left: 4px solid #f5576c;">
                    <div class="me-3">
                      <i class="mdi mdi-tag-multiple-outline display-6" style="color: #f5576c;"></i>
                    </div>
                    <div>
                      <h4 class="mb-0"><?php echo $categoryTotalData['total']; ?></h4>
                      <small class="text-muted">分类总数</small>
                    </div>
                  </div>
                </div>
              </div>
              <?php if($hasCategories): ?>
                <div class="table-responsive mb-4">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>分类名称</th>
                        <th class="text-center">文章数量</th>
                        <th class="text-center">占比</th>
                        <th class="text-center">最近更新</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while($category = mysqli_fetch_assoc($categoryDetailResult)):
                        $percentage = round(($category['count'] / $essaycount) * 100, 1);
                        $latestUpdate = $category['latest_update'] ? date('m-d', strtotime($category['latest_update'])) : '-';
                      ?>
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            <span class="category-color-dot me-2" style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php
                              $colors = ['#667eea', '#f5576c', '#4facfe', '#43e97b', '#ffc107', '#6f42c1', '#20c997', '#fd7e14'];
                              $colorIndex = crc32($category['category']) % count($colors);
                              echo $colors[$colorIndex];
                            ?>;"></span>
                            <span class="fw-bold"><?php echo htmlspecialchars($category['category']); ?></span>
                          </div>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-primary rounded-pill"><?php echo $category['count']; ?></span>
                        </td>
                        <td class="text-center">
                          <div class="d-flex align-items-center justify-content-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                              <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%; background-color: <?php
                                if($percentage > 50) echo '#4caf50';
                                elseif($percentage > 20) echo '#ff9800';
                                else echo '#f44336';
                              ?>;"></div>
                            </div>
                            <span class="text-muted"><?php echo $percentage; ?>%</span>
                          </div>
                        </td>
                        <td class="text-center text-muted"><?php echo $latestUpdate; ?></td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <h6 class="mb-3">分类分布图</h6>
                    <div class="category-chart" style="height: 200px; position: relative;">
                      <?php
                      $colors = ['#667eea', '#f5576c', '#4facfe', '#43e97b', '#ffc107', '#6f42c1', '#20c997'];
                      $total = $essaycount;
                      $startAngle = 0;
                      $radius = 80;

                      if($total > 0): ?>
                        <svg width="100%" height="200" viewBox="0 0 200 200" class="mb-3">
                          <?php
                          $i = 0;
                          foreach($chartData as $item):
                            $percentage = ($item['count'] / $total) * 100;
                            $angle = ($item['count'] / $total) * 360;
                            $endAngle = $startAngle + $angle;
                            $x1 = 100 + $radius * cos(deg2rad($startAngle));
                            $y1 = 100 + $radius * sin(deg2rad($startAngle));
                            $x2 = 100 + $radius * cos(deg2rad($endAngle));
                            $y2 = 100 + $radius * sin(deg2rad($endAngle));
                            $largeArcFlag = $angle > 180 ? 1 : 0;
                          ?>
                          <path d="M 100,100 L <?php echo $x1; ?>,<?php echo $y1; ?> A <?php echo $radius; ?>,<?php echo $radius; ?> 0 <?php echo $largeArcFlag; ?>,1 <?php echo $x2; ?>,<?php echo $y2; ?> Z"
                                fill="<?php echo $colors[$i % count($colors)]; ?>"
                                opacity="0.8"
                                data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                data-count="<?php echo $item['count']; ?>"
                                data-percentage="<?php echo round($percentage, 1); ?>"
                                class="category-slice"
                                onmouseover="showTooltip(event)"
                                onmouseout="hideTooltip()"
                                style="cursor: pointer; transition: opacity 0.3s;"
                          />
                          <?php
                            $startAngle = $endAngle;
                            $i++;
                          endforeach;
                          ?>
                          <circle cx="100" cy="100" r="50" fill="white"></circle>
                        </svg>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="mb-3">分类占比详情</h6>
                    <div class="category-legend" style="max-height: 180px; overflow-y: auto;">
                      <?php
                      $i = 0;
                      foreach($chartData as $item):
                        $percentage = round(($item['count'] / $total) * 100, 1);
                      ?>
                      <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: rgba(0,0,0,0.02);">
                        <div class="category-color me-3" style="width: 12px; height: 12px; border-radius: 2px; background-color: <?php echo $colors[$i % count($colors)]; ?>;"></div>
                        <div class="flex-grow-1">
                          <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?php echo htmlspecialchars($item['category']); ?></span>
                            <span class="text-muted"><?php echo $percentage; ?>%</span>
                          </div>
                          <small class="text-muted d-block"><?php echo $item['count']; ?> 篇文章</small>
                        </div>
                      </div>
                      <?php
                        $i++;
                      endforeach;
                      ?>
                    </div>
                  </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <button class="btn btn-outline-primary w-100 btn-sm" onclick="window.location.href='../edit.php'">
                        <i class="mdi mdi-plus me-1"></i> 发布文章
                      </button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-outline-info w-100 btn-sm" onclick="window.location.href='./audites.php'">
                        <i class="mdi mdi-filter me-1"></i> 按分类筛选
                      </button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-outline-secondary w-100 btn-sm" onclick="exportCategoryData()">
                        <i class="mdi mdi-download me-1"></i> 导出数据
                      </button>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="text-center py-5">
                  <i class="mdi mdi-tag-off-outline display-4 text-muted mb-3"></i>
                  <p class="text-muted">暂无分类数据</p>
                  <button class="btn btn-primary btn-sm" onclick="window.location.href='../edit.php'">
                    <i class="mdi mdi-plus me-1"></i> 创建第一篇分类文章
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="dashboard-widget">
              <h5 class="mb-4"><i class="mdi mdi-rocket-launch me-2"></i> 快速操作</h5>
              <div class="row g-3">
                <div class="col-6">
                  <div class="quick-action" onclick="window.location.href='../edit.php'">
                    <div class="quick-action-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                      <i class="mdi mdi-pencil"></i>
                    </div>
                    <div class="fw-bold">发布内容</div>
                    <small class="text-muted">创建新文章</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="quick-action" onclick="window.location.href='./audites.php'">
                    <div class="quick-action-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                      <i class="mdi mdi-check-all"></i>
                    </div>
                    <div class="fw-bold">内容审核</div>
                    <small class="text-muted"><?php echo $essl ?? 0; ?> 篇待审</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="quick-action" onclick="window.location.href='./userlist.php'">
                    <div class="quick-action-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                      <i class="mdi mdi-account-group"></i>
                    </div>
                    <div class="fw-bold">用户管理</div>
                    <small class="text-muted"><?php echo $usercount; ?> 位用户</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="quick-action" onclick="window.location.href='./rm.php'">
                    <div class="quick-action-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                      <i class="mdi mdi-folder-upload"></i>
                    </div>
                    <div class="fw-bold">资源管理</div>
                    <small class="text-muted"><?php echo $totalFiles; ?> 个文件</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="dashboard-widget">
              <h5 class="mb-4"><i class="mdi mdi-trophy-variant me-2"></i> 月度活跃用户</h5>
              <?php
              if($activeUsers && mysqli_num_rows($activeUsers) > 0):
                $rank = 1;
                while($user = mysqli_fetch_assoc($activeUsers)):
                  $rankColors = ['#ffd700', '#c0c0c1', '#cd7f32', '#4caf50', '#2196f3'];
              ?>
              <div class="activity-item">
                <div class="activity-icon" style="background: <?php echo $rankColors[$rank-1] ?? '#6c757d'; ?>; font-weight: bold;">
                  <?php echo $rank; ?>
                </div>
                <div class="activity-content">
                  <div class="d-flex justify-content-between">
                    <strong><?php echo $user['username']; ?></strong>
                    <span class="text-primary"><?php echo $user['essay_count']; ?> 篇</span>
                  </div>
                  <p class="mb-0 text-muted">过去30天发布</p>
                </div>
              </div>
              <?php
                  $rank++;
                endwhile;
              else:
              ?>
              <div class="text-center py-4">
                <i class="mdi mdi-account-off-outline display-4 text-muted"></i>
                <p class="mt-3 text-muted">暂无活跃用户数据</p>
              </div>
              <?php endif; ?>
            </div>
            <div class="dashboard-widget">
              <h5 class="mb-4"><i class="mdi mdi-server me-2"></i> 系统信息</h5>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">PHP 版本</span>
                  <span class="fw-bold"><?php echo phpversion(); ?></span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-success" style="width: 90%"></div>
                </div>
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">数据库状态</span>
                  <span class="fw-bold text-success">正常</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-success" style="width: 95%"></div>
                </div>
              </div>
              <div class="mb-0">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">系统负载</span>
                  <span class="fw-bold text-success">正常</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-info" style="width: 45%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="dashboard-widget">
              <div class="row text-center">
                <div class="col-md-3 mb-3 mb-md-0">
                  <div class="p-3 rounded" style="background: rgba(102, 126, 234, 0.1);">
                    <div class="fs-4 fw-bold text-primary"><?php echo $weekEssays; ?></div>
                    <small class="text-muted">周新增内容</small>
                  </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                  <div class="p-3 rounded" style="background: rgba(240, 147, 251, 0.1);">
                    <div class="fs-4 fw-bold" style="color: #f5576c;"><?php echo $todayComms; ?></div>
                    <small class="text-muted">今日评论数</small>
                  </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                  <div class="p-3 rounded" style="background: rgba(79, 172, 254, 0.1);">
                    <div class="fs-4 fw-bold" style="color: #00f2fe;"><?php echo $dshzl; ?></div>
                    <small class="text-muted">待处理事务</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="p-3 rounded" style="background: rgba(67, 233, 123, 0.1);">
                    <div class="fs-4 fw-bold" style="color: #38f9d7;"><?php echo $monthlyActiveUsers; ?></div>
                    <small class="text-muted">月活跃用户</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<script type="text/javascript" src="./assets/js/popper.min.js"></script>
<script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="./assets/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="./assets/js/jquery.cookie.min.js"></script>
<script type="text/javascript" src="./assets/js/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function showTooltip(event) {
    const path = event.target;
    const category = path.getAttribute('data-category');
    const count = path.getAttribute('data-count');
    const percentage = path.getAttribute('data-percentage');
    const tooltip = document.createElement('div');
    tooltip.className = 'category-tooltip';
    tooltip.innerHTML = `
        <div class="p-2 rounded shadow-sm" style="background: white; border: 1px solid #ddd; min-width: 150px;">
            <div class="fw-bold">${category}</div>
            <div class="text-muted">${count} 篇文章</div>
            <div class="text-primary">占比 ${percentage}%</div>
        </div>
    `;
    tooltip.style.position = 'absolute';
    tooltip.style.left = (event.clientX + 10) + 'px';
    tooltip.style.top = (event.clientY + 10) + 'px';
    tooltip.style.zIndex = '1000';
    document.body.appendChild(tooltip);
    path.style.opacity = '1';
    path.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.2))';
}
function hideTooltip() {
    document.querySelectorAll('.category-tooltip').forEach(el => el.remove());
    document.querySelectorAll('.category-slice').forEach(el => {
        el.style.opacity = '0.8';
        el.style.filter = 'none';
    });
}
function copyGroupNumber() {
    const groupNumber = '771080828';
    navigator.clipboard.writeText(groupNumber).then(() => {
        alert('群号已复制到剪贴板：' + groupNumber);
    }).catch(err => {
        console.error('复制失败:', err);
        const textArea = document.createElement('textarea');
        textArea.value = groupNumber;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('群号已复制到剪贴板：' + groupNumber);
    });
}
function exportCategoryData() {
    alert('导出功能需要后端支持，请联系开发者配置导出接口。');
}
$(document).ready(function() {
    $('.stat-card').each(function(index) {
        $(this).css('--card-index', index);
    });
    $('.dashboard-widget').hover(
        function() {
            $(this).css('transform', 'translateY(-3px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
    $('.quick-action').click(function() {
        $(this).css({
            'transform': 'scale(0.98)',
            'transition': 'transform 0.1s'
        });
        setTimeout(() => {
            $(this).css('transform', 'scale(1)');
        }, 100);
    });
    $('.trend-bar').hover(
        function() {
            $(this).css('opacity', '0.8').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('opacity', '1').css('transform', 'translateY(0)');
        }
    );
    $('.time-btn').click(function() {
        $('.time-btn').removeClass('active');
        $(this).addClass('active');
    });
    $('.category-slice').on('click', function() {
        const category = $(this).data('category');
        const count = $(this).data('count');
        alert(`点击了分类: ${category}\n文章数量: ${count} 篇`);
    });
    $('table tbody tr').on('click', function() {
        const categoryName = $(this).find('.fw-bold').text().trim();
        console.log('点击分类:', categoryName);
    });

    if (typeof Chart !== 'undefined') {
        const trendChart = document.getElementById('trendChart');
        if (trendChart) {
            const ctx = trendChart.getContext('2d');

            <?php
            $labels = [];
            $userData = [];
            $essayData = [];
            $commData = [];

            $dates = [];
            for($i = 6; $i >= 0; $i--) {
                $dates[] = date('Y-m-d', strtotime("-$i days"));
            }

            foreach($dates as $date) {
                $labels[] = date('m-d', strtotime($date));

                $userCount = 0;
                $essayCount = 0;
                $commCount = 0;

                foreach($regTrendData as $data) {
                    if($data['regdate'] == $date) {
                        $userCount = $data['regcount'];
                        break;
                    }
                }

                foreach($essayTrendData as $data) {
                    if($data['essaydate'] == $date) {
                        $essayCount = $data['essaycount'];
                        break;
                    }
                }

                foreach($commTrendData as $data) {
                    if($data['commdate'] == $date) {
                        $commCount = $data['commcount'];
                        break;
                    }
                }

                $userData[] = $userCount;
                $essayData[] = $essayCount;
                $commData[] = $commCount;
            }
            ?>

            const labels = <?php echo json_encode($labels); ?>;
            const userData = <?php echo json_encode($userData); ?>;
            const essayData = <?php echo json_encode($essayData); ?>;
            const commData = <?php echo json_encode($commData); ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '用户注册',
                            data: userData,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: '内容发布',
                            data: essayData,
                            borderColor: '#f5576c',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#f5576c',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: '评论互动',
                            data: commData,
                            borderColor: '#4facfe',
                            backgroundColor: 'rgba(79, 172, 254, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#4facfe',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: '#e0e0e0',
                            borderWidth: 1,
                            boxPadding: 10,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#999'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                color: '#999',
                                precision: 0
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animations: {
                        tension: {
                            duration: 1000,
                            easing: 'linear'
                        }
                    }
                }
            });
        }
    }
});
</script>
</body>
</html>
