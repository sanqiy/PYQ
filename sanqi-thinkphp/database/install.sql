-- 朋友圈应用安装脚本
-- 仅包含表结构，无种子数据（由安装程序写入）

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
  `img` varchar(500) NOT NULL DEFAULT '' COMMENT '头像',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '网址',
  `alipay_qr` varchar(500) NOT NULL DEFAULT '' COMMENT '支付宝收款码',
  `wechat_qr` varchar(500) NOT NULL DEFAULT '' COMMENT '微信收款码',
  `homeimg` varchar(500) NOT NULL DEFAULT '' COMMENT '主页背景图(-1则不设置)',
  `sign` varchar(255) NOT NULL DEFAULT '' COMMENT '签名(不设置则为空)',
  `essqx` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否拥有发布权限(0=否 1=是)',
  `esseam` tinyint(1) NOT NULL DEFAULT 1 COMMENT '收到消息是否发送邮件通知(0=否 1=是)',
  `regtime` datetime NULL DEFAULT NULL COMMENT '注册时间',
  `regip` varchar(45) NOT NULL DEFAULT '' COMMENT '注册ip',
  `logtime` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `logip` varchar(45) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `ban` tinyint(1) NOT NULL DEFAULT 0 COMMENT '账号是否被封禁(0=正常 -1=封禁)',
  `bantime` varchar(20) NOT NULL DEFAULT '' COMMENT '账号解封时间(true=永久封禁，否则填写解封日期)',
  `passid` varchar(128) NOT NULL DEFAULT '' COMMENT '账号唯一id标识',
  `role` varchar(20) NOT NULL DEFAULT 'user' COMMENT '角色: user=普通用户 admin=管理员',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_username` (`username`),
  UNIQUE KEY `idx_user_email` (`email`),
  KEY `idx_user_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 文章表
