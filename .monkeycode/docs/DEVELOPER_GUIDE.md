# sanqi 开发者指南

## 项目目的

sanqi 是 MySQL + PHP 的单用户「朋友圈」风格社交分享系统，仓库同时维护两个实现：`sanqi/`（原生版，零依赖）与 `sanqi-thinkphp/`（ThinkPHP 8 增强版）。两者共享核心数据模型，供站长在任意支持 PHP 的主机上快速搭建个人分享站点。

**核心职责**:
- 提供可安装、可配置的单用户社交主页（文章、评论、点赞、留言、友链）
- 提供完整管理后台（站点设置、用户与内容管理、审核、邮件、云存储、安全）
- 原生版与 ThinkPHP 版互为功能参照，迁移核心逻辑时保持一致

**相关系统**:
- `sanqi/` - PHP 原生版，页面直接 PHP 渲染，零框架
- `sanqi-thinkphp/` - ThinkPHP 8 MVC 重构增强版（功能超集）

---

## 环境搭建

### 前置条件

- PHP：原生版需 PHP 7+ 且开启 `mysqli`、`fileinfo`/`gd`(图片处理)；ThinkPHP 版需 PHP 8.0+
- MySQL 5.7+（InnoDB / utf8mb4）
- Web 服务器：Apache / Nginx（ThinkPHP 版需将站点根目录指向 `public/`）
- Composer（仅 ThinkPHP 版，`vendor/` 可用时跳过）

### 安装

#### 原生版

```bash
git clone <repo-url> sanqi
cd sanqi
# 直接上传到 Web 根目录，浏览器访问：
#   http://你的域名/install/
# 按向导填写数据库信息并创建数据表，随后：
# - 根目录生成 config.php（数据库连接）
# - install/ 下生成 ins.bak（安装标记）
# 安装后建议删除 install/ 目录或其中的 ins.bak
```

#### ThinkPHP 版

```bash
git clone <repo-url> sanqi-thinkphp
cd sanqi-thinkphp

# 初始化依赖（vendor 目录缺失时）
composer install

# 配置数据库
cp .example.env .env
# 编辑 .env：DB_HOST / DB_NAME / DB_USER / DB_PASS

# 导入数据表
mysql -u<user> -p <dbname> < database/install.sql

# 站点根目录指向 public/
php think run   # 开发预览，或配置 Nginx 到 public/
```

### 安装标记

| 版本 | 标记 | 说明 |
|------|------|------|
| 原生版 | `install/ins.bak` | 存在即视为已安装 |
| ThinkPHP 版 | 数据库 `installation` 表 / `configx` 中安装状态 | 由 `CheckInstalled`/`EnsureInstalled` 中间件使用 |

---

## 开发工作流

### 代码质量工具

项目为纯 PHP 传统工程，无内置 lint/测试框架：

| 工具 | 命令 | 目的 |
|------|------|------|
| PHP 语法检查 | `php -l <file>` | 语法校验（本环境可改用 tree-sitter-php 校验） |
| Composer | `composer validate` | ThinkPHP 版依赖检查 |
| 数据库迁移 | `php think migrate` | ThinkPHP 版执行 `database/*.php` 迁移 |

> 原生版无 Composer/迁移机制；新增数据表变更需在 `install/install.php` 建表段同步维护。

### 分支策略与提交

- `main` - 生产就绪代码
- 改动遵循「先提交子模块/子仓库，再更新引用」的节奏（本仓库无 submodule，直接提交）
- 提交前请清理调试输出与临时文件

---

## 常见任务

### 新增一个 API（原生版）

1. 在 `sanqi/api/<name>.php` 创建文件，**开头禁止 GET 访问**：
   ```php
   if ($_SERVER["REQUEST_METHOD"] !== "POST") {
       $arr = [["code" => "201", "msg" => "非法请求"]];
       exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
   }
   ```
2. 引用 `../config.php` 连接数据库；如需登录态校验可 include `api/wz.php` 的校验逻辑或复用鉴权段。
3. 输入一律 `addslashes(htmlspecialchars($_POST["..."], ...))`。
4. 结果 `json_encode($arr, JSON_UNESCAPED_UNICODE)` 输出。
5. 在对应前端 JS（`assets/js/*.js`）中添加 AJAX 调用。

### 新增一个 API（ThinkPHP 版）

