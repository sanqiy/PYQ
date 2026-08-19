-- 文章模板字段迁移
-- 为 essay 表添加 article_template 字段

ALTER TABLE `essay` ADD COLUMN `article_template` varchar(50) NOT NULL DEFAULT '' COMMENT '文章模板' AFTER `user_top`;