-- ----------------------------
DROP TABLE IF EXISTS `essay`;
CREATE TABLE `essay` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ptpuser` varchar(64) NOT NULL DEFAULT '' COMMENT '发布者账号',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否匿名发布',
  `article_title` varchar(255) NULL DEFAULT NULL COMMENT '长文章标题',
  `article_cover` varchar(500) NULL DEFAULT NULL COMMENT '长文章封面',
  `cover_color` varchar(20) NULL DEFAULT NULL COMMENT '封面色调(hex)',
  `ptptext` text NOT NULL COMMENT '文章内容',
  `ptpimag` text NOT NULL COMMENT '文章图片',
  `ptpvideo` text NOT NULL COMMENT '文章视频',
  `ptpmusic` text NOT NULL COMMENT '文章音乐',
  `ptplx` varchar(20) NOT NULL DEFAULT 'only' COMMENT '文章类型(img=图文 video=视频 music=音乐 article=长文章 only=仅文字)',
  `ptpdw` varchar(255) NOT NULL DEFAULT '' COMMENT '文章发布时定位',
  `tags` varchar(500) NULL DEFAULT NULL COMMENT '文章标签/话题',
  `ptptime` datetime NULL DEFAULT NULL COMMENT '文章发布时间',
  `ptpgg` tinyint(1) NOT NULL DEFAULT 0 COMMENT '文章是否为广告(0=不是 1=是)',
  `ptpggurl` varchar(500) NOT NULL DEFAULT '' COMMENT '广告跳转链接',
  `ptpys` tinyint(1) NOT NULL DEFAULT 1 COMMENT '文章是否可见(0=不可见 1=可见)',
  `commauth` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否允许评论(0=关 1=开)',
  `ptpaud` tinyint NOT NULL DEFAULT 1 COMMENT '审核状态(0=未审核 1=已审核)',
  `ip` varchar(45) NOT NULL DEFAULT '' COMMENT '文章发布时的ip',
  `cid` varchar(64) NOT NULL DEFAULT '' COMMENT '文章cid',
  `user_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户置顶(0=否 1=是)',
  `article_template` varchar(50) NOT NULL DEFAULT '' COMMENT '文章模板',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_essay_cid` (`cid`),
  KEY `idx_essay_ptpuser` (`ptpuser`),
  KEY `idx_essay_ptpaud` (`ptpaud`),
  KEY `idx_essay_ptptime` (`ptptime`),
  KEY `idx_essay_aud_vis_id` (`ptpaud`, `ptpys`, `id`),
  KEY `idx_essay_aud_id` (`ptpaud`, `id`),
  KEY `idx_essay_publish` (`ptpaud`, `ptpys`, `ptptime`, `id`),
  KEY `idx_essay_tags` (`tags`),
  KEY `idx_essay_user_top` (`user_top`, `ptpuser`, `ptpaud`, `ptpys`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 评论表
-- ----------------------------
DROP TABLE IF EXISTS `comm`;
CREATE TABLE `comm` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `couser` varchar(64) NOT NULL DEFAULT '' COMMENT '评论者账号',
  `coname` varchar(100) NOT NULL DEFAULT '' COMMENT '评论者昵称',
  `courl` varchar(500) NOT NULL DEFAULT '' COMMENT '评论者网址',
  `coemail` varchar(255) NOT NULL DEFAULT '' COMMENT '评论者邮箱',
  `coimg` varchar(500) NOT NULL DEFAULT '' COMMENT '评论者头像',
  `cotext` text NOT NULL COMMENT '评论内容',
  `bcouser` varchar(64) NOT NULL DEFAULT '' COMMENT '被评论者账号(没有被评论者则填false)',
  `bconame` varchar(100) NOT NULL DEFAULT '' COMMENT '被评论者昵称(没有被评论者则填false)',
  `comaud` tinyint NOT NULL DEFAULT 1 COMMENT '评论审核状态(0=未审核 1=已审核)',
  `cotime` datetime NULL DEFAULT NULL COMMENT '评论时间',
  `ip` varchar(45) NOT NULL DEFAULT '' COMMENT '评论ip',
  `wzcid` varchar(64) NOT NULL DEFAULT '' COMMENT '评论所属文章',
  `ecid` varchar(64) NOT NULL DEFAULT '' COMMENT '评论cid',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_comm_wzcid` (`wzcid`),
  KEY `idx_comm_ecid` (`ecid`),
  KEY `idx_comm_aud_id` (`comaud`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 评论编辑历史表
-- ----------------------------
DROP TABLE IF EXISTS `comment_edit_history`;
CREATE TABLE `comment_edit_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ecid` varchar(64) NOT NULL DEFAULT '' COMMENT '评论ID',
  `admin_user` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑管理员',
  `old_text` text NOT NULL COMMENT '编辑前内容',
  `new_text` text NOT NULL COMMENT '编辑后内容',
  `created_at` datetime NOT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `idx_comment_edit_ecid` (`ecid`),
  KEY `idx_comment_edit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 点赞表
-- ----------------------------
-- 保留 lname / limg 以兼容当前访客点赞展示逻辑；
-- 对应的移除迁移仅适用于已完成代码改造的分支。
DROP TABLE IF EXISTS `lcke`;
CREATE TABLE `lcke` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `luser` varchar(64) NOT NULL DEFAULT '' COMMENT '点赞者账号',
  `lname` varchar(100) NOT NULL DEFAULT '' COMMENT '点赞者昵称',
  `limg` varchar(500) NOT NULL DEFAULT '' COMMENT '点赞者头像',
  `lwz` varchar(64) NOT NULL DEFAULT '' COMMENT '点赞所属文章',
  `ltime` datetime NULL DEFAULT NULL COMMENT '点赞时间',
  PRIMARY KEY (`id`),
  KEY `idx_lcke_lwz` (`lwz`),
  KEY `idx_lcke_luser` (`luser`),
  UNIQUE KEY `idx_lcke_lwz_luser` (`lwz`, `luser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 文章自动草稿版本表
-- ----------------------------
DROP TABLE IF EXISTS `article_draft_versions`;
CREATE TABLE `article_draft_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '作者账号',
  `draft_key` varchar(128) NOT NULL DEFAULT '' COMMENT '草稿会话标识',
  `article_cid` varchar(64) NOT NULL DEFAULT '' COMMENT '关联文章CID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '快照标题',
  `form_data` mediumtext NOT NULL COMMENT '表单快照JSON',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_article_draft_user_key` (`username`, `draft_key`, `id`),
  KEY `idx_article_draft_cid` (`article_cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 文章附件表
-- ----------------------------
DROP TABLE IF EXISTS `article_attachments`;
CREATE TABLE `article_attachments` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `article_cid` varchar(100) NOT NULL DEFAULT '' COMMENT '关联文章cid',
  `type` varchar(20) NOT NULL DEFAULT 'file' COMMENT '类型: file=文件 link=链接',
  `file_url` varchar(500) NOT NULL DEFAULT '' COMMENT '文件URL',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `file_desc` varchar(500) NOT NULL DEFAULT '' COMMENT '文件描述',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '文件大小(字节)',
  `extract_code` varchar(50) NOT NULL DEFAULT '' COMMENT '提取码',
  `sort_order` int(6) NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `idx_attachment_article_cid` (`article_cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 消息表
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fuser` varchar(64) NOT NULL DEFAULT '' COMMENT '发送者账号',
  `fimg` varchar(500) NOT NULL DEFAULT '' COMMENT '发送者头像',
  `fname` varchar(100) NOT NULL DEFAULT '' COMMENT '发送者昵称',
  `suser` varchar(64) NOT NULL DEFAULT '' COMMENT '接收者账号',
  `type` tinyint NOT NULL DEFAULT 0 COMMENT '通知类型 0=评论 1=点赞 2=审核结果 3=系统公告 4=邮件状态',
  `related_id` varchar(64) NOT NULL DEFAULT '' COMMENT '关联ID(文章cid/评论ecid等)',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息标题',
  `text` text NOT NULL COMMENT '消息内容',
  `ftime` datetime NULL DEFAULT NULL COMMENT '发送时间',
  `msg` tinyint NOT NULL DEFAULT 0 COMMENT '消息状态 0=未读 1=已读 -1=已删除',
  PRIMARY KEY (`id`),
  KEY `idx_message_suser` (`suser`),
  KEY `idx_message_msg` (`msg`),
  KEY `idx_message_suser_msg` (`suser`, `msg`),
  KEY `idx_message_type` (`type`),
  KEY `idx_message_suser_type` (`suser`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 友链表
-- ----------------------------
DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '友链地址',
  `urls` varchar(255) NOT NULL DEFAULT '' COMMENT '友链说明',
  `urlimg` varchar(500) NOT NULL DEFAULT '' COMMENT '友链图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 文件上传记录表（MD5去重 + 引用计数）
-- ----------------------------
DROP TABLE IF EXISTS `file_uploads`;
CREATE TABLE `file_uploads` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `md5` varchar(32) NOT NULL DEFAULT '' COMMENT '文件MD5哈希',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '文件URL路径',
  `type` varchar(10) NOT NULL DEFAULT 'image' COMMENT '文件类型(image/video)',
  `ref_count` int(6) NOT NULL DEFAULT 1 COMMENT '引用计数',
  `created_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_file_uploads_md5` (`md5`),
  KEY `idx_file_uploads_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 扩展配置表（键值存储，站点配置也存于此）
-- ----------------------------
DROP TABLE IF EXISTS `configx`;
CREATE TABLE `configx` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名称',
  `text` text NOT NULL COMMENT '配置信息(JSON编码)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_configx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 投票表
-- ----------------------------
DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `article_cid` varchar(100) NOT NULL DEFAULT '' COMMENT '关联文章cid',
  `question` varchar(255) NOT NULL DEFAULT '' COMMENT '投票问题',
  `options` text NOT NULL COMMENT '选项(JSON数组)',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=单选 2=多选',
  `expire_at` datetime NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_polls_article_cid` (`article_cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 投票记录表
-- ----------------------------
DROP TABLE IF EXISTS `poll_votes`;
CREATE TABLE `poll_votes` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(6) unsigned NOT NULL DEFAULT 0 COMMENT '投票ID',
  `user_id` varchar(128) NOT NULL DEFAULT '' COMMENT '投票用户',
  `option_index` int(6) NOT NULL DEFAULT 0 COMMENT '选项索引',
  `created_at` datetime NULL DEFAULT NULL COMMENT '投票时间',
  PRIMARY KEY (`id`),
  KEY `idx_poll_votes_poll_id` (`poll_id`),
  KEY `idx_poll_votes_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for emoji
-- ----------------------------
DROP TABLE IF EXISTS `emoji`;
CREATE TABLE `emoji` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '表情名称',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '触发码 如 ::(呵呵)',
  `filename` varchar(100) NOT NULL DEFAULT '' COMMENT '图片文件名',
  `category` varchar(50) NOT NULL DEFAULT 'paopao' COMMENT '分类',
  `sort_order` int(6) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用 0禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_emoji_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 默认表情数据
INSERT INTO `emoji` (`name`, `code`, `filename`, `category`, `sort_order`, `status`) VALUES
('呵呵', '::(呵呵)', 'E591B5E591B5_2x.png', 'paopao', 1, 1),
('哈哈', '::(哈哈)', 'E59388E59388_2x.png', 'paopao', 2, 1),
('吐舌', '::(吐舌)', 'E59090E8888C_2x.png', 'paopao', 3, 1),
('太开心', '::(太开心)', 'E5A4AAE5BC80E5BF83_2x.png', 'paopao', 4, 1),
('笑眼', '::(笑眼)', 'E7AC91E79CBC_2x.png', 'paopao', 5, 1),
('花心', '::(花心)', 'E88AB1E5BF83_2x.png', 'paopao', 6, 1),
('小乖', '::(小乖)', 'E5B08FE4B996_2x.png', 'paopao', 7, 1),
('乖', '::(乖)', 'E4B996_2x.png', 'paopao', 8, 1),
('捂嘴笑', '::(捂嘴笑)', 'E68D82E598B4E7AC91_2x.png', 'paopao', 9, 1),
('滑稽', '::(滑稽)', 'E6BB91E7A8BD_2x.png', 'paopao', 10, 1),
('你懂的', '::(你懂的)', 'E4BDA0E68782E79A84_2x.png', 'paopao', 11, 1),
('不高兴', '::(不高兴)', 'E4B88DE9AB98E585B4_2x.png', 'paopao', 12, 1),
('怒', '::(怒)', 'E68092_2x.png', 'paopao', 13, 1),
('汗', '::(汗)', 'E6B197_2x.png', 'paopao', 14, 1),
('黑线', '::(黑线)', 'E9BB91E7BABF_2x.png', 'paopao', 15, 1),
('泪', '::(泪)', 'E6B3AA_2x.png', 'paopao', 16, 1),
('真棒', '::(真棒)', 'E79C9FE6A392_2x.png', 'paopao', 17, 1),
('喷', '::(喷)', 'E596B7_2x.png', 'paopao', 18, 1),
('惊哭', '::(惊哭)', 'E6838AE593AD_2x.png', 'paopao', 19, 1),
('阴险', '::(阴险)', 'E998B4E999A9_2x.png', 'paopao', 20, 1),
('鄙视', '::(鄙视)', 'E98499E8A786_2x.png', 'paopao', 21, 1),
('酷', '::(酷)', 'E985B7_2x.png', 'paopao', 22, 1),
('啊', '::(啊)', 'E5958A_2x.png', 'paopao', 23, 1),
('狂汗', '::(狂汗)', 'E78B82E6B197_2x.png', 'paopao', 24, 1),
('what', '::(what)', 'what_2x.png', 'paopao', 25, 1),
('疑问', '::(疑问)', 'E79691E997AE_2x.png', 'paopao', 26, 1),
('酸爽', '::(酸爽)', 'E985B8E788BD_2x.png', 'paopao', 27, 1),
('呀咩蹀', '::(呀咩蹀)', 'E59180E592A9E788B9_2x.png', 'paopao', 28, 1),
('委屈', '::(委屈)', 'E5A794E5B188_2x.png', 'paopao', 29, 1),
('惊讶', '::(惊讶)', 'E6838AE8AEB6_2x.png', 'paopao', 30, 1),
('睡觉', '::(睡觉)', 'E79DA1E8A789_2x.png', 'paopao', 31, 1),
('笑尿', '::(笑尿)', 'E7AC91E5B0BF_2x.png', 'paopao', 32, 1),
('挖鼻', '::(挖鼻)', 'E68C96E9BCBB_2x.png', 'paopao', 33, 1),
('吐', '::(吐)', 'E59090_2x.png', 'paopao', 34, 1),
('犀利', '::(犀利)', 'E78A80E588A9_2x.png', 'paopao', 35, 1),
('小红脸', '::(小红脸)', 'E5B08FE7BAA2E884B8_2x.png', 'paopao', 36, 1),
('懒得理', '::(懒得理)', 'E68792E5BE97E79086_2x.png', 'paopao', 37, 1),
('勉强', '::(勉强)', 'E58B89E5BCBA_2x.png', 'paopao', 38, 1),
('爱心', '::(爱心)', 'E788B1E5BF83_2x.png', 'paopao', 39, 1),
('心碎', '::(心碎)', 'E5BF83E7A28E_2x.png', 'paopao', 40, 1),
('玫瑰', '::(玫瑰)', 'E78EABE791B0_2x.png', 'paopao', 41, 1),
('礼物', '::(礼物)', 'E7A4BCE789A9_2x.png', 'paopao', 42, 1),
('彩虹', '::(彩虹)', 'E5BDA9E899B9_2x.png', 'paopao', 43, 1),
('太阳', '::(太阳)', 'E5A4AAE998B3_2x.png', 'paopao', 44, 1),
('星星月亮', '::(星星月亮)', 'E6989FE6989FE69C88E4BAAE_2x.png', 'paopao', 45, 1),
('钱币', '::(钱币)', 'E992B1E5B881_2x.png', 'paopao', 46, 1),
('茶杯', '::(茶杯)', 'E88CB6E69DAF_2x.png', 'paopao', 47, 1),
('蛋糕', '::(蛋糕)', 'E89B8BE7B395_2x.png', 'paopao', 48, 1),
('大拇指', '::(大拇指)', 'E5A4A7E68B87E68C87_2x.png', 'paopao', 49, 1),
('胜利', '::(胜利)', 'E8839CE588A9_2x.png', 'paopao', 50, 1),
('haha', '::(haha)', 'haha_2x.png', 'paopao', 51, 1),
('OK', '::(OK)', 'OK_2x.png', 'paopao', 52, 1),
('沙发', '::(沙发)', 'E6B299E58F91_2x.png', 'paopao', 53, 1),
('手纸', '::手纸', 'E6898BE7BAB8_2x.png', 'paopao', 54, 1),
('香蕉', '::(香蕉)', 'E9A699E89589_2x.png', 'paopao', 55, 1),
('便便', '::(便便)', 'E4BEBFE4BEBF_2x.png', 'paopao', 56, 1),
('药丸', '::(药丸)', 'E88DAFE4B8B8_2x.png', 'paopao', 57, 1),
('红领巾', '::(红领巾)', 'E7BAA2E9A286E5B7BE_2x.png', 'paopao', 58, 1),
('蜡烛', '::(蜡烛)', 'E89CA1E7839B_2x.png', 'paopao', 59, 1),
('音乐', '::(音乐)', 'E99FB3E4B990_2x.png', 'paopao', 60, 1),
('灯泡', '::(灯泡)', 'E781AFE6B3A1_2x.png', 'paopao', 61, 1);

SET FOREIGN_KEY_CHECKS = 1;
