# sanqi-thinkphp（ThinkPHP 8 增强版）

基于 ThinkPHP 8 的现代 MVC 重构朋友圈系统，功能为原生版超集，代码规模最大（约 600 个 PHP 文件）。

## 结构

```
sanqi-thinkphp/
├── public/ # Web 入口（index.php + 前端编译资源 + upload + user）
├── route/app.php # 全部路由（前台/API/后台/server）
├── app/
│   ├── controller/ # 前台9 + admin后台21 + api接口12 + server2
│   ├── service/ # 业务服务层（22 个）
│   ├── model/ # ORM 模型（16 个，对应数据库表）
│   ├── middleware/ # 7 个中间件（认证/CSRF/安装检查/异常日志）
│   ├── helper/ # 全局辅助函数（auth/security/markdown/emoji/format...）
│   ├── validate/ # 表单验证器（Article/Auth/Comment/User）
│   ├── traits/ # AuthTrait / SiteConfigTrait / ArticleHelperTrait
│   ├── command/ # 命令行任务（CacheClear/DbBackup/Migrate/OrphanUpload/RateLimitClear）
│   └── view/ # 模板视图（含 layout/component）
├── config/ # 框架配置（app/database/cache/filesystem/mail/ratelimit...）
├── database/ # install.sql + 13 个迁移脚本
├── extend/ # 第三方扩展（psr-0）
├── runtime/ # 运行缓存与日志
└── vendor/ # Composer 依赖
```

## 关键文件

| 文件 | 目的 |
|------|------|
| `public/index.php` | 框架统一入口 |
| `route/app.php` | 全部路由定义（唯一路由表，见 `INTERFACES.md`） |
| `app/controller/api/*` | 前端接口控制器（12 个） |
| `app/middleware/*` | 认证/CSRF/安装检查等横切逻辑 |
| `app/service/EmailTemplateService.php` | 邮件 HTML 外壳装饰 + 模板渲染 |
| `app/service/SiteConfigService.php` | 站点配置统一读取与缓存 |
| `database/install.sql` | 全新安装建表 SQL（14 表） |
| `.example.env` | 环境变量模板（数据库连接） |

## 依赖

**本模块依赖**:
- `topthink/framework`、`think-orm`、`think-view`、`think-filesystem`
- `phpmailer/phpmailer`（邮件）
- MySQL；Redis 可选（`RedisService`）

**依赖本模块的**:
- 无（独立部署模块）

## 规范

### 目录约定（ThinkPHP 标准）
- 控制器：`app/controller/{前台|admin|api|server}/`，类名 PascalCase
- 服务：`app/service/*Service.php`（业务逻辑），控制器保持薄
- 模型：`app/model/*.php`（表名 PascalCase，如 `Configx`）
- 中间件：`app/middleware/*`，在 `route/app.php` 引入

### 代码模式
**服务层分层**：
```php
// 控制器只做参数透传与响应，业务放服务
class Article extends Base
{
    public function save()
    {
        return $this->service(ArticleService::class)->save(...);
    }
}
```
**配置读取**：通过 `SiteConfigTrait` / `SiteConfigService` 获取，不直接 SQL。

### 错误处理
- 控制器统一 `Json::error($msg)` / `Json::success($data)` 返回（见 `app/controller` 具体实现）。
- 异常由 `ExceptionLog` 中间件记录到 `runtime/log/`。
- 后台操作写入 `AdminLogService` 操作日志。

### 测试
无测试框架；提供命令行工具验证：`php think migrate`（迁移）、`php think cache:clear`、`php think dbbak`（备份）。语法/逻辑建议 `php -l` 校验 + 手工回归。

## 添加新路由的流程

1. 在 `app/controller/api/`（或 admin/）新增控制器方法。
2. 在 `route/app.php` 对应组注册路由（注意后台组已挂 `admin_auth` 中间件）。
3. 业务逻辑下沉到 `app/service/`，数据访问走 `app/model/`。
4. 输入校验使用 `app/validate/` 或 `$request->validate()`。
5. 返回统一 JSON / 渲染 `app/view/` 模板。

**检查清单**:
- [ ] 路由已注册且访问方法正确
- [ ] 有中间件/鉴权保护（前台页面 ≠ API）
- [ ] 使用 ORM 参数绑定，无 SQL 拼接
- [ ] 输入已通过 `cleanXss` / `safeFilter`
- [ ] 更新 `route/app.php` 注释与本文档接口清单