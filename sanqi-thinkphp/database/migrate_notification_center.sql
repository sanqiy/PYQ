-- 通知中心迁移
-- 为 message 表添加 type 和 related_id 字段，支持统一通知分类

ALTER TABLE `message`
  ADD COLUMN `type` tinyint NOT NULL DEFAULT 0 COMMENT '通知类型 0=评论 1=点赞 2=审核结果 3=系统公告 4=邮件状态' AFTER `suser`,
  ADD COLUMN `related_id` varchar(64) NOT NULL DEFAULT '' COMMENT '关联ID(文章cid/评论ecid等)' AFTER `type`;

-- 回填现有数据的 type 字段
UPDATE `message` SET `type` = 1 WHERE `title` = '赞了你的文章';
UPDATE `message` SET `type` = 0 WHERE `type` = 0 AND `title` <> '';

-- 添加索引
ALTER TABLE `message`
  ADD KEY `idx_message_type` (`type`),
  ADD KEY `idx_message_suser_type` (`suser`, `type`);