1. 在 `app/controller/api/` 新增控制器（或扩展现有控制器）实现方法。
2. 在 `route/app.php` 的 `api` 路由组注册路由。
3. 业务逻辑下沉到 `app/service/` 对应服务（若为新领域则新建服务类）。
4. 使用 `app/validate/` 验证器或 `$request->validate()` 做输入校验。
5. 返回统一 JSON（`json()` 响应）。

### 新增数据库迁移

1. 在 `sanqi-thinkphp/database/migrate_<描述>.sql` 编写 `ALTER TABLE`/`INSERT` SQL。
2. 通过 `admin/database/migrate` 接口或 `php think up` 应用。
3. 同步更新 `database/install.sql` 保持全新安装一致。

### 邮件功能

- 原生版：模板位于 `sanqi/site/mailtemplate.php`（占位符 `{{ wz_name }}`/`{{ title }}`/`{{ text }}`/`{{ url }}`），发送走 `sanqi/site/email/email.php` + 内置 PHPMailer。
- ThinkPHP 版：`app/service/EmailService.php` 处理发送，`EmailTemplateService` 提供统一 HTML 外壳（`SHELL` + `decorate()`）；邮件模板可在后台 `admin/mail-templates` 编辑。
- 后台「邮件设置」页可发送测试邮件验证 SMTP。

### 修复 Bug（原生版注意点）

1. 复现问题（配置参数、开关状态、SQL 过滤）。
2. 定位到对应 `api/*.php` 或页面文件。
3. 保持「修改前先备份再小步修改」的传统工程习惯。
4. 检查同类接口是否复用同一段校验逻辑（很多鉴权代码是逐文件复制的）。

---

## 编码规范

### 文件组织与命名

- 原生版：`api/<名词>.php`、`admin/<页面>.php`、`assets/js/<页面>.js`；小写英文文件名。
- ThinkPHP 版：控制器 `Xxx.php`（PascalCase）、服务 `XxxService.php`、模型 `Xxx.php`（对应表名小写）。

### 代码风格（两版本差异，均已在历史提交中统一版权头）

| 版本 | 风格 | 缩进 | 版权头 |
|------|------|------|--------|
| `sanqi/` | PHP 原生风格 | Tab | `@copyright Copyright (c) sanqi` |
| `sanqi-thinkphp/` | PSR-12 风格 | 4 空格 | 同上 |

两个版本文件的 `/ **` 版权注释已统一为 sanqi 品牌，仅 `vendor/` 第三方依赖保留原始版权。

### 命名

| 类型 | 约定 | 示例 |
|------|------|------|
| 原生版 API | `api/<动词/名词>.php` | `delcomm.php` |
| 原生版变量 | `$` + 拼音缩写 | `$user_zh`, `$passid` |
| ThinkPHP 控制器 | PascalCase | `Article.php` |
| ThinkPHP 服务 | `XxxService` | `EmailService` |
| ThinkPHP 模型 | 表名 PascalCase | `Configx` |

### 安全规范

- **输入**：原生版所有外部输入 `addslashes(htmlspecialchars())`；ThinkPHP 版使用 `app/helper/security.php` 的 `safeFilter()`、`cleanXss()`、`cleanArticleHtml()`。
- **SQL**：原生版使用字符串拼接 SQL + 过滤转义（遗留风格，新代码应使用 ThinkPHP ORM 参数绑定）。
- **会话**：原生版 Cookie 会话（`username`+`passid`），封禁用户在各接口拦截；ThinkPHP 版中间件统一处理 + CSRF 校验。
- **密钥**：`.env` / `config.php` 禁止入库上传；后台邮箱 SMTP 密码、云存储密钥必须走环境变量或后台密文存储；绝不硬编码 API Key（对外部服务调用一律用占位符读取环境变量）。

### 日志

- 原生版：少量文件日志（如 `site/email/log.txt` 邮件发送日志）。
- ThinkPHP 版：`runtime/log/` 由框架日志驱动，`ExceptionLog` 中间件捕获异常，`AdminLogService` 记录后台操作日志。

### 测试

- 项目当前无单元测试框架。
- 变更验证手段：`php -l` 语法校验 + 手工功能回归（安装向导 → 发布文章 → 评论/点赞 → 后台审核） + 数据库迁移空跑。