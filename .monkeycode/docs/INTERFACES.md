# sanqi 接口文档

sanqi 提供两套接口：原生版（`sanqi/api/*.php`，PHP 直接输出 JSON）与 ThinkPHP 版（`/api`、`/admin`、`/server` 路由）。两套接口业务等价，ThinkPHP 版为超集。

前端页面与 JS（`assets/js/*.js`）通过原生接口交互；ThinkPHP 版由路由映射到 `app/controller/api/*` 控制器。

---

## 通用约定

### 原生版
- 接口路径：`./api/<name>.php`（相对站点根目录）。
- 除特别说明外全部要求 **POST**，`GET` 直接返回 `{"code":"201","msg":"非法请求"}`。
- 登录态基于 Cookie：`username`（账号）+ `passid`（唯一会话 id），在 `api/wz.php` 中校验一致性；不一致会清空 Cookie 并提示重新登录。
- 输入过滤：`addslashes(htmlspecialchars())`；输出 `json_encode(..., JSON_UNESCAPED_UNICODE)`。
- 被封禁用户（`ban` 非 0）在各接口入口被拦截。
- 部分写接口要求额外 `allkey`（秘钥，Session 下发）校验，见 [页面密码与秘钥](#页面密码与秘钥)。

### 成功/失败响应示意
```json
{"code": "201", "msg": "错误信息"}
```
成功时返回对应数据（部分接口直接以 HTML 片段输出，例如置顶列表、评论列表）。

---

## 原生版接口

### 会话与账号

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/login.php` | POST | 账号密码登录，校验密码（`passid` 会话生成与 Cookie 下发） |
| `api/reg.php` | POST | 用户注册（受 `regqx` 注册开关约束） |
| `api/repass.php` | POST | 重置密码（邮件验证） |
| `api/updata.php` | POST | 更新个人资料（昵称/签名/网址/邮箱等，写入 `user` 表） |
| `api/emaitzzt.php` | POST | 切换用户「收到消息邮件通知」开关（`user.esseam` 0/1） |

### 文章

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/form.php` | POST | **发布/编辑文章**。校验会话、封禁、发布权限（`essqx`/`kqsy` 开关），读取 `configx(filtertext)` 过滤词过滤，写入 `essay` 表；支持图文/视频/音乐/长文章；按 `ptpaud` 审核配置决定是否直接可见 |
| `api/api.php` | POST | 文章流分页加载（`page` 参数），支持关键词搜索 `so`，过滤未审核与隐藏文章 |
| `api/homeapi.php` | POST | 用户主页文章流分页（`page` + 目标用户），仅显示已审核可见文章 |
| `api/delewz.php` | POST | 删除自己的文章（`essay`） |
| `api/homeptpzt.php` | POST | 切换文章可见/隐藏（`essay.ptpys` 0/1） |
| `api/topes.php` | POST | 置顶文章列表（读取 `admin.topes` 中的文章 id 列表并渲染内容） |
| `api/topeshome.php` | POST? | 首页版置顶列表（同样读取 `admin.topes` 渲染） |
| `api/sqtopes.php` | POST | 设置置顶文章 id 列表（写入 `admin.topes`） |
| `api/pagepassver.php` | POST | 页面访问密码验证 |
| `api/comm.php` | POST | 加载文章评论列表（按 `wzcid` 过滤，排除未审核） |
| `api/fbpl.php` | POST | 发表评论（校验登录、邮箱格式、文章是否存在） |
| `api/delcomm.php` | POST | 删除自己的评论 |

### 点赞与留言

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/lcke.php` | POST | 点赞/取消点赞（`lcke` 表，含防刷用户 IP 校验） |
| `api/messg.php` | POST | 留言板：读取/更新留言（`message` 表） |

### 邮件

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/sendmail.php` | POST | 发送邮件（测试/注册/通知，引用 `site/mailtemplate.php` 模板） |

### 上传与云存储

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/uploadavatar.php` | POST | 上传头像（写入 `user.img`，落盘 `user/` 目录） |
| `api/uploadcover.php` | POST | 上传主页背景图（写入 `user.homeimg`） |
| `api/upyun.php` | POST | 又拍云（云存储）上传保存 |

### 工具

| 接口 | 方法 | 说明 |
|------|------|------|
| `api/symusic.php` | POST | 音乐搜索/解析 |
| `api/ip.php` | POST | IP 归属地查询 |
| `api/wz.php` | - | 站点引导：加载配置与登录态（被各页面 include，不独立调用） |

### 页面密码与秘钥

`form.php` 等写接口校验 `allkey`：先 `session_start()`，比对 `$_SESSION["allkey"]`，否则比对站点内建秘钥 `md5(md5(HTTP_HOST + "fC4gT5uU2pW7kU8eL8dI4nK5xE9uT6iW"))`。该内建秘钥字符串由程序固定，实际部署建议整体审查。

---

## 原生版后台接口（`admin/api/`）

| 接口 | 说明 |
|------|------|
| `admin/api/login.php` | 后台登录（验证管理员账号） |
| `admin/api/adminupdata.php` | 更新后台设置（站点信息、基础配置持久化） |
| `admin/api/updatever.php` | 版本更新检查 |
| `admin/api/userinfo.php` | 用户信息查询 |
| `admin/api/emailtest.php` | 邮件发送测试（引用站点邮件模板） |
| `admin/api/linkset.php` | 友链管理 |
| `admin/api/escoaud.php` | 评论/文章批量审核 |

后台页面 `admin/*.php`（`login/index/basic/authority/userlist/auditco/audites/emailset/imgset/linkset/rm/authver`）通过 `api/wz.php` 统一鉴权，仅管理员角色可进入。

---

## ThinkPHP 版接口（路由 `route/app.php`）

### 前台页面（GET）

| 路由 | 控制器方法 | 说明 |
|------|------|------|
| `/` | Index/index | 首页 |
| `/home` | Home/index | 用户主页 |
| `/user/<hash>` | User/index | 用户资料页 |
| `/edit[/<cid>]` | Edit/index | 编辑器 |
| `/view/<cid>` | View/index | 文章详情 |
| `/setup` | Setup/index | 个人设置 |
| `/repass` | Repass/index | 找回密码 |
| `/sticky[/<type>]` | Sticky/index | 置顶 |
| `/logout` | api/Auth/logout | 退出登录 |
| `/rss` | Index/rss | RSS 订阅 |

### API（`/api` 前缀，POST 为主）

**文章**
- `POST api/article/save` 保存/发布文章
- `POST api/article/markdown-preview` Markdown 预览
- `POST api/article/autosave-draft`、`GET api/article/draft-versions` 草稿自动保存/版本列表
- `POST api/article/delete`、`privacy`（可见性）、`pin`（管理员置顶）、`user-pin`
- `POST api/load-more`、`POST api/home/load-more` 文章流分页

**认证**
- `POST api/login`、`register`、`repass`、`site-password-verify`（站点访问密码）
- `POST api/login/verify-2fa` 2FA 二次验证

**评论 / 点赞**
- `POST api/comment/submit`、`load`、`delete`
- `POST api/like/toggle`

**用户**
- `POST api/user/update`、`logout-all`、`avatar`、`cover`、`qr`（收款码）、`email-notify`
- `GET api/ip-location` IP 定位

**上传 / 附件**
- `POST api/upload/image|video|file`
- `GET api/attachment/download`

**消息 / 投票 / 音乐 / 抖音**
- `POST api/message/operate` 通知操作
- `POST api/poll/vote`、`GET api/poll/result` 投票
- `GET|POST api/music/proxy|qq-proxy|kugou-proxy|kuwo-proxy|random|netease|qq|kugou|kuwo` 多源音乐
- `POST api/douyin/parse` 抖音视频解析

### 管理后台（`/admin` 前缀，需管理员认证，`AdminAuth` 中间件）

| 路由组 | 说明 |
|------|------|
| `GET admin/login` | 后台登录 |
| `GET admin/` | 仪表盘 |
| `admin/basic`（GET/POST save/upload） | 基础设置 |
| `admin/content`（GET/POST save） | 内容管理 |
| `admin/authority`（GET/POST save） | 权限设置 |
| `admin/audites`（GET/audit） | 文章审核 |
| `admin/auditco`（GET/audit/batch/edit/blacklist） | 评论审核 |
| `admin/notification/send` | 系统通知发送 |
| `admin/userlist`（GET/update） | 用户管理 |
| `admin/linkset`（add/update/delete） | 友链管理 |
| `admin/emojis`（add/update/delete/toggle） | 表情管理 |
| `admin/emailset`（save/test） | 邮件设置与测试 |
| `admin/cloudset`（save/test/s3-defaults） | 云存储设置 |
| `admin/security`（save/regenerate-totp） | 安全设置与 TOTP 重新生成 |
| `admin/uploads`（delete） | 上传文件管理 |
| `admin/logs` | 操作日志 |
| `admin/database`（backup/restore/delete/migrate） | 数据库备份与迁移 |
| `admin/upgrade`（migrate） | 系统升级迁移 |
| `admin/config-versions`（snapshot/restore） | 配置版本快照/回滚 |
| `admin/mail-templates`（save） | 邮件模板编辑 |

### 服务端路由（`/server` 前缀）

- `POST server/api/install/report` 安装上报（公开）
- `GET server/api/version/check` 版本检查（公开）
- `GET server/admin/`、`login`、`logout`、`POST version/save|delete|toggle` 版本管理后台（`ServerAdminAuth` 认证）

### 认证方式

ThinkPHP 版对应中间件链：

| 中间件 | 保护对象 | 能力 |
|--------|---------|------|
| `CheckInstalled` / `EnsureInstalled` | 全局/安装路由 | 未安装跳安装向导 |
| `AuthMiddleware` | 前台页面 + `/api` | Cookie/Token 会话、封禁拦截 |
| `AdminAuth` | `/admin` | 管理员角色校验 |
| `ServerAdminAuth` | `/server/admin` | 服务端管理校验 |
| `CsrfVerify` | 表单/API | CSRF Token 校验 |

控制器侧安全：`app/helper/security.php` 的 `safeFilter()`/`cleanXss()`/`cleanArticleHtml()` 清洗输入，`CommentSecurityService` 处理评论防刷。

---

## 数据返回格式

- **原生版**：`{"code":"201","msg":"..."}` 表示错误；成功返回具体业务字段或 HTML 片段（由调用 JS 拼接）。
- **ThinkPHP 版**：`api/Auth`、`api/Article` 等控制器统一返回 `SuccessJsonMessage`/`ErrorJsonMessage`（见 `app/controller/api` 具体实现），页面渲染由模板（`app/view/`）负责。

> 提示：如需精确的请求/响应字段，请直接阅读对应控制器源文件（如 `sanqi-thinkphp/app/controller/api/Article.php` 的 `save` 方法）。本文不臆造未在代码中出现的字段。