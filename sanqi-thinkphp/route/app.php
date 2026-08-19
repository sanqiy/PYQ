<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

use think\facade\Route;

// ============================================================
// 安装路由（仅在未安装时生效）
// ============================================================

Route::group('install', function () {
    Route::get('step2', 'Install/step2');
    Route::get('step3', 'Install/step3');
    Route::get('complete', 'Install/complete');
    Route::post('testDb', 'Install/testDb');
    Route::post('doInstall', 'Install/doInstall');
    Route::get('/', 'Install/index');
})->middleware('check_installed');

// ============================================================
// 前台页面路由
// ============================================================

Route::get('/', 'Index/index');
Route::get('home', 'Home/index');
Route::get('user/<hash>', 'User/index');
Route::get('edit/<cid>', 'Edit/index');
Route::get('edit', 'Edit/index');
Route::get('view/<cid>', 'View/index');
Route::get('setup', 'Setup/index');
Route::get('repass', 'Repass/index');
Route::get('sticky', 'Sticky/index');
Route::get('sticky/<type>', 'Sticky/index');
Route::get('logout', 'api/Auth/logout');
Route::get('rss', 'Index/rss');

// ============================================================
// API 路由
// ============================================================

Route::group('api', function () {

    // --- 文章 ---
    Route::post('load-more', 'api/Article/loadMore');
    Route::post('home/load-more', 'api/HomeApi/loadMore');
    Route::post('article/save', 'api/Article/save');
    Route::post('article/markdown-preview', 'api/Article/markdownPreview');
    Route::post('article/autosave-draft', 'api/Article/autosaveDraft');
    Route::get('article/draft-versions', 'api/Article/draftVersions');
    Route::post('article/delete', 'api/Article/delete');
    Route::post('article/privacy', 'api/Article/privacy');
    Route::post('article/pin', 'api/Article/pin');
    Route::post('article/user-pin', 'api/Article/userPin');

    // --- 认证 ---
    Route::post('login', 'api/Auth/login');
    Route::post('login/verify-2fa', 'api/Auth/verify2fa');
    Route::post('register', 'api/Auth/register');
    Route::post('repass', 'api/Auth/repass');
    Route::post('site-password-verify', 'api/Auth/sitePasswordVerify');

    // --- 评论 ---
    Route::post('comment/submit', 'api/Comment/submit');
    Route::post('comment/load', 'api/Comment/load');
    Route::post('comment/delete', 'api/Comment/delete');

    // --- 点赞 ---
    Route::post('like/toggle', 'api/Like/toggle');

    // --- 用户 ---
    Route::post('user/update', 'api/User/update');
    Route::post('user/logout-all', 'api/User/logoutAll');
    Route::post('user/avatar', 'api/User/avatar');
    Route::post('user/cover', 'api/User/cover');
    Route::post('user/qr', 'api/User/qr');
    Route::post('user/email-notify', 'api/User/emailNotify');
    Route::get('ip-location', 'api/User/ipLocation');

    // --- 上传 ---
    Route::post('upload/image', 'api/Upload/image');
    Route::post('upload/video', 'api/Upload/video');
    Route::post('upload/file', 'api/Upload/file');

    // --- 附件 ---
    Route::get('attachment/download', 'api/Attachment/download');

    // --- 消息 ---
    Route::post('message/operate', 'api/Message/operate');

    // --- 投票 ---
    Route::post('poll/vote', 'api/Poll/vote');
    Route::get('poll/result', 'api/Poll/result');

    // --- 音乐 ---
    Route::get('music/proxy', 'api/Music/proxy');
    Route::get('music/qq-proxy', 'api/Music/qqProxy');
    Route::get('music/kugou-proxy', 'api/Music/kugouProxy');
    Route::get('music/kuwo-proxy', 'api/Music/kuwoProxy');
    Route::post('music/random', 'api/Music/random');
    Route::post('music/netease', 'api/Music/netease');
    Route::post('music/qq', 'api/Music/qq');
    Route::post('music/kugou', 'api/Music/kugou');
    Route::post('music/kuwo', 'api/Music/kuwo');
    Route::post('douyin/parse', 'api/Douyin/parse');
});

// ============================================================
// 管理后台路由
// ============================================================

