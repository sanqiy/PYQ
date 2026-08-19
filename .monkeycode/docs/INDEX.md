# sanqi 朋友圈系统文档

sanqi 是 PHP 编写的单用户「朋友圈」风格社交系统，提供文字 / 图文 / 视频 / 音乐 / 长文章发布、评论、点赞、留言、消息通知、友链等功能，附带完整管理后台。仓库包含两个可运行的实现：`sanqi/`（PHP 原生版，零依赖开箱即用）与 `sanqi-thinkphp/`（ThinkPHP 8 重构增强版，功能最全）。

**快速链接**: [架构](./ARCHITECTURE.md) | [接口](./INTERFACES.md) | [开发者指南](./DEVELOPER_GUIDE.md)

---

## 核心文档

### [架构](./ARCHITECTURE.md)
系统设计、两个版本的技术栈、目录结构与数据流。从这里开始了解系统如何运作。

### [接口](./INTERFACES.md)
原生版全部 `api/*.php` 端点与 ThinkPHP 版路由、认证方式、响应格式。集成或调试的参考。

### [开发者指南](./DEVELOPER_GUIDE.md)
环境搭建、安装部署、开发工作流、编码规范与常见任务。贡献者必读。

---

## 模块

| 模块 | 描述 | README |
|------|------|--------|
| `sanqi/` | PHP 原生版，无框架 | [README](../模块/sanqi原生版.md) |
| `sanqi-thinkphp/` | ThinkPHP 8 增强版 | [README](../模块/sanqi-thinkphp.md) |

---

## 核心概念

理解这些领域概念有助于导航代码库：

| 概念 | 描述 |
|------|------|
| [用户与认证](./专有概念/用户与认证.md) | 用户注册/登录、Cookie 会话、角色、封禁与权限 |
| [文章（essay）](./专有概念/文章.md) | 朋友圈内容的主体，支持图文/视频/音乐/长文章与审核 |
| [评论（comm）](./专有概念/评论.md) | 文章下的评论与回复，支持审核与匿名 |
| [点赞（lcke）](./专有概念/点赞.md) | 文章点赞记录，表级唯一约束防重复 |
| [配置（configx/admin）](./专有概念/配置.md) | 站点配置的存储与读取机制 |

---

## 快速参考

### 目录

```bash
# 原生版：部署根目录到 Web 服务器后访问 /install/
# ThinkPHP 版：需求 PHP 8.0+，public/ 为入口
```

### 重要文件

| 文件 | 目的 |
|------|------|
| `sanqi/index.php` | 原生版首页与站点入口 |
| `sanqi/config.php` | 原生版数据库连接（安装时生成） |
| `sanqi-thinkphp/route/app.php` | ThinkPHP 版全部路由定义 |
| `sanqi-thinkphp/database/install.sql` | ThinkPHP 版完整建表 SQL |
| `sanqi-thinkphp/.example.env` | ThinkPHP 版环境变量模板 |