# sanqi/（PHP 原生版）

PHP 原生版朋友圈系统，零框架、零 Composer 依赖，上传即用。

## 结构

```
sanqi/
├── index.php # 首页入口（站点信息 + 首页流）
├── home.php # 用户主页
├── view.php # 文章详情
├── edit.php # 编辑器（发布文章）
├── setup.php # 个人资料设置
├── repass.php # 修改/重置密码
├── archives.php # 文章归档
├── 404.php
├── api/ # 前端接口（26 个 JSON 端点 + wz.php）
├── admin/ # 管理后台（11 页面 + 6 个 admin/api 接口）
├── install/ # 可视化安装向导（建表 + 生成 config.php + ins.bak）
├── site/ # 站点侧组件：邮件模板/发送、播放器、页面加密、音乐库
├── user/ # 用户数据目录（头像/上传文件/背景）
└── assets/ # 静态资源（css/js/img/owo 表情/mesg 弹窗）
```

## 关键文件

| 文件 | 目的 |
|------|------|
| `index.php` | 唯一前端入口，检查安装状态并渲染首页流 |
| `api/wz.php` | 配置加载 + Cookie 登录态校验（被所有页面/接口复用） |
| `api/form.php` | 发布/编辑文章核心接口（含权限与过滤词校验） |
| `admin/basic.php` | 基础设置（站点信息、开关、通知、音乐等） |
| `install/install.php` | 建表 + 生成根目录 `config.php` |
| `assets/js/home.js` / `view.js` / `edit.js` | 页面交互脚本 |

## 依赖

**本模块依赖**:
- MySQL（`mysqli`），连接信息来自根目录 `config.php`
- PHP 扩展：`mysqli`、`session`、`file`（上传）
- PHPMailer（内置在 `site/email/PHPMailer/`，本地打包，无外部下载）

**依赖本模块的**:
- 前端页面（直接复用 `api/*` 的 JSON 数据）
- 后台页面（复用同一套 `wz.php` 鉴权）

## 规范

### 文件命名
- 接口：`api/<动词/名词>.php`（小写，如 `delcomm.php`）
- 页面：根目录直接放页面（`home.php`、`view.php`）
- JS：`assets/js/<页面>.js`

### 代码模式

**接口统一模板**（写接口开头）:
```php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $arr = [["code" => "201", "msg" => "非法请求"]];
    exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
// 校验 Cookie 登录态后操作数据库
```

**鉴权复用**：
每个受保护接口复制同一段「读 admin 配置 + 查 user 会话 + 校验 passid」代码（`api/wz.php` 可被 include，但多数接口自行粘贴）。修改鉴权逻辑时需同步所有引用点。

### 错误处理
- 接口错误返回 `{"code":"201","msg":"..."}`（`201` 语义为业务失败）。
- 页面侧错误用 JS `alert()` 弹窗 + `location.href` 跳转提示用户。

### 测试
无自动测试。验证方式：`php -l` 语法检查 + 手工回归（安装 → 发布 → 评论/点赞 → 后台审核）。

## 添加新接口

1. 仿照 `api/form.php` 结构创建文件（POST 校验 + Cookie 鉴权 + SQL）。
2. 输入调用 `addslashes(htmlspecialchars())` 过滤。
3. 在 `assets/js/<页面>.js` 添加 AJAX 调用。
4. 若涉及新配置项，按 `configx` 表约定写入。

**检查清单**:
- [ ] 顶部有 POST 请求校验
- [ ] 有登录态/封禁校验
- [ ] 输入已过滤
- [ ] 与 `assets/js` 交互已接通