Route::group('admin', function () {
    // --- 无需管理员验证（登录页面本身） ---
    Route::get('login', 'Login/index');

    // --- 仪表盘 ---
    Route::get('/', 'Index/index');

    // --- 基础设置 ---
    Route::get('basic', 'Basic/index');
    Route::post('basic/save', 'Basic/save');
    Route::post('basic/upload', 'Basic/upload');

    // --- 内容管理 ---
    Route::get('content', 'Content/index');
    Route::post('content/save', 'Content/save');

    // --- 权限设置 ---
    Route::get('authority', 'Authority/index');
    Route::post('authority/save', 'Authority/save');

    // --- 文章审核 ---
    Route::get('audites', 'Audites/index');
    Route::post('audites/audit', 'Audites/audit');

    // --- 评论审核 ---
    Route::get('auditco', 'Auditco/index');
    Route::post('auditco/audit', 'Auditco/audit');
    Route::post('auditco/batch', 'Auditco/batch');
    Route::post('auditco/edit', 'Auditco/edit');
    Route::post('auditco/blacklist', 'Auditco/blacklist');

    // --- 系统通知 ---
    Route::get('notification', 'Notification/index');
    Route::post('notification/send', 'Notification/send');

    // --- 用户管理 ---
    Route::get('userlist', 'UserList/index');
    Route::post('userlist/update', 'UserList/update');

    // --- 友链管理 ---
    Route::get('linkset', 'Linkset/index');
    Route::post('linkset/add', 'Linkset/add');
    Route::post('linkset/update', 'Linkset/update');
    Route::post('linkset/delete', 'Linkset/delete');

    // --- 表情管理 ---
    Route::get('emojis', 'Emojiset/index');
    Route::post('emojis/add', 'Emojiset/add');
    Route::post('emojis/update', 'Emojiset/update');
    Route::post('emojis/delete', 'Emojiset/delete');
    Route::post('emojis/toggle', 'Emojiset/toggle');

    // --- 邮件设置 ---
    Route::get('emailset', 'Emailset/index');
    Route::post('emailset/save', 'Emailset/save');
    Route::post('emailset/test', 'Emailset/test');

    // --- 云存储设置 ---
    Route::get('cloudset', 'Cloudset/index');
    Route::post('cloudset/save', 'Cloudset/save');
    Route::post('cloudset/test', 'Cloudset/test');
    Route::post('cloudset/s3-defaults', 'Cloudset/s3Defaults');

    // --- 安全设置与二次验证 ---
    Route::get('security', 'Security/index');
    Route::post('security/save', 'Security/save');
    Route::post('security/regenerate-totp', 'Security/regenerateTotp');

    // --- 上传文件管理 ---
    Route::get('uploads', 'Uploads/index');
    Route::post('uploads/delete', 'Uploads/delete');

    // --- 操作日志 ---
    Route::get('logs', 'Logs/index');

    // --- 数据库备份与迁移 ---
    Route::get('database', 'DatabaseTools/index');
    Route::post('database/backup', 'DatabaseTools/backup');
    Route::post('database/restore', 'DatabaseTools/restore');
    Route::post('database/delete', 'DatabaseTools/delete');
    Route::post('database/migrate', 'DatabaseTools/migrate');
    Route::get('upgrade', 'Upgrade/index');
    Route::post('upgrade/migrate', 'Upgrade/migrate');

    // --- 配置版本 ---
    Route::get('config-versions', 'ConfigVersions/index');
    Route::post('config-versions/snapshot', 'ConfigVersions/snapshot');
    Route::post('config-versions/restore', 'ConfigVersions/restore');

    // --- 邮件模板 ---
    Route::get('mail-templates', 'MailTemplates/index');
    Route::post('mail-templates/save', 'MailTemplates/save');
})->prefix('admin/')->middleware(['admin_auth']);

// ============================================================
// 服务端路由（安装上报 + 版本检查 + 管理后台）
// ============================================================
// 公开 API（无需认证）
Route::group('server/api', function () {
    Route::post('install/report', 'server.Api/installReport');
    Route::get('version/check', 'server.Api/versionCheck');
});
// 管理后台（复用管理员登录）
Route::group('server/admin', function () {
    Route::get('/', 'server.Admin/index');
    Route::get('login', 'server.Admin/login');
    Route::post('version/save', 'server.Admin/saveVersion');
    Route::post('version/delete', 'server.Admin/deleteVersion');
    Route::post('version/toggle', 'server.Admin/toggleVersion');
    Route::get('logout', 'server.Admin/logout');
})->middleware(['server_admin_auth']);
