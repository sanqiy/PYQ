<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 公共助手函数入口 — 按职责拆分到 helper/ 子目录
 *
 * 原文件超过 1100 行，已按以下职责拆分：
 *   base.php     — 基础工具（isFlag、randomString、strCut 等）
 *   security.php — 安全过滤（sanitizeHtml、cleanArticleHtml 等）
 *   url.php      — URL与资源路径（assetUrl、thumbUrl、staticUrl）
 *   auth.php     — 用户认证与密码（visitorIdentity、verifyPassword 等）
 *   emoji.php    — 表情渲染（renderArticleEmojis）
 *   markdown.php — Markdown渲染（renderMarkdownArticle 等）
 *   format.php   — 日期时间与格式化（formatFriendlyDate 等）
 *   admin.php    — 后台管理辅助（authority_value、admin_active 等）
 */

$helperDir = __DIR__ . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR;
require_once $helperDir . 'base.php';
require_once $helperDir . 'security.php';
require_once $helperDir . 'url.php';
require_once $helperDir . 'auth.php';
require_once $helperDir . 'emoji.php';
require_once $helperDir . 'markdown.php';
require_once $helperDir . 'format.php';
require_once $helperDir . 'admin.php';
require_once $helperDir . 'email.php';
