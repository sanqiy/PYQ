# 朋友圈系统（Moments）

合并 5 个版本（4 个 PHP 原生版 + 1 个 ThinkPHP 版）后生成的最优版本。

## 项目目录

- `moments/` — PHP 原生合并版（最优 PHP 版本，开箱即用）
- `moments-thinkphp/` — ThinkPHP 8 现代版（功能最全）
- `合并报告.md` — 详细合并分析与说明

## 快速开始

### PHP 版
1. 将 `moments/` 部署到 Web 服务器
2. 访问 `http://你的域名/install/` 安装
3. 管理员默认密码：`123456`

### ThinkPHP 版
1. 配置 `.env` 数据库连接
2. 访问 `http://你的域名/public/` 安装
3. 需 PHP 8.0+ 和 Composer 依赖